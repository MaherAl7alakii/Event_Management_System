<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;


class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'stripe_charge_id'         => 'ch_' . fake()->unique()->bothify('##########????'),
            'stripe_payment_intent_id' => 'pi_' . fake()->unique()->bothify('##########????'),
            'stripe_transfer_id'       => null,
            'amount'                   => fake()->numberBetween(50, 2000),
            'payment_type'             => 'deposit',
            'status'                   => 'succeeded',
        ];
    }
}
