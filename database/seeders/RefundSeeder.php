<?php

namespace Database\Seeders;

use App\Enums\RefundStatus;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Database\Seeder;

class RefundSeeder extends Seeder
{
    public function run(): void
    {
        Refund::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Refund for cancelled booking
        |--------------------------------------------------------------------------
        */

        $payment = Payment::whereHas('booking.event', function ($query) {
            $query->where('title', 'Hussein Wedding');
        })
            ->where('payment_type', 'deposit')
            ->first();

        if ($payment) {

            Refund::create([
                'payment_id' => $payment->id,

                'amount' => $payment->amount,

                'reason' =>
                    'Booking cancelled by customer.',

                'stripe_refund_id' =>
                    're_seed_' . $payment->id,

                'status' =>
                    RefundStatus::SUCCEEDED->value,

                'failure_reason' => null,

                'initiated_by' => $payment->booking->customer_id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Another refund
        |--------------------------------------------------------------------------
        */

        $payment = Payment::whereHas('booking.event', function ($query) {
            $query->where('title', 'Razan Engagement');
        })
            ->where('payment_type', 'deposit')
            ->first();

        if ($payment) {

            Refund::create([
                'payment_id' => $payment->id,

                'amount' => $payment->amount,

                'reason' =>
                    'Provider rejected the booking.',

                'stripe_refund_id' =>
                    're_seed_' . ($payment->id + 1000),

                'status' =>
                    RefundStatus::SUCCEEDED->value,

                'failure_reason' => null,

                'initiated_by' => $payment->booking->customer_id,
            ]);
        }
    }
}