<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $userIds = DB::table('users')
            ->pluck('id')
            ->toArray();

        
        $providerIds = DB::table('service_providers')
            ->pluck('id')
            ->toArray();

        if (empty($userIds) || empty($providerIds)) {
            return;
        }

        $comments = [
            'خدمة ممتازة والتعامل كان رائع جداً.',
            'تجربة جميلة جداً وأنصح بالتعامل معهم.',
            'الخدمة كانت جيدة والتنظيم ممتاز.',
            'تعامل احترافي وسرعة في تنفيذ الطلب.',
            'النتيجة كانت أفضل مما توقعت.',
            'خدمة جيدة جداً والأسعار مناسبة.',
            'التجربة كانت رائعة من البداية حتى النهاية.',
            'المزود كان متعاوناً ومحترفاً جداً.',
            'الخدمة ممتازة ولكن أتمنى تحسين وقت الاستجابة.',
            'تجربة جيدة وسأتعامل معهم مرة أخرى.',
        ];

       
        $existingReviews = [];

    
        foreach ($providerIds as $providerId) {

            $numberOfReviews = rand(3, 8);

            /*
             * نخلط المستخدمين حتى نختار Users مختلفين
             */
            $selectedUsers = collect($userIds)
                ->shuffle()
                ->take(min($numberOfReviews, count($userIds)));

            foreach ($selectedUsers as $userId) {

                $uniqueKey = $userId . '-' . $providerId;

                
                if (isset($existingReviews[$uniqueKey])) {
                    continue;
                }

                $existingReviews[$uniqueKey] = true;

              
                $rating = collect([
                    5, 5, 5,
                    4, 4, 4,
                    3, 3,
                    2,
                    1,
                ])->random();

                DB::table('reviews')->insert([
                    'user_id'            => $userId,
                    'service_provider_id' => $providerId,
                    'rating'             => $rating,
                    'comment'            => collect($comments)->random(),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }
}