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

        $categories = [
            [
                'icon' => 'camera',
                'ar'   => ['name' => 'مصور فوتوغرافي'],
                'en'   => ['name' => 'Photographer'],
            ],
            [
                'icon' => 'venue',
                'ar'   => ['name' => 'قاعة فعاليات'],
                'en'   => ['name' => 'Venue'],
            ],
            [
                'icon' => 'music',
                'ar'   => ['name' => 'موسيقى ودي جي'],
                'en'   => ['name' => 'Music & DJ'],
            ],
            [
                'icon' => 'decoration',
                'ar'   => ['name' => 'زينة وديكور'],
                'en'   => ['name' => 'Decoration'],
            ],
            [
                'icon' => 'dessert',
                'ar'   => ['name' => 'حلويات وقوالب كيك'],
                'en'   => ['name' => 'Desserts'],
            ],
            [
                'icon' => 'catering',
                'ar'   => ['name' => 'ضيافة وبوفيه'],
                'en'   => ['name' => 'Catering'],
            ],
            [
                'icon' => 'makeup',
                'ar'   => ['name' => 'خبيرة تجميل'],
                'en'   => ['name' => 'Makeup Artist'],
            ],
            [
                'icon' => 'car',
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
