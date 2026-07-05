<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = [
            'Damascus'     => 'دمشق',
            'Rif Damascus' => 'ريف دمشق',
            'Idlib'        => 'إدلب',
            'Aleppo'       => 'حلب',
            'Daraa'        => 'درعا',
            'Homs'         => 'حمص',
            'Hama'         => 'حماة',
            'Deir ez-Zor'  => 'دير الزور',
            'Raqqa'        => 'الرقة',
            'Hasakah'      => 'الحسكة',
            'Quneitra'     => 'القنيطرة',
            'Latakia'      => 'اللاذقية',
            'Tartus'       => 'طرطوس',
            'As-Suwayda'   => 'السويداء'
        ];

        foreach ($governorates as $nameEn => $nameAr) {

            $governorateId = DB::table('governorates')->insertGetId([
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            DB::table('governorate_translations')->insert([
                ['governorate_id' => $governorateId, 'locale' => 'ar', 'name' => $nameAr],
                ['governorate_id' => $governorateId, 'locale' => 'en', 'name' => $nameEn],
            ]);
        }
    }
}
