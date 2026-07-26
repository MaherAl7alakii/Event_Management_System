<?php

namespace Database\Seeders;

use App\Models\WorkingHour;
use Illuminate\Database\Seeder;

class WorkingHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providerIds = range(1, 11);
        $daysOfWeek = range(0, 6);

        $rows = [];

        foreach ($providerIds as $providerId) {
            foreach ($daysOfWeek as $day) {
                $isFriday = ($day === 5);

                $rows[] = [
                    'service_provider_id' => $providerId,
                    'day_of_week'          => $day,
                    'is_active'            => ! $isFriday,
                    'start_time'           => $isFriday ? '00:00:00' : '09:00:00',
                    'end_time'             => $isFriday ? '00:00:00' : '23:00:00',
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
            }
        }


        WorkingHour::upsert(
            $rows,
            ['service_provider_id', 'day_of_week'],
            ['is_active', 'start_time', 'end_time', 'updated_at']
        );
    }
}
