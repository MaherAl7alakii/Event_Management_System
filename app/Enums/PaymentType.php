<?php

namespace App\Enums;

enum PaymentType: string
{
    case SUBMISSION_FEE  = 'submission_fee';
    case DEPOSIT         = 'deposit';
    case ADD_ON_PAYMENT  = 'add_on_payment';
    case FINAL_BALANCE   = 'final_balance';


    public static function platformOnlyTypes(): array
    {
        return [self::SUBMISSION_FEE];
    }
}
