<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceProvider2Factory extends Factory
{

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
            'avatar'              => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1754198165/premium_photo-1689977927774-401b12d137d6_uckeje.jpg',
        ];
    }
}
