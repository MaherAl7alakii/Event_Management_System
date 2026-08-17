<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Requests\EventWithBookingsRequest;
use App\Http\Resources\Event\EventIndexResource;
use App\Http\Resources\Event\EventShowResource;
use App\Models\Event;
use App\Services\EventService;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected EventService $eventService;

    protected string $resourceName = 'messages.resources.event';

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $events = $this->eventService->getAllEvents(auth()->user());

        return $this->apiResponse(
            !$events->isEmpty() ?  EventIndexResource::collection($events): null,
            $events->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.events')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.events')]),
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        $data = $request->validated();
        $data['customer_id'] = auth()->id();

        $event = $this->eventService->createEvent($data);

        return $this->apiResponse(
            new EventShowResource($event),
            __('messages.created_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $eventData = $this->eventService->getEventById($event);

        return $this->apiResponse(
            new EventShowResource($eventData),
            __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Event $event)
    {

        $this->authorize('update', $event);

        $updatedEvent = $this->eventService->updateEvent(
            $event,
            $request->validated()
        );

        return $this->apiResponse(
            new EventShowResource($updatedEvent),
            __('messages.updated_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {

        $this->eventService->deleteEvent($event);

        return $this->apiResponse(
            null,
            __('messages.deleted_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }

    public function storeEventWithBookings(EventWithBookingsRequest $request)
    {

        $eventData = $request->only([
            'event_type_id',
            'other_type',
            'city_id',
            'title',
            'cover_image',
            'event_date',
            'start_time',
            'end_time',
            'guests_count'
        ]);


        $eventData['customer_id'] = auth()->id();
        $validatedData = $request->validated();
        $bookings = $validatedData['bookings'];

        $event = $this->eventService->createEventWithBookings($eventData, $bookings);


        return response()->json([
            'status'  => 201,
            'message' => 'success',
            new EventShowResource($event->load('bookings')),
        ], 201);
    }
}
