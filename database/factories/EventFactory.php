<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;


class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startHour = fake()->numberBetween(10, 16);

        return [
            'event_type_id' => fake()->numberBetween(1, 8),
            'city_id'       => fake()->numberBetween(1, 44),
            'other_type'    => null,
            'title'         => fake()->sentence(3),
            'cover_image'   => null,
            'start_time'    => sprintf('%02d:00:00', $startHour),
            'end_time'      => sprintf('%02d:00:00', min($startHour + 4, 23)),
            'guests_count'  => fake()->numberBetween(30, 400),
            'status'        => 'completed',
        ];
    }
}
