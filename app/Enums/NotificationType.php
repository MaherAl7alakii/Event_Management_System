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

    // -- الحجوزات --
    case BOOKING_SUBMITTED         = 'booking_submitted';

    case BOOKING_CANCELLED         = 'booking_cancelled';
    case BOOKING_COMPLETED         = 'booking_completed';
    case BOOKING_EXPIRED           = 'booking_expired';

    // -- المدفوعات --
    case DEPOSIT_PAID              = 'deposit_paid';
    case FINAL_BALANCE_PAID        = 'final_balance_paid';
    case PAYOUT_RELEASED           = 'payout_released';

    // -- تعديل السعر --
    case PRICE_PROPOSAL_CREATED    = 'price_proposal_created';
    case PRICE_PROPOSAL_ACCEPTED   = 'price_proposal_accepted';
    case PRICE_PROPOSAL_REJECTED   = 'price_proposal_rejected';
    case PRICE_PROPOSAL_EXPIRED    = 'price_proposal_expired';

    // -- الشكاوى --
    case COMPLAINT_FILED           = 'complaint_filed';
    case COMPLAINT_RESOLVED        = 'complaint_resolved';


    case BOOKING_MODIFICATION_PROPOSED   = 'booking_modification_proposed';
    case BOOKING_MODIFICATION_RESPONDED  = 'booking_modification_responded';








    public function subjectModel(): ?string
    {
        return match ($this) {
            self::NEW_BOOKING ,
            self::BOOKING_REJECTED,
            self::BOOKING_ACCEPTED,=> Booking::class,

            self::NEW_MESSAGE => Conversation::class,
            default => null,

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
