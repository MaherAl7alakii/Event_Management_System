<?php

namespace App\Enums;

enum PriceProposalStatus: string
{
    case PENDING  = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED  = 'expired';
}
