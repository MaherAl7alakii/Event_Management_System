<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Jobs\CancelExpiredBookingJob;
use App\Models\Booking;
use Illuminate\Console\Command;


class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';

    protected $description = 'Dispatch cancellation jobs for bookings whose payment deadline has passed.';

    public function handle(): int
    {
        $now = now();

        $expiredDepositBookingIds = Booking::query()
            ->where('status', BookingStatus::ACCEPTED->value)
            ->whereNotNull('deposit_deadline_at')
            ->where('deposit_deadline_at', '<=', $now)
            ->pluck('id');

        $expiredFinalPaymentBookingIds = Booking::query()
            ->where('status', BookingStatus::DEPOSIT_PAID->value)
            ->whereNotNull('final_payment_deadline_at')
            ->where('final_payment_deadline_at', '<=', $now)
            ->pluck('id');

        $allExpiredIds = $expiredDepositBookingIds->merge($expiredFinalPaymentBookingIds)->unique();


        foreach ($allExpiredIds as $bookingId) {
            CancelExpiredBookingJob::dispatch($bookingId);
        }

        $this->info("Dispatched {$allExpiredIds->count()} expired-booking cancellation job(s).");

        return self::SUCCESS;
    }
}
