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
        $type = $this->faker->randomElement(['image', 'video']);

        return [
            'type'  => $type,
            'title' => $this->faker->realText(30),
            'url'   => $type === 'image'
                ? $this->faker->imageUrl(640, 480, 'events', true)
                : 'https://example.com/portfolio/event-video.mp4',
        ];
    }
}
