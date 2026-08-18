<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;


class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'category_id'    => fake()->numberBetween(1, 8),
            'city_id'        => fake()->numberBetween(1, 44),
            'pricing_type'   => fake()->randomElement([
                'fixed', 'per_hour', 'per_person', 'per_hour_per_person',
            ]),
            'base_price'     => fake()->numberBetween(50, 2000),
            'is_active'      => true,
            'min_hours'      => fake()->numberBetween(1, 3),
            'max_hours'      => fake()->numberBetween(4, 10),
            'max_guests'     => fake()->numberBetween(50, 500),
            'rating'         => fake()->randomFloat(2, 3, 5),
            'rating_count'   => fake()->numberBetween(0, 100),
            'reviews_count'  => fake()->numberBetween(0, 100),
            'bookings_count' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Service $service) {
            $service->translateOrNew('en')->fill([
                'title'       => ucfirst(fake()->words(3, true)),
                'description' => fake()->paragraph(),
                'address'     => fake()->address(),
            ]);

            $service->translateOrNew('ar')->fill([
                'title'       => 'خدمة ' . fake()->word(),
                'description' => fake()->realText(150),
                'address'     => fake()->address(),
            ]);

            $service->save();
        });
    }
}
