<?php

namespace App\Enums;

enum ComplaintStatus: string
{

    case PENDING = 'pending';


    case RESOLVED_RELEASE = 'resolved_release';


    case RESOLVED_REFUND = 'resolved_refund';
}
