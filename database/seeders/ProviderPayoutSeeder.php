<?php

namespace Database\Seeders;

use App\Enums\ProviderPayoutReason;
use App\Enums\ProviderPayoutStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ProviderPayout;
use Illuminate\Database\Seeder;

class ProviderPayoutSeeder extends Seeder
{
    public function run(): void
    {
        ProviderPayout::query()->delete();

        foreach (Booking::with('payments')->get() as $booking) {

            if (!in_array($booking->status->value, [
                'deposit_paid',
                'confirmed',
                'completed',
            ])) {
                continue;
            }

            foreach ($booking->payments as $payment) {

                if ($payment->payment_type->value === 'submission_fee') {
                    continue;
                }

                $reason = match ($payment->payment_type->value) {

                    'deposit' =>
                        ProviderPayoutReason::DEPOSIT->value,

                    'final_balance' =>
                        ProviderPayoutReason::FINAL_BALANCE->value,

                    'add_on_payment' =>
                        ProviderPayoutReason::ADD_ON->value,

                    default =>
                        ProviderPayoutReason::DEPOSIT->value,
                };

                $status = match ($booking->status->value) {

                    'completed' =>
                        ProviderPayoutStatus::RELEASED->value,

                    'confirmed' =>
                        ProviderPayoutStatus::AWAITING_RELEASE->value,

                    'deposit_paid' =>
                        ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value,

                    default =>
                        ProviderPayoutStatus::CANCELLED->value,
                };

                ProviderPayout::create([
                    'booking_id' => $booking->id,

                    'provider_id' => $booking->provider_id,

                    'payment_id' => $payment->id,

                    'amount' => $payment->amount,

                    'reason' => $reason,

                    'status' => $status,

                    'release_at' => $status ===
                        ProviderPayoutStatus::AWAITING_RELEASE->value
                        ? now()->addDays(7)
                        : null,

                    'stripe_transfer_id' => $status ===
                        ProviderPayoutStatus::RELEASED->value
                        ? 'tr_payout_seed_' . $payment->id
                        : null,

                    'failure_reason' => null,

                    'hold_reason' => null,
                ]);
            }
        }
    }
}