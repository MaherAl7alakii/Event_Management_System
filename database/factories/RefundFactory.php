<?php

namespace Database\Factories;

use App\Models\Refund;
use Illuminate\Database\Eloquent\Factories\Factory;


class RefundFactory extends Factory
{
    protected $model = Refund::class;

    public function definition(): array
    {
        return [
            'amount'           => fake()->numberBetween(50, 2000),
            'reason'           => 'booking_cancelled',
            'stripe_refund_id' => 're_' . fake()->unique()->bothify('##########????'),
            'status'           => 'succeeded',
            'failure_reason'   => null,
            'initiated_by'     => null,
        ];
    }
}
