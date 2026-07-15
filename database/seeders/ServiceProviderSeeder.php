<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = User::role('service_provider')->get();

        foreach ($providers as $provider) {


            $serviceProvider = ServiceProvider::factory()->create([
                'user_id' => $provider->id,
                'business_name' => "{$provider->name} company",
            ]);

            $serviceProvider->categories()->createMany([
                ['category_id' => 1],
                ['category_id' => 2],
            ]);

            $serviceProvider->documents()->createMany([
                ['url' => 'https://example.com/licenses/commercial-reg.pdf'],
                ['url' => 'https://example.com/licenses/tax-certificate.pdf'],
            ]);

            Portfolio::factory()
                ->count(3)
                ->create([
                    'service_provider_id' => $serviceProvider->id,
                ]);
        }
    }
}
