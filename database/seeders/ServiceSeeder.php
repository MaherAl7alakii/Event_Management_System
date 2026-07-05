<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableImages = collect([
            "https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239817/photographer-a-beginners-guide-to-taking-amazing-photos-2_l4xr0h.webp",
            "https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239464/6uhoF_hyijfk.jpg",
            "https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783240185/images_1_rbc9yx.jpg",
            "https://res.cloudinary.com/dgxlkhjw6/image/upload/v1755098151/photography_stock-770x365_nhpgqt.jpg",
            "https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239287/27f5946583ff1bb4226a742b49d15dd0_fgh7k8.jpg",
            "https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239661/thumbnail_fndk-aano-n-lhmr_l1gCKqCe_w5ohtv.jpg"
        ]);


        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        $cityIds = DB::table('cities')->pluck('id')->toArray();
        $pricingTypes = ['fixed', 'per_hour', 'per_person', 'per_hour_per_person'];


        $titles = [
            'ar' => ['تنسيق حفلات متكامل', 'جلسة تصوير احترافية', 'حجز قاعة فاخرة', 'خدمات ضيافة وبوفيه مفتوح', 'دي جي وإضاءة ليزرية', 'تأجير سيارات فخمة للمناسبات'],
            'en' => ['Full Event Planning', 'Professional Photography Session', 'Luxury Venue Booking', 'Catering & Open Buffet Services', 'DJ & Laser Lighting', 'Luxury Car Rental for Events']
        ];


        $providerIds = range(2, 11);

        foreach ($providerIds as $providerId) {

            $providerExists = DB::table('users')->where('id', $providerId)->exists();
            if (!$providerExists) {
                continue;
            }

            for ($i = 1; $i <= 10; $i++) {


                $pricingType = $pricingTypes[array_rand($pricingTypes)];
                $minHours = in_array($pricingType, ['per_hour', 'per_hour_per_person']) ? rand(2, 5) : null;
                $maxHours = $minHours ? $minHours + rand(3, 8) : null;
                $maxGuests = in_array($pricingType, ['per_person', 'per_hour_per_person']) ? rand(50, 500) : null;


                $rating = collect([1, 2, 3, 4, 5])->random() == 5 ? 5.00 : number_format(1 + (rand(0, 400) / 100), 2);
                $ratingCount = rand(5, 150);

                $reviewsCount = max(0, $ratingCount - rand(1, 10));
                $bookingsCount = $ratingCount + rand(20, 300);

                $serviceId = DB::table('services')->insertGetId([
                    'provider_id'    => $providerId,
                    'category_id'    => collect($categoryIds)->random(),
                    'city_id'        => collect($cityIds)->random(),
                    'pricing_type'   => $pricingType,
                    'base_price'     => rand(50, 1500),
                    'is_active'      => true,
                    'min_hours'      => $minHours,
                    'max_hours'      => $maxHours,
                    'max_guests'     => $maxGuests,
                    'rating'         => $rating,
                    'rating_count'   => $ratingCount,
                    'reviews_count'  => $reviewsCount,
                    'bookings_count' => $bookingsCount,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);


                $randIndex = array_rand($titles['ar']);

                DB::table('service_translations')->insert([
                    [
                        'service_id'  => $serviceId,
                        'locale'      => 'ar',
                        'title'       => $titles['ar'][$randIndex] . " #{$serviceId}",
                        'description' => "هذا النص هو وصف تجريبي وواقعي للخدمة المقدمة رقم {$serviceId}. يشمل العرض كافة التفاصيل المتفق عليها مع إمكانية التعديل حسب رغبة العميل وضمان جودة الخدمة والأداء الاحترافي.",
                        'address'     => "سوريا، المدينة المحددة، الشارع الرئيسي، بناء رقم " . rand(1, 50),
                    ],
                    [
                        'service_id'  => $serviceId,
                        'locale'      => 'en',
                        'title'       => $titles['en'][$randIndex] . " #{$serviceId}",
                        'description' => "This is a test description for the provided service number {$serviceId}. The offer includes all agreed details with the possibility of customization upon request, ensuring high-quality and professional delivery.",
                        'address'     => "Syria, Selected City, Main Street, Building No. " . rand(1, 50),
                    ]
                ]);

                $selectedImageUrl = $availableImages->random();

                $imagesData = [];
                for ($j = 1; $j <= 5; $j++) {
                    $imagesData[] = [
                        'service_id' => $serviceId,
                        'url'  => $selectedImageUrl,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('service_images')->insert($imagesData);
            }
        }
    }
}
