<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;

class EventService
{

    public function getAllEvents(User $user)
    {
        $events = $user->events()->latest()->get();

        return $events;
    }


    public function createEvent(array $data): Event
    {

        $event = Event::create($data);
        $event->status = 'draft';

        return $event;
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
