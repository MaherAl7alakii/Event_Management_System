<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function getAllEvents(User $user)
    {
        $events = $user->events()->latest()->get();

        return $events;
    }


    public function createEvent(array $data): Event
    {

        $event = Event::create($data);
        $event->status = EventStatus::DRAFT;;

        return $event;
    }

    public function createEventWithBookings(array $eventData, array $bookings): Event
    {
        return DB::transaction(function () use ($eventData, $bookings) {
            $event = Event::create($eventData);

            $event->status = EventStatus::DRAFT;
            $event->save();

            foreach ($bookings as $bookingData) {
                $bookingData['event_id'] = $event->id;


                $this->bookingService->createBooking($bookingData, $event->customer_id);
            }


            return $event;
        });
    }


    public function getEventById(Event $event): Event
    {
        return $event;
    }


    public function updateEvent(Event $event, array $data): Event
    {
        $event->update($data);

        return $event;
    }


    public function deleteEvent(Event $event): bool
    {
        return $event->delete();
    }

}
