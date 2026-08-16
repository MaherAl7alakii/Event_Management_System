<?php

namespace App\Http\Controllers;

use App\Enums\CancelledBy;
use App\Http\Requests\Booking\CancelBookingRequest;
use App\Http\Resources\Booking\BookingShowResource;
use App\Models\Booking;
use App\Services\BookingCancellationService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BookingCancellationController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private readonly BookingCancellationService $cancellation,
    ) {
    }


    public function cancel(CancelBookingRequest $request, Booking $booking): JsonResponse
    {
        $userId = auth()->id();

        $cancelledBy = match ($userId) {
            $booking->customer_id => CancelledBy::CUSTOMER,
            $booking->provider_id => CancelledBy::PROVIDER,
            default => null,
        };

        abort_if(
            $cancelledBy === null,
            Response::HTTP_FORBIDDEN,
            __('messages.not_party_to_booking')
        );
        try {
            $cancelled = $this->cancellation->cancel(
                $booking,
                $cancelledBy,
                $request->validated('reason'),
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return $this->apiResponse(
            new BookingShowResource($cancelled),
            __('messages.booking_cancelled_successfully'),
            Response::HTTP_OK // 200
        );
    }
}
