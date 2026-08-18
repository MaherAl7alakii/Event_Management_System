<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'en' => 'Wedding',
                'ar' => 'Wedding',
            ],
            [
                'en' => 'Engagement',
                'ar' => 'Engagement',
            ],
            [
                'en' => 'Birthday',
                'ar' => 'Birthday',
            ],
            [
                'en' => 'Graduation',
                'ar' => 'Graduation',
            ],
            [
                'en' => 'Corporate Event',
                'ar' => 'Corporate Event',
            ],
            [
                'en' => 'Private Party',
                'ar' => 'Private Party',
            ],
            [
                'en' => 'Baby Shower',
                'ar' => 'Baby Shower',
            ],
            [
                'en' => 'Anniversary',
                'ar' => 'Anniversary',
            ],
        ];

        foreach ($types as $type) {

            $eventType = EventType::create();

            DB::table('event_type_translations')->insert([
                [
                    'event_type_id' => $eventType->id,
                    'locale' => 'en',
                    'name' => $type['en'],
                ],
                [
                    'event_type_id' => $eventType->id,
                    'locale' => 'ar',
                    'name' => $type['ar'],
                ],
            ]);
        }
    }
}