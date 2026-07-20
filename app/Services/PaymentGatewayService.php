<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Exceptions\PaymentException;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

class PaymentGatewayService
{

    private const SUBMISSION_FEE_AMOUNT = 5.00;

    public function __construct(
        private readonly StripeClient $stripe,
        private readonly BookingEligibilityService $eligibility,
    ) {
    }


    public function createSubmissionFeeIntent(Event $event): array
    {
        $currentStatus = $event->status instanceof EventStatus
            ? $event->status
            : EventStatus::from($event->status);

        if ($currentStatus !== EventStatus::DRAFT) {
            throw new PaymentException(
                "Event #{$event->id} is not in draft status; submission fee is only charged once."
            );
        }

        return $this->createIntent(
            event: $event,
            totalAmount: self::SUBMISSION_FEE_AMOUNT,
            paymentType: PaymentType::SUBMISSION_FEE,
            bookingIdForRecord: null,
        );
    }


    public function createDepositIntent(Event $event): array
    {
        $this->eligibility->assertEligibleForDeposit($event);

        $totalAmount = round($event->calculateDepositAmount(), 2);

        return $this->createIntent(
            event: $event,
            totalAmount: $totalAmount,
            paymentType: PaymentType::DEPOSIT,
            bookingIdForRecord: null,
        );
    }


    public function createAddOnIntent(Booking $booking): array
    {
        $event = $booking->event;

        if (! $this->eventIsAtLeastDepositPaid($event)) {
            throw new PaymentException(
                "Cannot start add-on payment: event #{$event->id} has not completed its initial deposit yet."
            );
        }

        if ($booking->status !== BookingStatus::ACCEPTED) {
            throw new PaymentException(
                "Booking #{$booking->id} is not in an accepted state (current: {$booking->status->value})."
            );
        }

        $totalAmount = round((float) ($booking->final_price ?? $booking->estimated_price), 2);

        return $this->createIntent(
            event: $event,
            totalAmount: $totalAmount,
            paymentType: PaymentType::ADD_ON_PAYMENT,
            bookingIdForRecord: $booking->id,
        );
    }

    public function createFinalBalanceIntent(Event $event): array
    {
        $this->eligibility->assertEligibleForFinalBalance($event);

        $totalAmount = round($event->calculateRemainingBalance(), 2);

        if ($totalAmount <= 0) {
            throw new PaymentException("Event #{$event->id} has no remaining balance to pay.");
        }

        return $this->createIntent(
            event: $event,
            totalAmount: $totalAmount,
            paymentType: PaymentType::FINAL_BALANCE,
            bookingIdForRecord: null,
        );
    }


    private function createIntent(
        Event $event,
        float $totalAmount,
        PaymentType $paymentType,
        ?int $bookingIdForRecord,
    ): array {
        if ($totalAmount <= 0) {
            throw new PaymentException('Payment amount must be greater than zero.');
        }

        $amountInCents = (int) round($totalAmount * 100);

        return DB::transaction(function () use (
            $event,
            $totalAmount,
            $paymentType,
            $bookingIdForRecord,
            $amountInCents,
        ) {

            $payment = Payment::create([
                'event_id'                 => $event->id,
                'booking_id'               => $bookingIdForRecord,
                'stripe_charge_id'         => null,
                'stripe_payment_intent_id' => null,
                'amount'                   => $totalAmount,
                'payment_type'             => $paymentType->value,
                'status'                   => PaymentStatus::PENDING->value,
            ]);

            try {
                $intent = $this->stripe->paymentIntents->create([
                    'amount'   => $amountInCents,
                    'currency' => 'usd',
                    'metadata' => [
                        'payment_id'   => $payment->id,
                        'event_id'     => $event->id,
                        'payment_type' => $paymentType->value,
                    ],
                    'automatic_payment_methods' => [
                        'enabled'         => true,
                        'allow_redirects' => 'never',
                    ],
                ]);

            } catch (ApiErrorException $e) {
                Log::error('Failed to create Stripe PaymentIntent', [
                    'payment_id' => $payment->id,
                    'error'      => $e->getMessage(),
                ]);

                $payment->update(['status' => PaymentStatus::FAILED->value]);

                throw new PaymentException(
                    'Unable to initialize payment gateway.',
                    code: Response::HTTP_BAD_GATEWAY,
                    previous: $e
                );
            }

            $payment->update([
                'stripe_payment_intent_id' => $intent->id,
            ]);

            return [
                'client_secret' => $intent->client_secret,
                'payment_id'    => $payment->id,
                'amount'        => $totalAmount,
            ];
        });
    }

    private function eventIsAtLeastDepositPaid(Event $event): bool
    {
        $current = $event->status instanceof EventStatus
            ? $event->status
            : EventStatus::from($event->status);

        return in_array($current, [
            EventStatus::DEPOSIT_PAID,
            EventStatus::CONFIRMED,
            EventStatus::COMPLETED,
        ], true);
    }
}
