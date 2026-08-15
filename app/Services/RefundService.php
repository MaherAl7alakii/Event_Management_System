<?php

namespace App\Services;

use App\Models\Payment;
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

    public function refund(Payment $payment, float $amount, string $reason): void
    {
        if ($payment->stripe_charge_id === null) {
            throw new Exception("Payment #{$payment->id} has no charge to refund.");
        }

        if ($amount <= 0) {
            throw new Exception('Refund amount must be greater than zero.');
        }

        $amountInCents = (int) round($amount * 100);

        try {
            $this->stripe->refunds->create([
                'charge'   => $payment->stripe_charge_id,
                'amount'   => $amountInCents,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'reason'     => $reason,
                ],
            ]);

            Log::info('Refund issued', [
                'payment_id' => $payment->id,
                'amount'     => $amount,
                'reason'     => $reason,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Refund failed', [
                'payment_id' => $payment->id,
                'amount'     => $amount,
                'error'      => $e->getMessage(),
            ]);

            throw new Exception('Unable to process refund.', previous: $e);
        }
    }
}
