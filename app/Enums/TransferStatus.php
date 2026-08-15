<?php

namespace App\Enums;

enum TransferStatus: string
{

    case ON_HOLD = 'on_hold';

    case ON_COMPLAINT_HOLD = 'on_complaint_hold';


    case PENDING = 'pending';

    case SUCCEEDED = 'succeeded';
    case FAILED    = 'failed';

    case CANCELLED = 'cancelled';
}
