<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ProviderPayoutReason;
use App\Enums\ProviderPayoutStatus;
use App\Models\Booking;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BookingCancellationService
{
    private const NO_REFUND_THRESHOLD_HOURS = 72;

    public function __construct(
        private readonly EventStatusResolver $statusResolver,
        private readonly ProviderPayoutService $payouts,
        private readonly RefundService $refunds,
    ) {
    }


    public function cancel(Booking $booking, CancelledBy $cancelledBy, ?string $reason = null): Booking
    {
        $this->assertCancellable($booking);

        return DB::transaction(function () use ($booking, $cancelledBy, $reason) {
            $locked = Booking::lockForUpdate()->findOrFail($booking->id);

            $this->assertCancellable($locked);

            match ($cancelledBy) {
                CancelledBy::PROVIDER => $this->handleProviderCancellation($locked),
                CancelledBy::CUSTOMER => $this->handleCustomerCancellation($locked),
            };

            $locked->update([
                'status'       => BookingStatus::CANCELLED->value,
                'cancelled_at' => now(),
            ]);

            $this->statusResolver->resolveAndPersist($locked->event);

            Log::info('Booking cancelled', [
                'booking_id'   => $locked->id,
                'cancelled_by' => $cancelledBy->value,
                'reason'       => $reason,
            ]);

            //---- Notification ----

            return $locked->fresh();
        });
    }

    private function assertCancellable(Booking $booking): void
    {
        $nonCancellable = [
            BookingStatus::COMPLETED,
            BookingStatus::CANCELLED,
            BookingStatus::REJECTED,
            BookingStatus::EXPIRED,
        ];

        if (in_array($booking->status, $nonCancellable, true)) {
            throw new Exception(
                "Booking #{$booking->id} cannot be cancelled from its current status ({$booking->status->value})."
            );
        }
    }



    private function handleProviderCancellation(Booking $booking): void
    {
        $succeededPayments = $this->succeededPaymentsFor($booking);

        foreach ($succeededPayments as $payment) {
            $amount = $this->amountAttributableToBooking($payment, $booking);

            if ($amount > 0) {
                $this->refunds->refund($payment, $amount, 'provider_cancelled_booking');
            }
        }
    }


    private function handleCustomerCancellation(Booking $booking): void
    {
        match ($booking->status) {

            BookingStatus::ACCEPTED => null,


            BookingStatus::DEPOSIT_PAID => $this->scheduleImmediatePayout(
                $booking,
                $booking->depositAmountActuallyPaid(),
                ProviderPayoutReason::CANCELLATION_DEPOSIT_SHARE,
            ),


            BookingStatus::CONFIRMED => $this->handleCustomerCancellationAfterFullPayment($booking),

            default => throw new Exception(
                "Unexpected booking status ({$booking->status->value}) during customer cancellation."
            ),
        };
    }


    private function handleCustomerCancellationAfterFullPayment(Booking $booking): void
    {
        $hoursUntilBooking = now()->diffInHours($booking->startsAt(), false);

        if ($hoursUntilBooking <= self::NO_REFUND_THRESHOLD_HOURS) {

            $this->scheduleImmediatePayout(
                $booking,
                $booking->totalValue(),
                ProviderPayoutReason::CANCELLATION_FULL_AMOUNT,
            );

            return;
        }


        $depositShare = $booking->depositAmountActuallyPaid();
        $remainingShare = max(round($booking->totalValue() - $depositShare, 2), 0);

        $this->scheduleImmediatePayout($booking, $depositShare, ProviderPayoutReason::CANCELLATION_DEPOSIT_SHARE);

        if ($remainingShare > 0) {
            $this->refundRemainingShare($booking, $remainingShare);
        }
    }


    private function scheduleImmediatePayout(Booking $booking, float $amount, ProviderPayoutReason $reason): void
    {
        if ($amount <= 0) {
            return;
        }

        $payment = $this->latestSucceededPaymentFor($booking);

        $payout = $this->payouts->schedule($booking, $amount, $reason, $payment);

        $payout->update([
            'status'     => ProviderPayoutStatus::AWAITING_RELEASE->value,
            'release_at' => now()->addHours(24),
        ]);
    }

    private function refundRemainingShare(Booking $booking, float $amount): void
    {
        $payment = $this->latestSucceededPaymentFor($booking, preferType: PaymentType::FINAL_BALANCE);


        if (! $payment) {
            Log::error('No payment found to refund remaining share from', ['booking_id' => $booking->id]);
            return;
        }

        $this->refunds->refund($payment, $amount, 'customer_cancelled_booking_partial_refund');
    }


    private function succeededPaymentsFor(Booking $booking): Collection
    {
        return Payment::where('event_id', $booking->event_id)
            ->where('status', PaymentStatus::SUCCEEDED->value)
            ->whereIn('payment_type', [
                PaymentType::DEPOSIT->value,
                PaymentType::FINAL_BALANCE->value,
                PaymentType::ADD_ON_PAYMENT->value,
            ])
            ->get()
            ->filter(fn (Payment $payment) => $payment->payment_type !== PaymentType::ADD_ON_PAYMENT
                || $payment->booking_id === $booking->id);
    }

    private function latestSucceededPaymentFor(Booking $booking, ?PaymentType $preferType = null): ?Payment
    {
        $payments = $this->succeededPaymentsFor($booking);

        if ($preferType) {
            $preferred = $payments->firstWhere('payment_type', $preferType);

            if ($preferred) {
                return $preferred;
            }
        }

        return $payments->sortByDesc('created_at')->first();
    }

    private function amountAttributableToBooking(Payment $payment, Booking $booking): float
    {
        if ($payment->payment_type === PaymentType::ADD_ON_PAYMENT) {
            return $payment->booking_id === $booking->id ? (float) $payment->amount : 0.0;
        }

        return match (true) {
            $payment->payment_type === PaymentType::DEPOSIT
                && $booking->status === BookingStatus::DEPOSIT_PAID => $booking->depositAmountActuallyPaid(),

            $payment->payment_type === PaymentType::FINAL_BALANCE
                && $booking->status === BookingStatus::CONFIRMED => $booking->totalValue() - $booking->depositAmountActuallyPaid(),

            default => 0.0,
        };
    }
}
