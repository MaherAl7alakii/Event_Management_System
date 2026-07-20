<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
//use App\Enums\TransferStatus;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
//use App\Models\PaymentTransfer;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;


class PaymentCompletionService
{
    public function __construct(
//        private readonly StripeClient $stripe,
        private readonly EventSubmissionService $submissionService,
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


            $this->applyBookingAndEventSideEffects($lockedPayment);

            return $lockedPayment->fresh();
        });
    }

    private function applyBookingAndEventSideEffects(Payment $payment): void
    {
        $event = Event::lockForUpdate()->findOrFail($payment->event_id);

        match ($payment->payment_type) {

            PaymentType::SUBMISSION_FEE  => $this->submissionService->completeFirstSubmissionAfterPayment($event),
            PaymentType::DEPOSIT => $this->handleDepositSideEffects($event),
            PaymentType::ADD_ON_PAYMENT  => $this->handleAddOnSideEffects($payment),
            PaymentType::FINAL_BALANCE   => $this->handleFinalBalanceSideEffects($event),
        };
    }

    private function handleDepositSideEffects(Event $event): void
    {
        Booking::where('event_id', $event->id)
            ->where('status', BookingStatus::ACCEPTED->value)
            ->update(['status' => BookingStatus::DEPOSIT_PAID->value]);

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

        Booking::where('id', $payment->booking_id)
            ->where('status', BookingStatus::ACCEPTED->value)
            ->update(['status' => BookingStatus::DEPOSIT_PAID->value]);


    }


    private function handleFinalBalanceSideEffects(Event $event): void
    {
        Booking::where('event_id', $event->id)
            ->whereIn('status', [
                BookingStatus::ACCEPTED->value,
                BookingStatus::DEPOSIT_PAID->value,
            ])
            ->update([
                'status'       => BookingStatus::CONFIRMED->value,
                'confirmed_at' => now(),
            ]);

        $event->update(['status' => EventStatus::CONFIRMED->value]);
    }

}
