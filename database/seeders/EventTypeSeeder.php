<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            [
                'ar'   => 'حفل زفاف',
                'en'   => 'Wedding Party'
            ],
            [
                'ar'   => 'حفل تخرج',
                'en'   => 'Graduation Ceremony'
            ],
            [
                'ar'   => 'عيد ميلاد',
                'en'   => 'Birthday Party'
            ],
            [
                'ar'   => 'حفل استقبال مولود',
                'en'   => 'Baby Shower'
            ],
            [
                'ar'   => 'أخرى',
                'en'   => 'Other'
            ],
        ];

        foreach ($eventTypes as $type) {
            $eventTypeId = DB::table('event_types')->insertGetId([
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            DB::table('event_type_translations')->insert([
                [
                    'event_type_id' => $eventTypeId,
                    'locale'        => 'ar',
                    'name'          => $type['ar'],
                ],
                [
                    'event_type_id' => $eventTypeId,
                    'locale'        => 'en',
                    'name'          => $type['en'],
                ]
            ]);
        }
    }
}
