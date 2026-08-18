<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 *
 * ملاحظة: كل الحقول العلائقية (service_id, event_id, customer_id,
 * provider_id) وكذلك الحالة والأسعار والتواريخ الدقيقة يتم تمريرها
 * دائماً من الـ Seeder (DemoDataSeeder@createBooking) لأنها تعتمد على
 * منطق العمل الخاص بكل حالة (completed / cancelled / expired).
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'pricing_type'         => 'fixed',
            'base_price'           => fake()->numberBetween(50, 2000),
            'estimated_price'      => fake()->numberBetween(50, 2000),
            'final_price'          => null,
            'deposit_amount_paid'  => null,
            'booking_date'         => now()->format('Y-m-d'),
            'start_time'           => '10:00:00',
            'duration'             => fake()->numberBetween(60, 480),
            'buffer_after_minutes' => fake()->numberBetween(0, 60),
            'quantity'             => fake()->numberBetween(1, 5),
            'status'               => 'completed',
            'customer_notes'       => fake()->optional()->sentence(),
        ];
    }
}
