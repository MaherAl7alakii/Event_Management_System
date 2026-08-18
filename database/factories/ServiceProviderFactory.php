<?php

namespace Database\Factories;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;


class ServiceProviderFactory extends Factory
{
    protected $model = ServiceProvider::class;

    public function definition(): array
    {
        return [
            'city_id'             => fake()->numberBetween(1, 44),
            'account_type'        => fake()->randomElement(['individual', 'professional']),
            'business_name'       => fake()->company(),
            'avatar'              => null,
            'phone'               => '09' . fake()->numerify('########'),
            'address'             => fake()->address(),
            'approval_status'     => 'approved',
            'years_of_experience' => (string) fake()->numberBetween(1, 20),
            'description'         => fake()->paragraph(),
            'verified_at'         => now(),
            'rejection_reason'    => null,
            'stripe_account_id'   => 'acct_' . fake()->bothify('??????????'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'approval_status'   => 'pending',
            'verified_at'       => null,
            'rejection_reason'  => null,
            'stripe_account_id' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'approval_status'   => 'rejected',
            'verified_at'       => null,
            'rejection_reason'  => fake()->randomElement([
                'الوثائق المرفقة غير مكتملة',
                'معلومات النشاط التجاري غير صحيحة',
                'عدم مطابقة الشروط المطلوبة للانضمام',
            ]),
            'stripe_account_id' => null,
        ]);
    }
}
