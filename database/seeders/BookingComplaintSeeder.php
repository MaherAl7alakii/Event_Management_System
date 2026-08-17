<?php

namespace Database\Seeders;

use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\BookingComplaint;
use Illuminate\Database\Seeder;

class BookingComplaintSeeder extends Seeder
{
    public function run(): void
    {
        BookingComplaint::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Complaint 1 - Completed booking
        |--------------------------------------------------------------------------
        */

        $booking = Booking::whereHas('event', function ($query) {
            $query->where('title', 'Lama Wedding');
        })->first();

        if ($booking) {
            BookingComplaint::create([
                'booking_id' => $booking->id,

                'customer_id' => $booking->customer_id,

                'description' =>
                    'Some edited wedding photos were delivered later than expected.',

                'status' => ComplaintStatus::RESOLVED_RELEASE->value,

                'admin_notes' =>
                    'Provider was contacted and the missing photos were delivered.',

                'resolved_by' => 1,

                'resolved_at' => now()->subDays(2),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Complaint 2 - Confirmed booking
        |--------------------------------------------------------------------------
        */

        $booking = Booking::whereHas('event', function ($query) {
            $query->where('title', 'Rami Wedding');
        })->first();

        if ($booking) {
            BookingComplaint::create([
                'booking_id' => $booking->id,

                'customer_id' => $booking->customer_id,

                'description' =>
                    'Customer reported an issue with the agreed service details.',

                'status' => ComplaintStatus::PENDING->value,

                'admin_notes' => null,

                'resolved_by' => null,

                'resolved_at' => null,
            ]);
        }
    }
}