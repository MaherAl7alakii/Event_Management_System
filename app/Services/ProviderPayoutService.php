<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\ProviderPayoutReason;
use App\Enums\ProviderPayoutStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ProviderPayout;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;


class ProviderPayoutService
{

    private const RELEASE_GRACE_HOURS = 24;

    public function __construct(
        private readonly StripeClient $stripe,
        private readonly LedgerService $ledger,
    ) {
    }


    public function schedule(
        Booking $booking,
        float $amount,
        ProviderPayoutReason $reason,
        ?Payment $payment = null,
    ): ProviderPayout {
        if ($amount <= 0) {
            throw new Exception("Payout amount must be greater than zero for booking #{$booking->id}.");
        }


        $available = round(
            $this->ledger->netPaidByCustomer($booking) - $this->ledger->totalAllocatedToProvider($booking),
            2
        );

//        if (round($amount, 2) > $available + 0.01) {
//            throw new Exception(
//                "Payout amount ({$amount}) exceeds the amount available to allocate for booking #{$booking->id} (available: {$available})."
//            );
//        }

        $isAlreadyCompleted = $booking->status === BookingStatus::COMPLETED && $booking->completed_at !== null;

        $payout = ProviderPayout::create([
            'booking_id'  => $booking->id,
            'provider_id' => $booking->provider_id,
            'payment_id'  => $payment?->id,
            'amount'      => round($amount, 2),
            'reason'      => $reason->value,
            'status'      => $isAlreadyCompleted
                ? ProviderPayoutStatus::AWAITING_RELEASE->value
                : ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value,
            'release_at'  => $isAlreadyCompleted
                ? $booking->completed_at->copy()->addHours(self::RELEASE_GRACE_HOURS)
                : null,
        ]);

        $this->ledger->recordPayout($booking, $payout);

        return $payout;
    }



    public function cancelScheduledPayouts(Booking $booking): void
    {
        ProviderPayout::where('booking_id', $booking->id)
            ->where('status', ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value)
            ->update(['status' => ProviderPayoutStatus::CANCELLED->value]);
    }



    public function reconcileForBooking(
        Booking $booking,
        float $targetAmount,
        ProviderPayoutReason $reason,
        ?Payment $payment = null,
    ): ?ProviderPayout {
        $this->cancelScheduledPayouts($booking);

        if ($targetAmount <= 0) {
            return null;
        }

        return $this->schedule($booking, $targetAmount, $reason, $payment);
    }


    public function scheduleReleaseForCompletedBooking(Booking $booking): void
    {
        $releaseAt = now()->addHours(self::RELEASE_GRACE_HOURS);

        ProviderPayout::where('booking_id', $booking->id)
            ->where('status', ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value)
            ->update([
                'status'     => ProviderPayoutStatus::AWAITING_RELEASE->value,
                'release_at' => $releaseAt,
            ]);
    }



    public function releaseScheduledPayoutsImmediately(Booking $booking): void
    {
        ProviderPayout::where('booking_id', $booking->id)
            ->where('status', ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value)
            ->update([
                'status'     => ProviderPayoutStatus::AWAITING_RELEASE->value,
                'release_at' => now()->addHours(self::RELEASE_GRACE_HOURS),
            ]);
    }


    public function holdForComplaint(Booking $booking, string $reason): void
    {
        ProviderPayout::where('booking_id', $booking->id)
            ->whereIn('status', [
                ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value,
                ProviderPayoutStatus::AWAITING_RELEASE->value,
            ])
            ->update([
                'status'      => ProviderPayoutStatus::ON_HOLD->value,
                'hold_reason' => $reason,
            ]);
    }

    public function releaseHeldPayouts(Booking $booking): void
    {
        $held = ProviderPayout::where('booking_id', $booking->id)
            ->where('status', ProviderPayoutStatus::ON_HOLD->value)
            ->get();

        foreach ($held as $payout) {
            $this->releasePayout($payout);
        }
    }


    public function cancelHeldPayouts(Booking $booking): void
    {
        ProviderPayout::where('booking_id', $booking->id)
            ->where('status', ProviderPayoutStatus::ON_HOLD->value)
            ->update(['status' => ProviderPayoutStatus::CANCELLED->value]);
    }

    public function releasePayout(ProviderPayout $payout): void
    {
        DB::transaction(function () use ($payout) {
            $locked = ProviderPayout::lockForUpdate()->findOrFail($payout->id);

            if ($locked->status !== ProviderPayoutStatus::AWAITING_RELEASE) {
                return;
            }

            $provider = $locked->provider?->serviceProvider;


            if (! $provider || ! $provider->hasCompletedStripeOnboarding()) {
                $locked->update([
                    'status'         => ProviderPayoutStatus::FAILED->value,
                    'failure_reason' => 'Provider has not completed Stripe Connect onboarding.',
                ]);

                Log::error('Payout release failed: provider not onboarded', ['payout_id' => $locked->id]);

                return;
            }

            if (! $locked->payment || ! $locked->payment->stripe_charge_id) {
                $locked->update([
                    'status'         => ProviderPayoutStatus::FAILED->value,
                    'failure_reason' => 'No underlying payment/charge found to fund this payout.',
                ]);

                Log::error('Payout release failed: missing underlying payment', ['payout_id' => $locked->id]);

                return;
            }

            try {
                $charge = $this->stripe->charges->retrieve(
                    $locked->payment->stripe_charge_id,
                    ['expand' => ['balance_transaction']]
                );

                $balanceTransaction = $charge->balance_transaction;
                $currency = $balanceTransaction->currency;

                $amountInCents = $balanceTransaction->net;

                $stripeTransfer = $this->stripe->transfers->create([
                    'amount'             => $amountInCents,
                    'currency'           => $currency,
                    'destination'        => $provider->stripe_account_id,
                    'metadata'           => [
                        'provider_payout_id' => $locked->id,
                        'booking_id'         => $locked->booking_id,
                        'provider_id'        => $locked->provider_id,
                        'reason'             => $locked->reason->value,
                    ],
                ]);

                $locked->update([
                    'status'             => ProviderPayoutStatus::RELEASED->value,
                    'stripe_transfer_id' => $stripeTransfer->id,
                ]);

                Log::info('Provider payout released', [
                    'payout_id'   => $locked->id,
                    'provider_id' => $locked->provider_id,
                    'amount'      => $locked->amount,
                ]);
            } catch (ApiErrorException $e) {
                $locked->update([
                    'status'         => ProviderPayoutStatus::FAILED->value,
                    'failure_reason' => $e->getMessage(),
                ]);

                Log::error('Stripe transfer failed for payout release', [
                    'payout_id' => $locked->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        });
    }
}
