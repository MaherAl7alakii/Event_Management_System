<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [

            // Damascus
            1 => ['governorate_id' => 1,'en' => 'Damascus','ar' => 'دمشق',],
            2 => ['governorate_id' => 1,'en' => 'Mazzeh','ar' => 'المزة',],
            3 => ['governorate_id' => 1,'en' => 'Kafr Sousa','ar' => 'كفرسوسة',],
            4 => ['governorate_id' => 1,'en' => 'Barzeh','ar' => 'برزة',],

            // Rif Damascus
            5 => ['governorate_id' => 2,'en' => 'Douma','ar' => 'دوما',],
            6 => ['governorate_id' => 2,'en' => 'Harasta','ar' => 'حرستا',],
            7 => ['governorate_id' => 2,'en' => 'Darayya','ar' => 'داريا',],

            // Idlib
            8 => ['governorate_id' => 3,'en' => 'Idlib','ar' => 'إدلب',],
            9 => ['governorate_id' => 3,'en' => 'Maarrat al-Numan','ar' => 'معرة النعمان',],
            10 => ['governorate_id' => 3,'en' => 'Saraqib','ar' => 'سراقب',],
            11 => ['governorate_id' => 3,'en' => 'Ariha','ar' => 'أريحا',],

            // Aleppo
            12 => ['governorate_id' => 4,'en' => 'Aleppo','ar' => 'حلب',],
            13 => ['governorate_id' => 4,'en' => 'Azaz','ar' => 'أعزاز',],
            14 => ['governorate_id' => 4,'en' => 'Manbij','ar' => 'منبج',],

            // Daraa
            15 => ['governorate_id' => 5,'en' => 'Daraa','ar' => 'درعا',],
            16 => ['governorate_id' => 5,'en' => 'Nawa','ar' => 'نوى',],
            17 => ['governorate_id' => 5,'en' => 'Izra','ar' => 'إزرع',],
            18 => ['governorate_id' => 5,'en' => 'Busra al-Sham','ar' => 'بصرى الشام',],

            // Homs
            19 => ['governorate_id' => 6,'en' => 'Homs','ar' => 'حمص',],
            20 => ['governorate_id' => 6,'en' => 'Al-Waer','ar' => 'الوعر',],
            21 => ['governorate_id' => 6,'en' => 'Inshaat','ar' => 'الإنشاءات',],

            // Hama
            22 => ['governorate_id' => 7,'en' => 'Hama','ar' => 'حماة',],
            23 => ['governorate_id' => 7,'en' => 'Salamiyah','ar' => 'السلمية',],
            24 => ['governorate_id' => 7,'en' => 'Mhardeh','ar' => 'محردة',],

            // Deir ez-Zor
            25 => ['governorate_id' => 8,'en' => 'Deir ez-Zor','ar' => 'دير الزور',],
            26 => ['governorate_id' => 8,'en' => 'Al-Mayadin','ar' => 'الميادين',],
            27 => ['governorate_id' => 8,'en' => 'Al-Bukamal','ar' => 'البوكمال',],

            // Raqqa
            28 => ['governorate_id' => 9,'en' => 'Raqqa','ar' => 'الرقة',],
            29 => ['governorate_id' => 9,'en' => 'Al-Thawrah','ar' => 'الثورة',],
            30 => ['governorate_id' => 9,'en' => 'Tal Abyad','ar' => 'تل أبيض',],

            // Hasakah
            31 => ['governorate_id' => 10,'en' => 'Qamishli','ar' => 'القامشلي',],
            32 => ['governorate_id' => 10,'en' => 'Al-Malikiyah','ar' => 'المالكية',],
            33 => ['governorate_id' => 10,'en' => 'Ras al-Ayn','ar' => 'رأس العين',],

            // Quneitra
            34 => ['governorate_id' => 11,'en' => 'Khan Arnabeh','ar' => 'خان أرنبة',],
            35 => ['governorate_id' => 11,'en' => 'Jaba','ar' => 'جبا',],

            // Latakia
            36 => ['governorate_id' => 12,'en' => 'Latakia','ar' => 'اللاذقية',],
            37 => ['governorate_id' => 12,'en' => 'Jableh','ar' => 'جبلة',],
            38 => ['governorate_id' => 12,'en' => 'Qardaha','ar' => 'القرداحة',],

            // Tartus
            39 => ['governorate_id' => 13,'en' => 'Tartus','ar' => 'طرطوس',],
            40 => ['governorate_id' => 13,'en' => 'Baniyas','ar' => 'بانياس',],
            41 => ['governorate_id' => 13,'en' => 'Safita','ar' => 'صافيتا',],

            // As-Suwayda
            42 => ['governorate_id' => 14,'en' => 'As-Suwayda','ar' => 'السويداء',],
            43 => ['governorate_id' => 14,'en' => 'Shahba','ar' => 'شهبا',],
            44 => ['governorate_id' => 14,'en' => 'Salkhad','ar' => 'صلخد',],
        ];

        foreach ($cities as $id => $data) {

            DB::table('cities')->updateOrInsert(
                ['id' => $id],
                [
                    'id'             => $id,
                    'governorate_id'  => $data['governorate_id'],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]
            );

            DB::table('city_translations')->updateOrInsert(
                [
                    'city_id' => $id,
                    'locale'  => 'ar',
                ],
                [
                    'name' => $data['ar'],
                ]
            );

            DB::table('city_translations')->updateOrInsert(
                [
                    'city_id' => $id,
                    'locale'  => 'en',
                ],
                [
                    'name' => $data['en'],
                ]
            );
        }
    }
}