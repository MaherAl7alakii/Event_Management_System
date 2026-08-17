<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceOfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [

            'Wedding Photography Package' => [
                'discount' => 20,
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],

            'Bridal Makeup' => [
                'discount' => 15,
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],

            'Wedding Decoration' => [
                'discount' => 10,
                'start_date' => '2026-09-05',
                'end_date' => '2026-09-25',
                'is_active' => true,
            ],

            'Wedding Hall' => [
                'discount' => 15,
                'start_date' => '2026-09-01',
                'end_date' => '2026-10-01',
                'is_active' => true,
            ],

            'DJ and Sound System' => [
                'discount' => 20,
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],

            'Custom Wedding Cake' => [
                'discount' => 10,
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],

            'Wedding Buffet' => [
                'discount' => 12,
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],

            'Luxury Car Rental' => [
                'discount' => 15,
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],

            'Event Photography' => [
                'discount' => 10,
                'start_date' => '2026-09-10',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],

            'Event Decoration' => [
                'discount' => 10,
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-30',
                'is_active' => true,
            ],
        ];

        foreach ($offers as $title => $data) {

            $service = Service::whereHas('translations', function ($query) use ($title) {
                $query->where('locale', 'en')
                    ->where('title', $title);
            })->first();

            if (!$service) {
                continue;
            }

            $originalPrice = $service->base_price;

            $offerPrice = $originalPrice -
                ($originalPrice * $data['discount'] / 100);

            $service->offer()->updateOrCreate(
                [
                    'service_id' => $service->id,
                ],
                [
                    'discount' => $data['discount'],
                    'original_price' => $originalPrice,
                    'offer_price' => $offerPrice,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'is_active' => $data['is_active'],
                ]
            );
        }
    }
}