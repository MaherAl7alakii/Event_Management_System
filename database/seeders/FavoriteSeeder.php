<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')
            ->pluck('id')
            ->toArray();

        $serviceIds = DB::table('services')
            ->pluck('id')
            ->toArray();

        $providerIds = DB::table('service_providers')
            ->pluck('id')
            ->toArray();

        if (empty($userIds)) {
            return;
        }

        foreach ($userIds as $userId) {

          
            $selectedServices = collect($serviceIds)
                ->shuffle()
                ->take(rand(2, min(5, count($serviceIds))));

            foreach ($selectedServices as $serviceId) {

                DB::table('favorites')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'favoritable_type' => \App\Models\Service::class,
                        'favoritable_id' => $serviceId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            
            if (!empty($providerIds)) {

                $selectedProviders = collect($providerIds)
                    ->shuffle()
                    ->take(rand(1, min(3, count($providerIds))));

                foreach ($selectedProviders as $providerId) {

                    DB::table('favorites')->updateOrInsert(
                        [
                            'user_id' => $userId,
                            'favoritable_type' => \App\Models\ServiceProvider::class,
                            'favoritable_id' => $providerId,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}