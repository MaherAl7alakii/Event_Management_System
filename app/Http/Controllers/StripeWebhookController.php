<?php

namespace App\Http\Controllers;

use App\Services\PaymentCompletionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;


class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentCompletionService $distributionService,
    ) {
    }

    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        } catch (Exception $e) {
            Log::error('Malformed Stripe webhook payload', ['error' => $e->getMessage()]);

            return response('Malformed payload', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;

            $chargeId = $intent->latest_charge ?? null;

            try {
                $this->distributionService->handleSucceededPayment($intent->id, $chargeId);
            } catch (Exception $e) {
                Log::error('Failed to process succeeded payment webhook', [
                    'payment_intent_id' => $intent->id,
                    'error'              => $e->getMessage(),
                ]);

                return response('Processing failed', 500);
            }
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $intent = $event->data->object;

            \App\Models\Payment::where('stripe_payment_intent_id', $intent->id)
                ->update(['status' => \App\Enums\PaymentStatus::FAILED->value]);

            Log::info('Payment intent failed', ['payment_intent_id' => $intent->id]);
        }

        return response('Webhook handled', 200);
    }
}
