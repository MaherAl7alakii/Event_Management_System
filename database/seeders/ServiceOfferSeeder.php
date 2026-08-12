<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = DB::table('services')
            ->where('is_active', true)
            ->get();

        if ($services->isEmpty()) {
            return;
        }

        foreach ($services as $service) {

            // مو كل الخدمات لازم يكون عليها عرض
            if (rand(1, 100) > 60) {
                continue;
            }

            $offerExists = DB::table('service_offers')
                ->where('service_id', $service->id)
                ->exists();

            if ($offerExists) {
                continue;
            }

            $discount = collect([10, 15, 20, 25, 30, 35])->random();

            $originalPrice = (float) $service->base_price;

            $offerPrice = round(
                $originalPrice - ($originalPrice * $discount / 100),
                2
            );

            /*
             * نوزع العروض بين:
             * - عروض فعالة حالياً
             * - عروض مستقبلية
             * - عروض منتهية
             */

            $offerType = rand(1, 3);

            if ($offerType === 1) {

                // عرض فعال حالياً
                $startDate = Carbon::today()->subDays(rand(1, 10));
                $endDate = Carbon::today()->addDays(rand(5, 30));

                $isActive = true;

            } elseif ($offerType === 2) {

                // عرض مستقبلي
                $startDate = Carbon::today()->addDays(rand(3, 15));
                $endDate = $startDate->copy()->addDays(rand(7, 30));

                $isActive = true;

            } else {

                // عرض منتهي
                $endDate = Carbon::today()->subDays(rand(1, 15));
                $startDate = $endDate->copy()->subDays(rand(7, 30));

                $isActive = true;
            }

            DB::table('service_offers')->insert([
                'service_id'     => $service->id,
                'discount'       => $discount,
                'original_price' => $originalPrice,
                'offer_price'    => $offerPrice,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'is_active'      => $isActive,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}