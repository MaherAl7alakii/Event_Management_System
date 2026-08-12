<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        $providers = DB::table('service_providers')->get();

        if ($providers->isEmpty()) {
            return;
        }

        $packageNames = [
            'باقة المناسبات الذهبية',
            'باقة الحفل المتكاملة',
            'الباقة الأساسية',
            'الباقة المميزة',
            'باقة الحفل الفاخر',
            'باقة المناسبات الخاصة',
        ];

        $packageDescriptions = [
            'باقة متكاملة تشمل مجموعة من الخدمات المناسبة للمناسبات والاحتفالات.',
            'باقة مصممة لتوفير تجربة متكاملة للعميل مع مجموعة من الخدمات المميزة.',
            'مجموعة من الخدمات الأساسية بسعر مناسب لتلبية احتياجات المناسبة.',
            'باقة مميزة تجمع عدة خدمات احترافية ضمن عرض واحد.',
            'باقة فاخرة ومتكاملة للمناسبات الكبيرة والفعاليات الخاصة.',
            'حل متكامل للمناسبات مع إمكانية الاستفادة من عدة خدمات بسعر مخفض.',
        ];

        $packageImages = [
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239817/photographer-a-beginners-guide-to-taking-amazing-photos-2_l4xr0h.webp',
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239464/6uhoF_hyijfk.jpg',
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783240185/images_1_rbc9yx.jpg',
            'https://res.cloudinary.com/dgxlkhjw6/image/upload/v1755098151/photography_stock-770x365_nhpgqt.jpg',
        ];

        foreach ($providers as $provider) {

            $services = DB::table('services')
                ->where('provider_id', $provider->user_id)
                ->where('is_active', true)
                ->get();

        
            if ($services->count() < 2) {
                continue;
            }

            
            $packagesCount = rand(1, min(3, $services->count() - 1));

            for ($i = 0; $i < $packagesCount; $i++) {

                  $numberOfServices = rand(2,min(4, $services->count()));

                $selectedServices = $services
                    ->shuffle()
                    ->take($numberOfServices);

               
                $totalPrice = $selectedServices->sum(function ($service) {
                    return (float) $service->base_price;
                });

            
                $discount = collect([
                    10,
                    15,
                    20,
                    25,
                    30,
                ])->random();

                
                $finalPrice = round($totalPrice - ($totalPrice * $discount / 100),2);

                
                $status = rand(1, 100) <= 85
                    ? 'active'
                    : 'hidden';

                
                $packageId = DB::table('packages')->insertGetId([
                    'service_provider_id' => $provider->id,
                    'image'               => collect($packageImages)->random(),
                    'name'                => collect($packageNames)->random(),
                    'description'         => collect($packageDescriptions)->random(),
                    'total_price'         => $totalPrice,
                    'discount'            => $discount,
                    'final_price'          => $finalPrice,
                    'status'              => $status,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);

                /*
                 * ربط الخدمات بالـ Package
                 */
                $packageServices = [];

                foreach ($selectedServices as $service) {
                    $packageServices[] = [
                        'package_id' => $packageId,
                        'service_id' => $service->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('package_service')->insert($packageServices);
            }
        }
    }
}