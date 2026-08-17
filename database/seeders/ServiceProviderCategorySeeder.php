<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceProviderCategorySeeder extends Seeder
{
    public function run(): void
    {
        $providers = ServiceProvider::with('user')->get();

        $categories = Category::with('translations')->get();

        $providerCategories = [

            'ahmad.khalil@gmail.com' => [
                'Photographer',
            ],

            'lina.hassan@gmail.com' => [
                'Makeup Artist',
                'Photographer',
                'Desserts'
            ],

            'omar.saleh@gmail.com' => [
                'Decoration',
            ],

            'sara.ahmad@gmail.com' => [
                'Venue',
            ],

            'khaled.nasser@gmail.com' => [
                'Music & DJ',
            ],

            'maya.ibrahim@gmail.com' => [
                'Desserts',
            ],

            'yazan.mahmoud@gmail.com' => [
                'Catering',
            ],

            'rana.samir@gmail.com' => [
                'Car Rental',
            ],

            'tarek.ibrahim@gmail.com' => [
                'Photographer',
                'Music & DJ',
            ],

            'nour.ali@gmail.com' => [
                'Decoration',
                'Venue',
                'Catering'
            ],
        ];

        foreach ($providerCategories as $email => $categoryNames) {

            $provider = $providers->first(
                fn ($provider) =>
                    $provider->user &&
                    $provider->user->email === $email
            );

            if (!$provider) {
                continue;
            }

            foreach ($categoryNames as $categoryName) {

                $category = $categories->first(
                    fn ($category) =>
                        $category->translations
                            ->where('locale', 'en')
                            ->where('name', $categoryName)
                            ->isNotEmpty()
                );

                if (!$category) {
                    continue;
                }

                DB::table('service_provider_categories')->updateOrInsert(
                    [
                        'service_provider_id' => $provider->id,
                        'category_id' => $category->id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}