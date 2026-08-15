<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\ProviderPayoutService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * يحوّل حجزاً واحداً إلى completed فور انتهاء وقته الفعلي (بما فيه
 * هامش المزود، endsAtWithBuffer)، ويجدول استحقاقاته المالية المعلّقة
 * للتحرير بعد 24 ساعة (عبر ProviderPayoutService).
 *
 * فقط الحجوزات confirmed تُكمَل تلقائياً — أي حجز لم يصل لهذه الحالة
 * (draft/pending/accepted/deposit_paid) يعني أن العملية المالية لم
 * تكتمل، فلا معنى لإكماله كخدمة تم تسليمها فعلياً وتم دفعها بالكامل.
 */
class CompleteBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $bookingId,
    ) {
    }

    public function handle(ProviderPayoutService $payoutService): void
    {
        DB::transaction(function () use ($payoutService) {
            $booking = Booking::lockForUpdate()->find($this->bookingId);

            if (! $booking || $booking->status !== BookingStatus::CONFIRMED) {
                return;
            }

            // إعادة تحقق تحت القفل: هل حقاً انتهى وقته فعلياً الآن؟
            if (now()->lt($booking->endsAtWithBuffer())) {
                return;
            }

            $booking->update([
                'status'       => BookingStatus::COMPLETED->value,
                'completed_at' => now(),
            ]);

            $payoutService->scheduleReleaseForCompletedBooking($booking->fresh());
        });
    }
}
