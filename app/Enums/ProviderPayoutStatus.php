<?php

namespace App\Enums;

enum ProviderPayoutStatus: string
{
    case SCHEDULED_FOR_COMPLETION = 'scheduled_for_completion';
    case AWAITING_RELEASE         = 'awaiting_release';
    case ON_HOLD                  = 'on_hold';
    case RELEASED                 = 'released';
    case CANCELLED                = 'cancelled';
    case FAILED                   = 'failed';
}
