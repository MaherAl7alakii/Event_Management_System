<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = [
            1  => ['en' => 'Damascus',     'ar' => 'دمشق'],
            2  => ['en' => 'Rif Damascus', 'ar' => 'ريف دمشق'],
            3  => ['en' => 'Idlib',        'ar' => 'إدلب'],
            4  => ['en' => 'Aleppo',       'ar' => 'حلب'],
            5  => ['en' => 'Daraa',        'ar' => 'درعا'],
            6  => ['en' => 'Homs',         'ar' => 'حمص'],
            7  => ['en' => 'Hama',         'ar' => 'حماة'],
            8  => ['en' => 'Deir ez-Zor',  'ar' => 'دير الزور'],
            9  => ['en' => 'Raqqa',        'ar' => 'الرقة'],
            10 => ['en' => 'Hasakah',      'ar' => 'الحسكة'],
            11 => ['en' => 'Quneitra',     'ar' => 'القنيطرة'],
            12 => ['en' => 'Latakia',      'ar' => 'اللاذقية'],
            13 => ['en' => 'Tartus',       'ar' => 'طرطوس'],
            14 => ['en' => 'As-Suwayda',   'ar' => 'السويداء'],
        ];

        foreach ($governorates as $id => $data) {

            DB::table('governorates')->updateOrInsert(
                ['id' => $id],
                [
                    'id'         => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('governorate_translations')->updateOrInsert(
                [
                    'governorate_id' => $id,
                    'locale'         => 'ar',
                ],
                [
                    'name' => $data['ar'],
                ]
            );

            DB::table('governorate_translations')->updateOrInsert(
                [
                    'governorate_id' => $id,
                    'locale'         => 'en',
                ],
                [
                    'name' => $data['en'],
                ]
            );
        }
    }
}