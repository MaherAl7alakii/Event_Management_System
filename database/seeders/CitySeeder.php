<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {

        $data = [
            'Damascus'     => ['Mazzeh' => 'المزة', 'Kafr Sousa' => 'كفرسوسة', 'Barzeh' => 'برزة'],
            'Rif Damascus' => ['Douma' => 'دوما', 'Harasta' => 'حرستا', 'Darayya' => 'داريا'],
            'Idlib'        => ['Maarrat al-Numan' => 'معرة النعمان', 'Saraqib' => 'سراقب', 'Ariha' => 'أريحة'],
            'Aleppo'       => ['Al-Shaar' => 'الشعار', 'Al-Hamdaniya' => 'الحمدانية', 'Salah al-Din' => 'صلاح الدين'],
            'Daraa'        => ['Nawa' => 'نوى', 'Izra' => 'إزرع', 'Busra al-Sham' => 'بصرى الشام'],
            'Homs'         => ['Al-Waer' => 'الوعر', 'Karm al-Zeitoun' => 'كرم الزيتون', 'Inshaat' => 'الإنشاءات'],
            'Hama'         => ['Al-Hader' => 'الحاضر', 'Al-Salamiyah' => 'السلمية', 'Mhardeh' => 'محردة'],
            'Deir ez-Zor'  => ['Al-Mayadin' => 'الميادين', 'Al-Bukamal' => 'البوكمال', 'Al-Quriyah' => 'القورية'],
            'Raqqa'        => ['Al-Thawrah' => 'الثورة', 'Al-Karamah' => 'الكرامة', 'Tal Abyad' => 'تل أبيض'],
            'Hasakah'      => ['Qamishli' => 'القامشلي', 'Al-Malikiyah' => 'المالكية', 'Ras al-Ayn' => 'رأس العين'],
            'Quneitra'     => ['Khan Arnabeh' => 'خان أرنبة', 'Baath City' => 'مدينة البعث', 'Jaba' => 'جبا'],
            'Latakia'      => ['Jableh' => 'جبلة', 'Qardaha' => 'القرداحة', 'Al-Haffah' => 'الحفة'],
            'Tartus'       => ['Baniyas' => 'بانياس', 'Safita' => 'صافيتا', 'Dreikish' => 'دريكيش'],
            'As-Suwayda'   => ['Shahba' => 'شهبا', 'Salkhad' => 'صلخد', 'Qanawat' => 'قنوات'],
        ];

        foreach ($data as $govNameEn => $cities) {

            $govTranslation = DB::table('governorate_translations')
                ->where('locale', 'en')
                ->where('name', $govNameEn)
                ->first();

            if (!$govTranslation) {
                continue;
            }

            $governorateId = $govTranslation->governorate_id;

            foreach ($cities as $cityNameEn => $cityNameAr) {

                $cityId = DB::table('cities')->insertGetId([
                    'governorate_id' => $governorateId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);


                DB::table('city_translations')->insert([
                    ['city_id' => $cityId, 'locale' => 'ar', 'name' => $cityNameAr],
                    ['city_id' => $cityId, 'locale' => 'en', 'name' => $cityNameEn],
                ]);
            }
        }
    }
}
