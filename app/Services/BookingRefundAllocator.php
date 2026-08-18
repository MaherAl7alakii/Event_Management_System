<?php

namespace App\Services;

use App\Enums\LedgerEntryType;
use App\Models\Booking;
use App\Models\BookingLedgerEntry;
use App\Models\Payment;
use Exception;


class BookingRefundAllocator
{
    public function __construct(
        private readonly RefundService $refunds,
        private readonly LedgerService $ledger,
    ) {
    }


    public function refundBookingShare(Booking $booking, float $amount, string $reason): void
    {
        $remaining = round($amount, 2);

        if ($remaining <= 0) {
            return;
        }

        $chargeTypeValues = array_map(fn (LedgerEntryType $t) => $t->value, LedgerEntryType::chargeTypes());

        $chargesByPayment = BookingLedgerEntry::where('booking_id', $booking->id)
            ->whereIn('type', $chargeTypeValues)
            ->whereNotNull('payment_id')
            ->selectRaw('payment_id, SUM(amount) as total_charged')
            ->groupBy('payment_id')
            ->pluck('total_charged', 'payment_id');

        if ($chargesByPayment->isEmpty()) {
            throw new Exception(
                "Booking #{$booking->id} has no ledger charge history to refund from — cannot allocate refund."
            );
        }


        $payments = Payment::whereIn('id', $chargesByPayment->keys())
            ->orderByDesc('created_at')
            ->get();

        foreach ($payments as $payment) {
            if ($remaining <= 0) {
                break;
            }

            $totalCharged = (float) $chargesByPayment[$payment->id];

            $alreadyRefundedFromThisPayment = BookingLedgerEntry::where('booking_id', $booking->id)
                ->where('type', LedgerEntryType::REFUND->value)
                ->whereHas('refund', fn ($q) => $q->where('payment_id', $payment->id))
                ->sum('amount');

            $availableOnThisPayment = round($totalCharged - (float) $alreadyRefundedFromThisPayment, 2);

            if ($availableOnThisPayment <= 0) {
                continue;
            }

            $toRefund = min($remaining, $availableOnThisPayment);

            $refund = $this->refunds->refund($payment, $toRefund, $reason);
            $this->ledger->recordRefund($booking, $refund, $toRefund);

            $remaining = round($remaining - $toRefund, 2);
        }

        if ($remaining > 0.01) {
            throw new Exception(
                "Refund allocation incomplete for booking #{$booking->id}: {$remaining} could not be matched to any contributing payment. Check for data inconsistency in the ledger."
            );
        }
    }
}
