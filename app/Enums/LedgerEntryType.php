<?php

namespace App\Enums;


enum LedgerEntryType: string
{
    case DEPOSIT_CHARGE = 'deposit_charge';
    case FINAL_BALANCE_CHARGE = 'final_balance_charge';
    case ADD_ON_CHARGE = 'add_on_charge';
    case REFUND = 'refund';
    case PAYOUT = 'payout';


    public function isCharge(): bool
    {
        return in_array($this, self::chargeTypes(), true);
    }

    public function isCredit(): bool
    {
        return $this->isCharge();
    }

    public function isDebit(): bool
    {
        return in_array($this, [self::REFUND, self::PAYOUT], true);
    }

    /**
     * @return self[]
     */
    public static function chargeTypes(): array
    {
        return [
            self::DEPOSIT_CHARGE,
            self::FINAL_BALANCE_CHARGE,
            self::ADD_ON_CHARGE,
        ];
    }
}
