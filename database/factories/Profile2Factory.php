<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class Profile2Factory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'city_id'       => City::inRandomOrder()->first()?->id,
            'phone'         => fake()->phoneNumber(),
            'avatar'        => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1754198165/premium_photo-1689977927774-401b12d137d6_uckeje.jpg',
            'address'       => fake()->address(),
            'gender'        => fake()->randomElement(['male', 'female']),
            'birth_of_date' => fake()->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
        ];
    }
}
