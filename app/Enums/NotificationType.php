<?php

namespace App\Enums;


use App\Http\Resources\Booking\BookingShowResource;
use App\Http\Resources\Event\EventShowResource;
use App\Models\Booking;
use App\Models\Event;

enum NotificationType: string
{
    case NEW_BOOKING               = 'new_booking';
    case BOOKING_ACCEPTED          = 'booking_accepted';
    case BOOKING_REJECTED          = 'booking_rejected';





    public function subjectModel(): ?string
    {
        return match ($this) {
            self::NEW_BOOKING => Booking::class,

            self::BOOKING_REJECTED,
            self::BOOKING_ACCEPTED,=> Event::class,

        };
    }


    public function subjectResource(): ?string
    {
        return match ($this) {
            self::NEW_BOOKING => BookingShowResource::class,

            self::BOOKING_ACCEPTED,
            self::BOOKING_REJECTED => EventShowResource::class ,

            default => null,
        };
    }

}
