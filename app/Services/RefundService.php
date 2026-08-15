<?php

namespace App\Services;

use App\Enums\RefundStatus;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class RefundService
{
    public function __construct(
        private readonly StripeClient $stripe,
    ) {
    }


    public function refund(Payment $payment, float $amount, string $reason, ?User $initiatedBy = null): Refund
    {
        if ($payment->stripe_charge_id === null) {
            throw new Exception("Payment #{$payment->id} has no charge to refund.");
        }

        if ($amount <= 0) {
            throw new Exception('Refund amount must be greater than zero.');
        }


        $refundRecord = Refund::create([
            'payment_id'    => $payment->id,
            'amount'        => round($amount, 2),
            'reason'        => $reason,
            'status'        => RefundStatus::PENDING->value,
            'initiated_by'  => $initiatedBy?->id,
        ]);

        $amountInCents = (int) round($amount * 100);

        try {
            $stripeRefund = $this->stripe->refunds->create([
                'charge'   => $payment->stripe_charge_id,
                'amount'   => $amountInCents,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'refund_id'  => $refundRecord->id,
                    'reason'     => $reason,
                ],
            ]);

            $refundRecord->update([
                'status'            => RefundStatus::SUCCEEDED->value,
                'stripe_refund_id'  => $stripeRefund->id,
            ]);

            Log::info('Refund issued', [
                'refund_id'  => $refundRecord->id,
                'payment_id' => $payment->id,
                'amount'     => $amount,
                'reason'     => $reason,
            ]);
        } catch (ApiErrorException $e) {
            $refundRecord->update([
                'status'         => RefundStatus::FAILED->value,
                'failure_reason' => $e->getMessage(),
            ]);

            Log::error('Refund failed', [
                'refund_id'  => $refundRecord->id,
                'payment_id' => $payment->id,
                'amount'     => $amount,
                'error'      => $e->getMessage(),
            ]);

            throw new Exception('Unable to process refund.', previous: $e);
        }

        return $refundRecord->fresh();
    }
}
