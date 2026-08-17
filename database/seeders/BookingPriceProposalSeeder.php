<?php

namespace Database\Seeders;

use App\Enums\PriceProposalStatus;
use App\Models\Booking;
use App\Models\BookingPriceProposal;
use Illuminate\Database\Seeder;

class BookingPriceProposalSeeder extends Seeder
{
    public function run(): void
    {
        BookingPriceProposal::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Proposal 1 - Accepted
        |--------------------------------------------------------------------------
        */

        $booking = Booking::whereHas('event', function ($query) {
            $query->where('title', 'Sara Wedding');
        })->first();

        if ($booking) {

            $oldPrice = (float) $booking->estimated_price;

            $newPrice = round($oldPrice + 50, 2);

            BookingPriceProposal::create([
                'booking_id' => $booking->id,

                'proposed_by' => $booking->provider_id,

                'old_price' => $oldPrice,

                'new_price' => $newPrice,

                'status' => PriceProposalStatus::ACCEPTED->value,

                'respond_by' => now()->subDays(3),

                'responded_at' => now()->subDays(4),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Proposal 2 - Pending
        |--------------------------------------------------------------------------
        */

        $booking = Booking::whereHas('event', function ($query) {
            $query->where('title', 'Jana Graduation Party');
        })->first();

        if ($booking) {

            $oldPrice = (float) $booking->estimated_price;

            $newPrice = round($oldPrice + 75, 2);

            BookingPriceProposal::create([
                'booking_id' => $booking->id,

                'proposed_by' => $booking->provider_id,

                'old_price' => $oldPrice,

                'new_price' => $newPrice,

                'status' => PriceProposalStatus::PENDING->value,

                'respond_by' => now()->addDays(2),

                'responded_at' => null,
            ]);
        }
    }
}