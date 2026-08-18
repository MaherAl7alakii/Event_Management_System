<?php

namespace Database\Factories;

use App\Models\ProviderPayout;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProviderPayoutFactory extends Factory
{
    protected $model = ProviderPayout::class;

    public function definition(): array
    {
        return [
            'amount'             => fake()->numberBetween(50, 2000),
            'reason'             => 'deposit',
            'status'             => 'released',
            'release_at'         => now(),
            'stripe_transfer_id' => 'tr_' . fake()->unique()->bothify('##########????'),
            'failure_reason'     => null,
            'hold_reason'        => null,
        ];
    }
}
