<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [

            [
                'Wedding Photography Package',
                'Wedding Decoration',
            ],

            [
                'Wedding Photography Package',
                'Bridal Makeup',
            ],

            [
                'Wedding Photography Package',
                'Wedding Hall',
            ],

            [
                'Wedding Hall',
                'Wedding Buffet',
            ],

            [
                'Wedding Hall',
                'Wedding DJ Package',
            ],

            [
                'Wedding Decoration',
                'Wedding Hall',
            ],

            [
                'Bridal Makeup',
                'Custom Wedding Cake',
            ],

            [
                'Wedding DJ Package',
                'Wedding Buffet',
            ],

            [
                'Custom Wedding Cake',
                'Dessert Table',
            ],

            [
                'Event Decoration',
                'Outdoor Event Venue',
            ],
        ];

        foreach ($links as [$serviceTitle, $linkedTitle]) {

            $service = $this->findService($serviceTitle);
            $linkedService = $this->findService($linkedTitle);

            if (!$service || !$linkedService) {
                continue;
            }

            // ممنوع الخدمة تربط نفسها بنفسها
            if ($service->id === $linkedService->id) {
                continue;
            }

            DB::table('service_links')->updateOrInsert(
                [
                    'service_id' => $service->id,
                    'linked_service_id' => $linkedService->id,
                ],
                [
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function findService(string $title): ?Service
    {
        return Service::whereHas('translations', function ($query) use ($title) {
            $query->where('locale', 'en')
                ->where('title', $title);
        })->first();
    }
}