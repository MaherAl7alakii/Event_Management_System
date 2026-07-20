<?php

namespace App\Http\Controllers;

use App\Http\Resources\Event\EventShowResource;
use App\Models\Booking;
use App\Models\Event;
use App\Services\EventSubmissionService;
use App\Services\PaymentGatewayService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    use AuthorizesRequests;
    use ResponseTrait;
    public function __construct(
        private readonly PaymentGatewayService $gatewayService,
        private readonly EventSubmissionService $submissionService,
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
}
