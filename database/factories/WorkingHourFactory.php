<?php

namespace Database\Factories;

use App\Models\WorkingHour;
use Illuminate\Database\Eloquent\Factories\Factory;


class WorkingHourFactory extends Factory
{
    protected $model = WorkingHour::class;

    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 11);
        $endHour   = fake()->numberBetween(17, 22);

        return [
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time'   => sprintf('%02d:00:00', $endHour),
            'is_active'  => true,
        ];
    }
}
