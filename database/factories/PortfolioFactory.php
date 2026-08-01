<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Portfolio>
 */
class PortfolioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
//        $type = $this->faker->randomElement(['image', 'video']);
        $type = 'image';

        return [
            'type'  => $type,
            'title' => $this->faker->realText(30),
            'url'   => $type === 'image'
                ? 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239464/6uhoF_hyijfk.jpg'
                : 'https://example.com/portfolio/event-video.mp4',
        ];
    }
}
