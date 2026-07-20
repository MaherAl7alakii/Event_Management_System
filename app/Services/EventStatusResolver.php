<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Support\Collection;

class EventStatusResolver
{

    private const LOCKED_EVENT_STATUSES = [
        EventStatus::DEPOSIT_PAID,
        EventStatus::CONFIRMED,
        EventStatus::COMPLETED,
        EventStatus::CANCELLED,
        EventStatus::EXPIRED,
    ];


    public function resolveAndPersist(Event $event): Event
    {
        $currentStatus = $this->toEventStatus($event->status);


        if (in_array($currentStatus, self::LOCKED_EVENT_STATUSES, true)) {
            return $event;
        }

        $bookings = $event->bookings()->get(['id', 'status']);

        $newStatus = $this->resolve($bookings);

        if ($newStatus !== $currentStatus) {
            $event->status = $newStatus->value;


            if ($newStatus !== EventStatus::DRAFT && $event->submitted_at === null) {
                $event->submitted_at = now();
            }

            $event->save();
        }

        return $event;
    }


    public function resolve(Collection $bookings): EventStatus
    {
        if ($bookings->isEmpty()) {
            return EventStatus::DRAFT;
        }

        $statuses = $bookings->map(
            fn (Booking $booking) => $this->toBookingStatus($booking->status)
        );

        $nonDraft = $statuses->reject(
            fn (BookingStatus $status) => in_array($status, BookingStatus::unsentStatuses(), true)
        );


        if ($nonDraft->isEmpty()) {
            return EventStatus::DRAFT;
        }

        $hasDraft = $statuses->contains(BookingStatus::DRAFT);
        $hasPending = $nonDraft->contains(BookingStatus::PENDING);
        $hasAcceptedOrBeyond = $nonDraft->contains(
            fn (BookingStatus $status) => in_array($status, BookingStatus::acceptedOrBeyondStatuses(), true)
        );
        $allNonDraftAreRejectedOrExpired = $nonDraft->every(
            fn (BookingStatus $status) => in_array($status, BookingStatus::terminalRejectedStatuses(), true)
        );


        if ($allNonDraftAreRejectedOrExpired) {
            return EventStatus::SUBMITTED;
        }


        if ($hasAcceptedOrBeyond && ($hasPending || $hasDraft)) {
            return EventStatus::PARTIALLY_ACCEPTED;
        }


        if ($hasAcceptedOrBeyond && ! $hasPending && ! $hasDraft) {
            return EventStatus::AWAITING_PAYMENT;
        }


        return EventStatus::SUBMITTED;
    }


    private function toEventStatus(EventStatus|string $status): EventStatus
    {
        return $status instanceof EventStatus ? $status : EventStatus::from($status);
    }

    private function toBookingStatus(BookingStatus|string $status): BookingStatus
    {
        return $status instanceof BookingStatus ? $status : BookingStatus::from($status);
    }
}
