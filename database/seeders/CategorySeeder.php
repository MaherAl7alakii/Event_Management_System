<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [

            1 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998884/camera.png',
                'ar'   => 'مصور فوتوغرافي',
                'en'   => 'Photographer',
            ],

            2 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998885/venue.png',
                'ar'   => 'قاعة فعاليات',
                'en'   => 'Venue',
            ],

            3 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998884/music.png',
                'ar'   => 'موسيقى ودي جي',
                'en'   => 'Music & DJ',
            ],

            4 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998885/decoration.png',
                'ar'   => 'زينة وديكور',
                'en'   => 'Decoration',
            ],

            5 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998884/cake.png',
                'ar'   => 'حلويات وقوالب كيك',
                'en'   => 'Desserts',
            ],

            6 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998884/catering.png',
                'ar'   => 'ضيافة وبوفيه',
                'en'   => 'Catering',
            ],

            7 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998884/makeup.png',
                'ar'   => 'خبيرة تجميل',
                'en'   => 'Makeup Artist',
            ],

            8 => [
                'icon' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786998884/car.png',
                'ar'   => 'تأجير سيارات',
                'en'   => 'Car Rental',
            ],
        ];

        foreach ($categories as $id => $data) {

            DB::table('categories')->updateOrInsert(
                ['id' => $id],
                [
                    'id'         => $id,
                    'icon'       => $data['icon'],
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('category_translations')->updateOrInsert(
                [
                    'category_id' => $id,
                    'locale'      => 'ar',
                ],
                [
                    'name' => $data['ar'],
                ]
            );

            DB::table('category_translations')->updateOrInsert(
                [
                    'category_id' => $id,
                    'locale'      => 'en',
                ],
                [
                    'name' => $data['en'],
                ]
            );
        }
    }
}

