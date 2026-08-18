<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'city_id'       => fake()->numberBetween(1, 44),
            'phone'         => '09' . fake()->numerify('########'),
            'avatar'        => null,
            'address'       => fake()->address(),
            'gender'        => fake()->randomElement(['male', 'female']),
            'birth_of_date' => fake()->dateTimeBetween('-55 years', '-18 years')->format('Y-m-d'),
        ];
    }
}
