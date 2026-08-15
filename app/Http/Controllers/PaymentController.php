<?php

namespace App\Http\Controllers;

use App\Http\Resources\Event\EventShowResource;
use App\Http\Resources\Payment\PaymentResource;
use App\Http\Resources\Payment\ProviderPayoutResource;
use App\Models\Booking;
use App\Models\Event;
use App\Services\EventSubmissionService;
use App\Services\PaymentGatewayService;
use App\Services\PaymentQueryService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    use AuthorizesRequests;
    use ResponseTrait;

    protected string $resourceName = 'messages.resources.payments';
    public function __construct(
        private readonly PaymentGatewayService $gatewayService,
        private readonly EventSubmissionService $submissionService,
        private readonly PaymentQueryService $paymentQuery
    ) {
    }

    public function submit(Event $event): JsonResponse
    {
        $this->authorize('update', $event);


        $result = $this->submissionService->submit($event);


        if ($result['requires_payment']) {
            return $this->apiResponse(
                [
                    'requires_payment' => true,
                    'client_secret'    => $result['client_secret'],
                    'payment_id'       => $result['payment_id'],
                    'amount'           => $result['amount'],
                ],
                __('messages.payment.submission_fee_required'),
                Response::HTTP_OK
            );
        }

        return $this->apiResponse(
            [
                'requires_payment' => false,
                'event'            => new EventShowResource($result['event']),
            ],
            __('messages.payment.event_submitted_success'),
            Response::HTTP_OK
        );
    }

    public function createDepositIntent(Event $event): JsonResponse
    {
        $this->authorize('update', $event);


        $result = $this->gatewayService->createDepositIntent($event);


        return $this->apiResponse(
            [
                'client_secret' => $result['client_secret'],
                'payment_id'    => $result['payment_id'],
                'amount'        => $result['amount'],
            ],
            __('messages.payment.deposit_intent_created'),
            Response::HTTP_OK
        );
    }


    public function createAddOnIntent(Booking $booking): JsonResponse
    {
        $this->authorize('update', $booking->event);


        $result = $this->gatewayService->createAddOnIntent($booking);


        return response()->json([
            'client_secret' => $result['client_secret'],
            'payment_id'    => $result['payment_id'],
            'amount'        => $result['amount'],
        ]);
    }


    public function createFinalBalanceIntent(Event $event): JsonResponse
    {
        $this->authorize('update', $event);

        $result = $this->gatewayService->createFinalBalanceIntent($event);

        return $this->apiResponse(
            [
                'client_secret' => $result['client_secret'],
                'payment_id'    => $result['payment_id'],
                'amount'        => $result['amount'],
            ],
            __('messages.payment.final_balance_intent_created'),
            Response::HTTP_OK
        );
    }


    public function payments(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date']);
        $payments = $this->paymentQuery->forCustomer(auth()->user(), $filters);

        return $this->apiResponse(
           !$payments->isEmpty() ? PaymentResource::collection($payments) : null,
            $payments->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourceName)])
                : __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function payouts(Request $request)
    {
        $provider = auth()->user();

        abort_unless($provider->serviceProvider, 403);

        $filters = $request->only(['from_date', 'to_date']);
        $payouts = $this->paymentQuery->forProvider($provider, $filters);

        return $this->apiResponse(
            !$payouts->isEmpty() ? ProviderPayoutResource::collection($payouts) : null,
            $payouts->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.payouts')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.payouts')]),
            Response::HTTP_OK
        );
    }


    public function payoutsSummary()
    {
        $provider = auth()->user();

        abort_unless($provider->serviceProvider, 403);

        $summary = $this->paymentQuery->providerEarningsSummary($provider);

        return $this->apiResponse(
            $summary,
            __('messages.fetched_success', ['resource' => __('messages.resources.payouts')]),
            Response::HTTP_OK
        );
    }


    public function adminPayments(Request $request)
    {
        $filters = $request->only(['status', 'customer_id', 'provider_id', 'from_date', 'to_date']);
        $payments = $this->paymentQuery->allPayments($filters);

        return $this->apiResponse(
           !$payments->isEmpty() ? PaymentResource::collection($payments) : null,
            $payments->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourceName)])
                : __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function adminPayouts(Request $request)
    {
        $filters = $request->only(['status', 'customer_id', 'provider_id', 'from_date', 'to_date']);
        $payouts = $this->paymentQuery->allPayouts($filters);

        return $this->apiResponse(
            ! $payouts->isEmpty() ? ProviderPayoutResource::collection($payouts): null,
            $payouts->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.payouts')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.payouts')]),
            Response::HTTP_OK
        );
    }
}
