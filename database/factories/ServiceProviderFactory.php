<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceProvider>
 */
class ServiceProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id'             => City::inRandomOrder()->first()?->id ?? 1,
            'business_name'       => $this->faker->company(),
            'phone'               => '+96650' . $this->faker->numerify('#######'),
            'address'             => $this->faker->address(),
            'years_of_experience' => $this->faker->numberBetween(2, 15),
            'description'         => $this->faker->paragraph(3),
            'approval_status'     => 'approved',
            'verified_at'         => now(),
            'avatar'              => 'avatars/default-provider.png',
        ];
    }
}
