<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Jobs\CompleteBookingJob;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CompleteDueBookings extends Command
{
    protected $signature = 'bookings:complete-due';

    protected $description = 'Dispatch completion jobs for confirmed bookings whose time has fully elapsed.';

    public function handle(): int
    {

        $cutoff = Carbon::now()->subDays(3)->toDateString();

        $candidateIds = Booking::query()
            ->where('status', BookingStatus::CONFIRMED->value)
            ->where('booking_date', '>=', $cutoff)
            ->where('booking_date', '<=', now()->toDateString())
            ->get()
            ->filter(fn (Booking $booking) => now()->gte($booking->endsAtWithBuffer()))
            ->pluck('id');

        foreach ($candidateIds as $bookingId) {
            CompleteBookingJob::dispatch($bookingId);
        }

        $this->info("Dispatched {$candidateIds->count()} booking completion job(s).");

        return self::SUCCESS;
    }
}
