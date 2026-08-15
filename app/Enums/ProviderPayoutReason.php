<?php

namespace App\Enums;

enum ProviderPayoutReason: string
{
    case DEPOSIT                     = 'deposit';
    case FINAL_BALANCE                = 'final_balance';
    case ADD_ON                       = 'add_on';


    case CANCELLATION_DEPOSIT_SHARE   = 'cancellation_deposit_share';


    case CANCELLATION_FULL_AMOUNT     = 'cancellation_full_amount';
}
