<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingRespondRequest;
use App\Http\Resources\Booking\BookingShowResource;
use App\Models\Booking;
use App\Services\BookingService;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class BookingStatusController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected BookingService $bookingService;
    protected string $resourceName = 'messages.resources.booking';

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }


    public function accept(BookingRespondRequest $request ,Booking $booking)
    {

//        $this->authorize('accept', $booking);

        $finalPrice = $request->input('final_price');


        $updatedBooking = $this->bookingService->respondToBooking($booking, 'accept',$request->buffer_after_minutes,$finalPrice);

        return $this->apiResponse(
            new BookingShowResource($updatedBooking),
            __('messages.booking_accepted_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function reject(Booking $booking)
    {
        $this->authorize('reject', $booking);

        $updatedBooking = $this->bookingService->respondToBooking($booking, 'reject');

        return $this->apiResponse(
            new BookingShowResource($updatedBooking),
            __('messages.booking_rejected_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }
}
