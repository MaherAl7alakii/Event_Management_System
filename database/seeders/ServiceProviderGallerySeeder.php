<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceProviderGallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableImages = [
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239817/photographer-a-beginners-guide-to-taking-amazing-photos-2_l4xr0h.webp',
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239464/6uhoF_hyijfk.jpg',
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783240185/images_1_rbc9yx.jpg',
            'https://res.cloudinary.com/dgxlkhjw6/image/upload/v1755098151/photography_stock-770x365_nhpgqt.jpg',
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239287/27f5946583ff1bb4226a742b49d15dd0_fgh7k8.jpg',
            'https://res.cloudinary.com/dqf3h5hcs/image/upload/v1783239661/thumbnail_fndk-aano-n-lhmr_l1gCKqCe_w5ohtv.jpg',
        ];

        $availableVideos = [
            'https://res.cloudinary.com/dqf3h5hcs/video/upload/v1783239817/event-video-1.mp4',
            'https://res.cloudinary.com/dqf3h5hcs/video/upload/v1783239464/event-video-2.mp4',
        ];

        $titles = [
            'تصوير حفلات ومناسبات',
            'تجهيز وتنظيم المناسبات',
            'قاعة مناسبات فاخرة',
            'ديكور وتنسيق حفلات',
            'خدمات ضيافة وبوفيه',
            'موسيقى ودي جي',
            'جلسات تصوير احترافية',
            'سيارات فاخرة للمناسبات',
        ];

       
        $providers = DB::table('service_providers')
            ->pluck('id')
            ->toArray();

        
        $categoryIds = DB::table('categories')
            ->pluck('id')
            ->toArray();

        if (empty($providers) || empty($categoryIds)) {
            return;
        }

        foreach ($providers as $providerId) {

           
            $galleryCount = rand(4, 8);

            for ($i = 1; $i <= $galleryCount; $i++) {
                $type = rand(1, 100) <= 80
                    ? 'image'
                    : 'video';

                if ($type === 'image') {
                    $path = collect($availableImages)->random();
                } else {
                    $path = collect($availableVideos)->random();
                }

                /*
                 * أحياناً نخلي العنصر بدون Category
                 * حتى نختبر category_id = null
                 */
                $categoryId = rand(1, 100) <= 85
                    ? collect($categoryIds)->random()
                    : null;

                DB::table('service_provider_galleries')->insert([
                    'service_provider_id' => $providerId,
                    'category_id'         => $categoryId,
                    'type'                => $type,
                    'title'               => collect($titles)->random(),
                    'path'                => $path,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }
}