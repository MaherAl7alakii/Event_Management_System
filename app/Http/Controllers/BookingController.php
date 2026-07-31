<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingStoreRequest;
use App\Http\Requests\Booking\BookingUpdateRequest;
use App\Http\Requests\Booking\CheckBookingAvailabilityRequest;
use App\Http\Requests\Booking\EstimatePriceRequest;
use App\Http\Resources\Booking\BookingIndexResource;
use App\Http\Resources\Booking\BookingShowResource;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Service;
use App\Services\BookingService;
use App\Traits\PaginationResponseTrait;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;
    use PaginationResponseTrait;

    protected BookingService $bookingService;


    protected string $resourceName = 'messages.resources.booking';
    protected string $resourcesName = 'messages.resources.bookings';

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $bookings = $this->bookingService->getUserBookings(auth()->user(), $status);

        return $this->apiResponse(
            !$bookings->isEmpty() ? [
                'pagination' => $this->formatPaginatedResponse($bookings),
                'bookings'   => BookingIndexResource::collection($bookings)
            ] : null,
            $bookings->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourcesName)])
                : __('messages.fetched_success', ['resource' => __($this->resourcesName)]),
            Response::HTTP_OK
        );
    }

    public function getEventBookings(Request $request ,Event $event)
    {

        $this->authorize('viewBookings', $event);

        $status = $request->query('status');

        $bookings = $this->bookingService->getBookingsByEvent($event ,$status);

        return $this->apiResponse(
            !$bookings->isEmpty() ? BookingIndexResource::collection($bookings): null,
            $bookings->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourcesName)])
                : __('messages.fetched_success', ['resource' => __($this->resourcesName)]),
            Response::HTTP_OK
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingStoreRequest $request)
    {
        $event = Event::findOrFail($request->validated()['event_id']);

        $this->authorize('create', [Booking::class, $event]);

        $booking = $this->bookingService->createBooking($request->validated(), auth()->id());

        return $this->apiResponse(
            new BookingShowResource($booking),
            __('messages.created_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['service', 'customer', 'provider', 'event.city.governorate']);

        return $this->apiResponse(
            new BookingShowResource($booking),
            __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(BookingUpdateRequest $request, Booking $booking)
    {

        $this->authorize('update', $booking);


        $updatedBooking = $this->bookingService->updateBooking($booking, $request->validated());


        return $this->apiResponse(
            new BookingShowResource($updatedBooking),
            __('messages.updated_success',  ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }



    public function estimatePrice(EstimatePriceRequest $request, Service $service)
    {

        $estimatedPrice = $this->bookingService->calculateEstimatedPrice($service, $request->validated());

        return response()->json([
            'status' => true,
            'data'   => [
                'service_id'      => $service->id,
                'base_price'      => (float) $service->base_price,
                'pricing_type'    => $service->pricing_type->value,
                'estimated_price' => $estimatedPrice,
            ]
        ], Response::HTTP_OK);
    }


    public function checkAvailability(CheckBookingAvailabilityRequest $request,Service $service)
    {

        $result = $this->bookingService->checkAvailability(
            service: $service,
            bookingDate: $request->validated('booking_date'),
            startTime: $request->validated('start_time'),
            duration: $request->validated('duration'),
        );

        $messageKey = $result['stage'] === 'date_validated'
            ? 'messages.date_available'
            : 'messages.slot_available';

        return $this->apiResponse(
            null,
            __($messageKey),
            200
        );
    }

}
