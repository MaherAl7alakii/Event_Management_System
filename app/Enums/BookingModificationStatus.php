<?php

namespace App\Enums;

enum BookingModificationStatus: string
{
    case PENDING              = 'pending';
    case APPROVED             = 'approved';
    case REJECTED             = 'rejected';
    case EXPIRED               = 'expired';
    case APPLIED_IMMEDIATELY  = 'applied_immediately';

    public function isFinal(): bool
    {
        return $this !== self::PENDING;
    }
}
