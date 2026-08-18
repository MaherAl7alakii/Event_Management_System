<?php

namespace Database\Seeders;

use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $portfolios = [

            'ahmad.khalil@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Wedding Photography',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961968/Wedding_Photography.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Outdoor Wedding Session',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961965/Outdoor_Wedding_Session.avif',
                ],
                [
                    'type' => 'image',
                    'title' => 'Bride and Groom Portrait',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961962/Bride_and_Groom_Portrait.jpg',
                ],
            ],

            'lina.hassan@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Bridal Makeup',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961962/Bridal_Makeup.avif',
                ],
                [
                    'type' => 'image',
                    'title' => 'Evening Makeup',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961963/Evening_Makeup.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Natural Makeup Look',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961964/Natural_Makeup_Look.jpg',
                ],
            ],

            'omar.saleh@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Wedding Decoration',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961967/Wedding_Decoration.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Elegant Event Setup',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961962/Elegant_Event_Setup.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Floral Wedding Decoration',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961963/Floral_Wedding_Decoration.jpg',
                ],
            ],

            'sara.ahmad@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Luxury Wedding Venue',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961964/Luxury_Wedding_Venue.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Outdoor Event Venue',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961965/Outdoor_Event_Venue.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Elegant Celebration Hall',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961962/Elegant_Celebration_Hall.webp',
                ],
            ],

            'khaled.nasser@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Wedding DJ Setup',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961968/Wedding_DJ_Setup.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Wedding Entertainment',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961968/Wedding_Entertainment.webp',
                ],
                [
                    'type' => 'image',
                    'title' => 'Professional Sound System',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961966/Professional_Sound_System.jpg',
                ],
            ],

            'maya.ibrahim@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Wedding Cake',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961966/Wedding_Cake.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Custom Dessert Table',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961962/Custom_Dessert_Table.webp',
                ],
                [
                    'type' => 'image',
                    'title' => 'Wedding Cupcake Collection',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961967/Wedding_Cupcake_Collection.jpg',
                ],
            ],

            'yazan.mahmoud@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Wedding Buffet',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961966/Wedding_Buffet.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Event Catering Setup',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961963/Event_Catering_Setup.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Elegant Dinner Service',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961962/Elegant_Dinner_Service.jpg',
                ],
            ],

            'rana.samir@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Luxury Wedding Car',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961964/Luxury_Wedding_Car.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961963/Event_Transportation.jpg',
                    'url' => 'REPLACE_WITH_RANA_PORTFOLIO_IMAGE_2',
                ],
                [
                    'type' => 'image',
                    'title' => 'Luxury Car Collection',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961963/Luxury_Car_Collection.jpg',
                ],
            ],

            'tarek.ibrahim@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Wedding DJ Performance',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961967/Wedding_DJ_Performance.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Live Event Performance',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961963/Live_Event_Performance.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Professional DJ Setup',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961965/Professional_DJ_Setup.jpg',
                ],
            ],

            'nour.ali@gmail.com' => [
                [
                    'type' => 'image',
                    'title' => 'Decoration',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961961/decoration.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Elegant Venue Setup',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961962/Elegant_Venue_Setup.jpg',
                ],
                [
                    'type' => 'image',
                    'title' => 'Wedding Floral Design',
                    'url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786961963/Floral_Wedding_Decoration.jpg',
                ],
            ],
        ];

        foreach ($portfolios as $email => $items) {

            $provider = ServiceProvider::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })->first();

            if (!$provider) {
                continue;
            }

            // منع التكرار عند إعادة تشغيل Seeder
            $provider->portfolios()->delete();

            foreach ($items as $item) {
                $provider->portfolios()->create([
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'url' => $item['url'],
                ]);
            }
        }
    }
}