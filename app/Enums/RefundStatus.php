<?php

namespace App\Enums;

enum RefundStatus: string
{
    case PENDING   = 'pending';
    case SUCCEEDED = 'succeeded';
    case FAILED    = 'failed';
}
