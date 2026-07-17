<?php

namespace App\Enums;

enum BookingStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case DEPOSIT_PAID = 'deposit_paid';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';


}
