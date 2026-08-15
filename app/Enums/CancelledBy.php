<?php

namespace App\Enums;

enum CancelledBy: string
{
    case CUSTOMER = 'customer';
    case PROVIDER = 'provider';
}
