<?php

namespace Database\Seeders;

use App\Enums\TransferStatus;
use App\Models\PaymentTransfer;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentTransferSeeder extends Seeder
{
    public function run(): void
    {
        PaymentTransfer::query()->delete();

        foreach (
            Payment::with('booking')->where('status', 'succeeded')->get()
            as $payment
        ) {

            $booking = $payment->booking;

            if (!$booking) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Submission fee belongs to platform
            |--------------------------------------------------------------------------
            */

            if ($payment->payment_type->value === 'submission_fee') {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Transfer status
            |--------------------------------------------------------------------------
            */

            $status = match ($booking->status->value) {

                'completed' =>
                    TransferStatus::SUCCEEDED->value,

                'confirmed',
                'deposit_paid' =>
                    TransferStatus::ON_HOLD->value,

                'cancelled' =>
                    TransferStatus::CANCELLED->value,

                default =>
                    TransferStatus::PENDING->value,
            };

            PaymentTransfer::create([
                'payment_id' => $payment->id,

                'booking_id' => $booking->id,

                'provider_id' => $booking->provider_id,

                'stripe_transfer_id' =>
                    $status === TransferStatus::SUCCEEDED->value
                        ? 'tr_seed_' . $payment->id
                        : null,

                'amount' => $payment->amount,

                'status' => $status,

                'failure_reason' => null,
            ]);
        }
    }
}