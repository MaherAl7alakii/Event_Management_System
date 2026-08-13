<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories =[
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'مصور فوتوغرافي'],
                'en'   => ['name' => 'Photographer'],
            ],
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'قاعة فعاليات'],
                'en'   => ['name' => 'Venue'],
            ],
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'موسيقى ودي جي'],
                'en'   => ['name' => 'Music & DJ'],
            ],
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'زينة وديكور'],
                'en'   => ['name' => 'Decoration'],
            ],
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'حلويات وقوالب كيك'],
                'en'   => ['name' => 'Desserts'],
            ],
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'ضيافة وبوفيه'],
                'en'   => ['name' => 'Catering'],
            ],
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'خبيرة تجميل'],
                'en'   => ['name' => 'Makeup Artist'],
            ],
            [
                'icon' => 'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783347392/car-rental_uitffw.svg',
                'ar'   => ['name' => 'تأجير سيارات'],
                'en'   => ['name' => 'Car Rental'],
            ],
        ];

        foreach ($categories as $categoryData) {

            $categoryId = DB::table('categories')->insertGetId([
                'icon'       => $categoryData['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('category_translations')->insert([
                [
                    'category_id' => $categoryId,
                    'locale'      => 'ar',
                    'name'        => $categoryData['ar']['name']
                ],
                [
                    'category_id' => $categoryId,
                    'locale'      => 'en',
                    'name'        => $categoryData['en']['name']
                ],
            ]);
        }

    }
}
