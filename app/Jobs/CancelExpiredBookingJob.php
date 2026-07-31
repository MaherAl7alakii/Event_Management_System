<?php

namespace App\Jobs;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\EventStatusResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CancelExpiredBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $bookingId,
    ) {
    }

    public function handle(EventStatusResolver $statusResolver): void
    {
        DB::transaction(function () use ($statusResolver) {
            $booking = Booking::lockForUpdate()->find($this->bookingId);

            if (! $booking) {
                return;
            }

            if (! $this->isStillExpired($booking)) {
                return;
            }

            $booking->update([
                'status'      => BookingStatus::EXPIRED->value,
                'rejected_at' => now(),
            ]);

            $statusResolver->resolveAndPersist($booking->event);

            Log::info('Booking auto-cancelled: payment deadline expired', [
                'booking_id' => $booking->id,
                'status_at_expiry' => $booking->getOriginal('status'),
            ]);

            //---- Notification
        });
    }


    private function isStillExpired(Booking $booking): bool
    {
        $now = now();

        if ($booking->status === BookingStatus::ACCEPTED
            && $booking->deposit_deadline_at !== null
            && $now->gte($booking->deposit_deadline_at)) {
            return true;
        }

        if ($booking->status === BookingStatus::DEPOSIT_PAID
            && $booking->final_payment_deadline_at !== null
            && $now->gte($booking->final_payment_deadline_at)) {
            return true;
        }

        return false;
    }
}
