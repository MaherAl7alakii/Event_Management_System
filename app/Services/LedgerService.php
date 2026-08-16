<?php

namespace App\Services;

use App\Enums\LedgerEntryType;
use App\Enums\ProviderPayoutStatus;
use App\Models\Booking;
use App\Models\BookingLedgerEntry;
use App\Models\Payment;
use App\Models\ProviderPayout;
use App\Models\Refund;
use Exception;


class LedgerService
{

    public function recordCharge(Booking $booking, Payment $payment, float $amount, LedgerEntryType $type): BookingLedgerEntry
    {
        if (! $type->isCharge()) {
            throw new Exception("{$type->value} is not a charge type.");
        }

        if ($amount <= 0) {
            throw new Exception('Charge amount must be greater than zero.');
        }

        return BookingLedgerEntry::create([
            'booking_id' => $booking->id,
            'type'       => $type->value,
            'amount'     => round($amount, 2),
            'payment_id' => $payment->id,
        ]);
    }


    public function recordRefund(Booking $booking, Refund $refund, float $amount): BookingLedgerEntry
    {
        $refundable = $this->refundableAmount($booking);


        if (round($amount, 2) > round($refundable, 2) + 0.01) {
            throw new Exception(
                "Refund amount ({$amount}) exceeds refundable balance ({$refundable}) for booking #{$booking->id}."
            );
        }

        return BookingLedgerEntry::create([
            'booking_id' => $booking->id,
            'type'       => LedgerEntryType::REFUND->value,
            'amount'     => round($amount, 2),
            'refund_id'  => $refund->id,
        ]);
    }

    public function recordPayout(Booking $booking, ProviderPayout $payout): BookingLedgerEntry
    {
        return BookingLedgerEntry::create([
            'booking_id'          => $booking->id,
            'type'                => LedgerEntryType::PAYOUT->value,
            'amount'              => round((float) $payout->amount, 2),
            'provider_payout_id'  => $payout->id,
        ]);
    }


    public function netPaidByCustomer(Booking $booking): float
    {
        $charges = BookingLedgerEntry::where('booking_id', $booking->id)
            ->whereIn('type', array_map(fn ($t) => $t->value, LedgerEntryType::chargeTypes()))
            ->sum('amount');

        $refunds = BookingLedgerEntry::where('booking_id', $booking->id)
            ->where('type', LedgerEntryType::REFUND->value)
            ->sum('amount');

        return max(round((float) $charges - (float) $refunds, 2), 0);
    }


    public function refundableAmount(Booking $booking): float
    {
        return $this->netPaidByCustomer($booking);
    }


    public function totalAllocatedToProvider(Booking $booking): float
    {
        return (float) BookingLedgerEntry::where('booking_id', $booking->id)
            ->where('type', LedgerEntryType::PAYOUT->value)
            ->whereHas('providerPayout', function ($q) {
                $q->where('status', '!=', ProviderPayoutStatus::CANCELLED->value);
            })
            ->sum('amount');
    }

    public function unallocatedAmountFor(Payment $payment): float
    {
        $allocated = BookingLedgerEntry::where('payment_id', $payment->id)
            ->whereIn('type', array_map(fn ($t) => $t->value, LedgerEntryType::chargeTypes()))
            ->sum('amount');

        return max(round((float) $payment->amount - (float) $allocated, 2), 0);
    }
}
