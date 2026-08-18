<?php

namespace Database\Seeders;

use App\Models\ServiceProvider;
use App\Enums\TimeOffReason;
use App\Enums\TimeOffType;
use Illuminate\Database\Seeder;

class TimeOffSeeder extends Seeder
{
    public function run(): void
    {
        $timeOffs = [

            /*
            |--------------------------------------------------------------------------
            | Ahmad Khalil - Photographer
            |--------------------------------------------------------------------------
            */

            'ahmad.khalil@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-10',
                    'end_date' => '2026-09-12',
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => TimeOffReason::VACATION->value,
                    'note' => 'Short vacation.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-05',
                    'end_date' => '2026-10-05',
                    'start_time' => '13:00',
                    'end_time' => '17:00',
                    'reason' => TimeOffReason::PERSONAL->value,
                    'note' => 'Personal appointment.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Lina Hassan - Makeup Artist
            |--------------------------------------------------------------------------
            */

            'lina.hassan@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-15',
                    'end_date' => '2026-09-15',
                    'start_time' => '10:00',
                    'end_time' => '14:00',
                    'reason' => TimeOffReason::TRAINING->value,
                    'note' => 'Professional makeup training.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-20',
                    'end_date' => '2026-10-22',
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => TimeOffReason::VACATION->value,
                    'note' => 'Annual vacation.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Omar Saleh - Decoration
            |--------------------------------------------------------------------------
            */

            'omar.saleh@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-08',
                    'end_date' => '2026-09-08',
                    'start_time' => '09:00',
                    'end_time' => '13:00',
                    'reason' => TimeOffReason::PERSONAL->value,
                    'note' => 'Personal appointment.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-12',
                    'end_date' => '2026-10-12',
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'reason' => TimeOffReason::MAINTENANCE->value,
                    'note' => 'Decoration equipment maintenance.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Sara Ahmad - Venue
            |--------------------------------------------------------------------------
            */

            'sara.ahmad@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-14',
                    'end_date' => '2026-09-14',
                    'start_time' => '09:00',
                    'end_time' => '15:00',
                    'reason' => TimeOffReason::MAINTENANCE->value,
                    'note' => 'Venue maintenance and preparation.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-03',
                    'end_date' => '2026-10-03',
                    'start_time' => '09:00',
                    'end_time' => '14:00',
                    'reason' => TimeOffReason::MAINTENANCE->value,
                    'note' => 'Electrical and interior maintenance.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Khaled Nasser - Music & DJ
            |--------------------------------------------------------------------------
            */

            'khaled.nasser@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-18',
                    'end_date' => '2026-09-20',
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => TimeOffReason::VACATION->value,
                    'note' => 'Short vacation.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-10',
                    'end_date' => '2026-10-10',
                    'start_time' => '16:00',
                    'end_time' => '20:00',
                    'reason' => TimeOffReason::PERSONAL->value,
                    'note' => 'Personal commitment.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Maya Ibrahim - Desserts
            |--------------------------------------------------------------------------
            */

            'maya.ibrahim@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-07',
                    'end_date' => '2026-09-07',
                    'start_time' => '09:00',
                    'end_time' => '13:00',
                    'reason' => TimeOffReason::TRAINING->value,
                    'note' => 'Pastry and cake decoration training.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-25',
                    'end_date' => '2026-10-27',
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => TimeOffReason::VACATION->value,
                    'note' => 'Short vacation.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Yazan Mahmoud - Catering
            |--------------------------------------------------------------------------
            */

            'yazan.mahmoud@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-09',
                    'end_date' => '2026-09-09',
                    'start_time' => '07:00',
                    'end_time' => '12:00',
                    'reason' => TimeOffReason::MAINTENANCE->value,
                    'note' => 'Kitchen equipment maintenance.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-15',
                    'end_date' => '2026-10-17',
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => TimeOffReason::VACATION->value,
                    'note' => 'Annual vacation.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Rana Samir - Car Rental
            |--------------------------------------------------------------------------
            */

            'rana.samir@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-11',
                    'end_date' => '2026-09-11',
                    'start_time' => '08:00',
                    'end_time' => '13:00',
                    'reason' => TimeOffReason::MAINTENANCE->value,
                    'note' => 'Vehicle maintenance.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-08',
                    'end_date' => '2026-10-08',
                    'start_time' => '08:00',
                    'end_time' => '12:00',
                    'reason' => TimeOffReason::MAINTENANCE->value,
                    'note' => 'Routine vehicle inspection.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Tarek Ibrahim - Photographer + Music & DJ
            |--------------------------------------------------------------------------
            */

            'tarek.ibrahim@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-22',
                    'end_date' => '2026-09-24',
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => TimeOffReason::VACATION->value,
                    'note' => 'Short vacation.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-18',
                    'end_date' => '2026-10-18',
                    'start_time' => '14:00',
                    'end_time' => '18:00',
                    'reason' => TimeOffReason::PERSONAL->value,
                    'note' => 'Personal appointment.',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Nour Ali - Decoration + Venue
            |--------------------------------------------------------------------------
            */

            'nour.ali@gmail.com' => [
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-09-16',
                    'end_date' => '2026-09-16',
                    'start_time' => '09:00',
                    'end_time' => '14:00',
                    'reason' => TimeOffReason::MAINTENANCE->value,
                    'note' => 'Decoration equipment maintenance.',
                ],
                [
                    'type' => TimeOffType::TIME_OFF->value,
                    'start_date' => '2026-10-28',
                    'end_date' => '2026-10-30',
                    'start_time' => null,
                    'end_time' => null,
                    'reason' => TimeOffReason::VACATION->value,
                    'note' => 'Short vacation.',
                ],
            ],
        ];

        foreach ($timeOffs as $email => $items) {

            $provider = ServiceProvider::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })->first();

            if (!$provider) {
                continue;
            }

            // Prevent duplicate data when seeder runs again
            $provider->timeOffs()->delete();

            foreach ($items as $item) {

                $provider->timeOffs()->create([
                    'type' => $item['type'],
                    'start_date' => $item['start_date'],
                    'end_date' => $item['end_date'],
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'reason' => $item['reason'],
                    'note' => $item['note'],
                ]);
            }
        }
    }
}