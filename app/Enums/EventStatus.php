<?php

namespace App\Enums;

enum EventStatus: string
{
    case DRAFT             = 'draft';
    case SUBMITTED         = 'submitted';
    case PARTIALLY_ACCEPTED = 'partially_accepted';
    case AWAITING_PAYMENT  = 'awaiting_payment';
    case DEPOSIT_PAID      = 'deposit_paid';
    case CONFIRMED         = 'confirmed';
    case COMPLETED         = 'completed';
    case CANCELLED         = 'cancelled';
    case EXPIRED           = 'expired';



}
