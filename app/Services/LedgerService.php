<?php

namespace App\Services;

use App\Enums\LedgerEntryType;
use App\Models\Booking;
use App\Models\BookingLedgerEntry;
use App\Models\Payment;
use App\Models\ProviderPayout;
use App\Models\Refund;
use Exception;

/**
 * الواجهة المركزية الوحيدة للتعامل مع دفتر حسابات الحجوزات
 * (BookingLedgerEntry). لا يجب لأي كود آخر في المشروع إنشاء صف
 * ledger مباشرة أو حساب "كم دفع الزبون" يدوياً — كل ذلك يمر من هنا،
 * لضمان بقاء الأرقام متسقة دائماً مهما تعقّدت سلسلة الأحداث على حجز
 * معين (دفعات متعددة، استردادات جزئية، تعديلات سعر متكررة...).
 */
class LedgerService
{
    /**
     * يسجّل حصة هذا الحجز من دفعة ناجحة (عربون/رصيد نهائي/إضافة).
     * $amount هو حصة هذا الحجز تحديداً من الدفعة الإجمالية، وليس
     * بالضرورة كامل مبلغ $payment (الذي قد يغطي عدة حجوزات معاً).
     */
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

    /**
     * يسجّل استرداداً فعلياً منفَّذاً بالفعل (بعد نجاح RefundService)
     * من حصة هذا الحجز تحديداً.
     *
     * @throws Exception إن كان المبلغ يتجاوز الصافي المتاح للاسترداد لهذا الحجز.
     */
    public function recordRefund(Booking $booking, Refund $refund, float $amount): BookingLedgerEntry
    {
        $refundable = $this->refundableAmount($booking);

        // هامش تقريب بسيط (نقطتان عشريتان) لتفادي رفض مبالغ متطابقة
        // فعلياً لكنها تختلف بجزء من المليمتر بسبب حسابات عشرية متتالية.
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

    /**
     * يسجّل استحقاقاً (سواء كان لا يزال مُجدولاً أو حُرِّر فعلياً — لا
     * فرق من منظور الدفتر، لأن ProviderPayout نفسه يتتبّع حالته
     * الخاصة؛ الدفتر فقط يوثّق "كم من مال هذا الحجز خُصِّص كاستحقاق
     * لمزوده"، بمعزل عن سؤال "هل حُوِّل فعلياً بعد أم لا").
     */
    public function recordPayout(Booking $booking, ProviderPayout $payout): BookingLedgerEntry
    {
        return BookingLedgerEntry::create([
            'booking_id'          => $booking->id,
            'type'                => LedgerEntryType::PAYOUT->value,
            'amount'              => round((float) $payout->amount, 2),
            'provider_payout_id'  => $payout->id,
        ]);
    }

    /* ==================================================================
     | القراءة: كل الحسابات المشتقة من الدفتر
     * ================================================================== */

    /**
     * إجمالي ما دفعه الزبون فعلياً لهذا الحجز حتى الآن، ناقص أي
     * استرداد سابق. هذا هو الرقم الصحيح دائماً — بديل كامل عن
     * Booking.deposit_amount_paid القديم الذي كان يمثل فقط "آخر عربون
     * سُجِّل"، وينهار بمجرد وجود دفعة ثانية أو استرداد جزئي.
     */
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

    /**
     * الحد الأقصى القابل للاسترداد الآن لهذا الحجز: كل ما دُفع فعلياً
     * ناقص كل ما استُرد سابقاً بالفعل. يمنع استرداداً مزدوجاً أو
     * مبالغاً فيه من نفس الحجز، حتى لو استُدعيت الدالة أكثر من مرة.
     */
    public function refundableAmount(Booking $booking): float
    {
        return $this->netPaidByCustomer($booking);
    }


    public function totalAllocatedToProvider(Booking $booking): float
    {
        return (float) BookingLedgerEntry::where('booking_id', $booking->id)
            ->where('type', LedgerEntryType::PAYOUT->value)
            ->sum('amount');
    }

    /**
     * كم تبقّى من دفعة واحدة (Payment على مستوى الحدث) لم يُخصَّص بعد
     * لأي حجز — يُستخدم للتحقق الدفاعي بعد كل عملية تخصيص جماعية
     * (schedulePayoutsForPayment وما شابهها)، للتأكد أن مجموع حصص كل
     * الحجوزات المخصَّصة من نفس الدفعة لا يتجاوز مبلغها الإجمالي.
     */
    public function unallocatedAmountFor(Payment $payment): float
    {
        $allocated = BookingLedgerEntry::where('payment_id', $payment->id)
            ->whereIn('type', array_map(fn ($t) => $t->value, LedgerEntryType::chargeTypes()))
            ->sum('amount');

        return max(round((float) $payment->amount - (float) $allocated, 2), 0);
    }
}
