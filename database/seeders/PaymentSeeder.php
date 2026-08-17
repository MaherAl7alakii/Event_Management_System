<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::query()->delete();

        foreach (Booking::with('event')->get() as $booking) {

            /*
            |--------------------------------------------------------------------------
            | Deposit
            |--------------------------------------------------------------------------
            */

            if (in_array($booking->status->value, [
                'deposit_paid',
                'confirmed',
                'completed',
            ])) {

                $amount = (float) (
                    $booking->deposit_amount_paid
                    ?? $booking->depositAmount()
                );

                Payment::create([
                    'event_id' => $booking->event_id,
                    'booking_id' => $booking->id,

                    'amount' => $amount,

                    'stripe_charge_id' =>
                        'ch_seed_deposit_' . $booking->id,

                    'stripe_payment_intent_id' =>
                        'pi_seed_deposit_' . $booking->id,

                    'stripe_transfer_id' => null,

                    'payment_type' => PaymentType::DEPOSIT->value,

                    'status' => PaymentStatus::SUCCEEDED->value,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Final Balance
            |--------------------------------------------------------------------------
            */

            if ($booking->status->value === 'completed') {

                $total = (float) $booking->totalValue();

                $deposit = (float) $booking->depositAmount();

                $remaining = max($total - $deposit, 0);

                if ($remaining > 0) {

                    Payment::create([
                        'event_id' => $booking->event_id,
                        'booking_id' => $booking->id,

                        'amount' => round($remaining, 2),

                        'stripe_charge_id' =>
                            'ch_seed_final_' . $booking->id,

                        'stripe_payment_intent_id' =>
                            'pi_seed_final_' . $booking->id,

                        'stripe_transfer_id' => null,

                        'payment_type' =>
                            PaymentType::FINAL_BALANCE->value,

                        'status' =>
                            PaymentStatus::SUCCEEDED->value,
                    ]);
                }
            }
        }
    }
}