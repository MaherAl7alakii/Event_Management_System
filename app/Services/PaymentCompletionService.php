<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
use App\Enums\LedgerEntryType;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ProviderPayoutReason;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PaymentCompletionService
{
    public function __construct(
        private readonly EventSubmissionService $submissionService,
        private readonly BookingDeadlineCalculator $deadlines,
        private readonly ProviderPayoutService $payouts,
        private readonly LedgerService $ledger,
    ) {
    }


    public function handleSucceededPayment(string $paymentIntentId, string $chargeId): Payment
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if (! $payment) {
            throw new Exception("No local payment record found for intent {$paymentIntentId}.");
        }


        if ($payment->status === PaymentStatus::SUCCEEDED) {
            Log::info('Payment already processed, skipping duplicate webhook', [
                'payment_id' => $payment->id,
            ]);

            return $payment;
        }

        return DB::transaction(function () use ($payment, $chargeId) {
            $lockedPayment = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($lockedPayment->status === PaymentStatus::SUCCEEDED) {
                return $lockedPayment;
            }

            $lockedPayment->update([
                'status'           => PaymentStatus::SUCCEEDED->value,
                'stripe_charge_id' => $chargeId,
            ]);


            $shares = $this->resolveBookingShares($lockedPayment);

            $this->recordLedgerCharges($lockedPayment, $shares);

            $this->schedulePayoutsForPayment($lockedPayment, $shares);

            $this->applyBookingAndEventSideEffects($lockedPayment, $shares);

            return $lockedPayment->fresh();
        });
    }


    private function resolveBookingShares(Payment $payment): array
    {
        if (in_array($payment->payment_type, PaymentType::platformOnlyTypes(), true)) {
            return [];
        }

        if ($payment->payment_type === PaymentType::ADD_ON_PAYMENT) {
            if (! $payment->booking_id) {
                return [];
            }

            $booking = Booking::find($payment->booking_id);

            if (! $booking) {
                return [];
            }


            return [['booking' => $booking, 'amount' => round((float) $payment->amount, 2)]];
        }

        $bookings = match ($payment->payment_type) {
            PaymentType::DEPOSIT => Booking::where('event_id', $payment->event_id)
                ->where('status', BookingStatus::ACCEPTED->value)
                ->get(),

            PaymentType::FINAL_BALANCE => Booking::where('event_id', $payment->event_id)
                ->whereIn('status', [
                    BookingStatus::ACCEPTED->value,
                    BookingStatus::DEPOSIT_PAID->value,
                ])
                ->get(),

            default => throw new Exception(
                "Payment type {$payment->payment_type->value} is not a per-booking chargeable type."
            ),
        };

        $shares = $bookings->map(fn (Booking $booking) => [
            'booking' => $booking,
            'amount'  => $payment->payment_type === PaymentType::DEPOSIT
                ? $booking->depositAmount()
                : $booking->remainingBalance(),
        ])->all();

        $sum = round(array_sum(array_column($shares, 'amount')), 2);
        $paymentAmount = round((float) $payment->amount, 2);

        if (abs($paymentAmount - $sum) > 0.01) {
            Log::error('Payment amount does not match current sum of booking shares — refusing to distribute silently', [
                'payment_id'     => $payment->id,
                'payment_type'   => $payment->payment_type->value,
                'payment_amount' => $paymentAmount,
                'computed_sum'   => $sum,
                'booking_ids'    => array_map(fn ($s) => $s['booking']->id, $shares),
            ]);

            throw new Exception(
                "Payment #{$payment->id} amount ({$paymentAmount}) does not match the sum of current booking shares ({$sum}). "
                . 'The set of eligible bookings likely changed after this payment was created. Manual reconciliation required.'
            );
        }

        return $shares;
    }

    private function chargeTypeFor(PaymentType $type): LedgerEntryType
    {
        return match ($type) {
            PaymentType::DEPOSIT       => LedgerEntryType::DEPOSIT_CHARGE,
            PaymentType::FINAL_BALANCE => LedgerEntryType::FINAL_BALANCE_CHARGE,
            PaymentType::ADD_ON_PAYMENT => LedgerEntryType::ADD_ON_CHARGE,
            default => throw new Exception("Payment type {$type->value} has no corresponding ledger charge type."),
        };
    }


    private function recordLedgerCharges(Payment $payment, array $shares): void
    {
        if (empty($shares)) {
            return;
        }

        $type = $this->chargeTypeFor($payment->payment_type);

        foreach ($shares as $share) {
            if ($share['amount'] <= 0) {
                continue;
            }

            $this->ledger->recordCharge($share['booking'], $payment, $share['amount'], $type);
        }
    }


    private function applyBookingAndEventSideEffects(Payment $payment, array $shares): void
    {
        $event = Event::lockForUpdate()->findOrFail($payment->event_id);

        match ($payment->payment_type) {
            PaymentType::SUBMISSION_FEE => $this->submissionService->completeFirstSubmissionAfterPayment($event),
            PaymentType::DEPOSIT        => $this->handleDepositSideEffects($event, $shares),
            PaymentType::ADD_ON_PAYMENT => $this->handleAddOnSideEffects($payment),
            PaymentType::FINAL_BALANCE  => $this->handleFinalBalanceSideEffects($event, $shares),
        };
    }



    private function handleDepositSideEffects(Event $event, array $shares): void
    {

        $bookingIds = array_map(fn ($s) => $s['booking']->id, $shares);

        if (! empty($bookingIds)) {
            Booking::whereIn('id', $bookingIds)
                ->where('status', BookingStatus::ACCEPTED->value)
                ->get()
                ->each(function (Booking $booking) {
                    $booking->update([
                        'status'                    => BookingStatus::DEPOSIT_PAID->value,
                        'deposit_deadline_at'       => null,
                        'final_payment_deadline_at' => $this->deadlines->finalPaymentDeadline($booking),
                    ]);
                });
        }

        $event->update([
            'status'       => EventStatus::DEPOSIT_PAID->value,
            'confirmed_at' => $event->confirmed_at ?? now(),
        ]);
    }



    private function handleAddOnSideEffects(Payment $payment): void
    {
        if ($payment->booking_id === null) {
            return;
        }

        $booking = Booking::where('id', $payment->booking_id)
            ->where('status', BookingStatus::ACCEPTED->value)
            ->first();

        if (! $booking) {
            return;
        }

        $isFullyPaid = $booking->remainingBalance() <= 0;

        $booking->update([
            'status'                    => $isFullyPaid ? BookingStatus::CONFIRMED->value : BookingStatus::DEPOSIT_PAID->value,
            'confirmed_at'              => $isFullyPaid ? now() : $booking->confirmed_at,
            'final_payment_deadline_at' => $isFullyPaid ? null : $this->deadlines->finalPaymentDeadline($booking),
        ]);
    }



    private function handleFinalBalanceSideEffects(Event $event, array $shares): void
    {
        $bookingIds = array_map(fn ($s) => $s['booking']->id, $shares);

        if (! empty($bookingIds)) {
            Booking::whereIn('id', $bookingIds)
                ->whereIn('status', [
                    BookingStatus::ACCEPTED->value,
                    BookingStatus::DEPOSIT_PAID->value,
                ])
                ->update([
                    'status'                    => BookingStatus::CONFIRMED->value,
                    'confirmed_at'              => now(),
                    'deposit_deadline_at'       => null,
                    'final_payment_deadline_at' => null,
                ]);
        }

        $event->update(['status' => EventStatus::CONFIRMED->value]);
    }



    private function schedulePayoutsForPayment(Payment $payment, array $shares): void
    {
        if (empty($shares)) {
            return;
        }

        $reason = match ($payment->payment_type) {
            PaymentType::DEPOSIT        => ProviderPayoutReason::DEPOSIT,
            PaymentType::FINAL_BALANCE  => ProviderPayoutReason::FINAL_BALANCE,
            PaymentType::ADD_ON_PAYMENT => ProviderPayoutReason::ADD_ON,
            default => throw new Exception(
                "Payment type {$payment->payment_type->value} has no provider payout reason."
            ),
        };

        foreach ($shares as $share) {
            if ($share['amount'] <= 0) {
                continue;
            }

            $this->payouts->schedule(
                booking: $share['booking'],
                amount: $share['amount'],
                reason: $reason,
                payment: $payment,
            );
        }
    }
}
