<?php

namespace Database\Factories;

use App\Models\ServiceProviderDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceProviderDocument>
 */
class ServiceProviderDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ServiceProviderDocument::class;

    public function definition(): array
    {
        return [
            'url' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1785587726/Doc_mzd5eb.pdf',
        ];
    }
}
