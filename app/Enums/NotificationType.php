<?php

namespace App\Enums;


use App\Http\Resources\Booking\BookingShowResource;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\Event\EventShowResource;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Event;

enum NotificationType: string
{
    case NEW_BOOKING          = 'new_booking';
    case BOOKING_ACCEPTED     = 'booking_accepted';
    case BOOKING_REJECTED     = 'booking_rejected';
    case NEW_MESSAGE          = 'new_message';





    public function subjectModel(): ?string
    {
        return match ($this) {
            self::NEW_BOOKING ,
            self::BOOKING_REJECTED,
            self::BOOKING_ACCEPTED,=> Booking::class,

            self::NEW_MESSAGE => Conversation::class

        };
    }


    public function subjectResource(): ?string
    {
        return match ($this) {
            self::NEW_BOOKING ,
            self::BOOKING_ACCEPTED,
            self::BOOKING_REJECTED => BookingShowResource::class,

            self::NEW_MESSAGE => ConversationResource::class,

            default => null,
        };
    }

}
