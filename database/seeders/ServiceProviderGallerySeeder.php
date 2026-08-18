<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;

class ServiceProviderGallerySeeder extends Seeder
{
    public function run(): void
    {
        $galleries = [

            'ahmad.khalil@gmail.com' => [
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Wedding Photography',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968142/Photographer.jpg',
                ],
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Outdoor Wedding Session',
                    'path' => 'PUT_CLOUDINARY_URL_HEREhttps://res.cloudinary.com/kzrnsaw4/image/upload/v1786968143/Photographer2.jpg',
                ],
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Outdoor Wedding Session',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968143/Photographer3.jpg',
                ],
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Outdoor Wedding Session',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968144/Photographer4.jpg',
                ],
            ],

            'lina.hassan@gmail.com' => [
                [
                    'category' => 'Makeup Artist',
                    'type' => 'image',
                    'title' => 'Bridal Makeup',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968137/Makeup_Artist.jpg',
                ],
                [
                    'category' => 'Makeup Artist',
                    'type' => 'image',
                    'title' => 'Evening Makeup',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968138/Makeup_Artist2.jpg',
                ],
                [
                    'category' => 'Makeup Artist',
                    'type' => 'image',
                    'title' => 'Evening Makeup',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968138/Makeup_Artist3.jpg',
                ],
                [
                    'category' => 'Makeup Artist',
                    'type' => 'image',
                    'title' => 'Evening Makeup',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968139/Makeup_Artist4.jpg',
                ],
                [
                    'category' => 'Makeup Artist',
                    'type' => 'image',
                    'title' => 'Evening Makeup',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000829/makeup4.jpg',
                ],
                [
                    'category' => 'Makeup Artist',
                    'type' => 'image',
                    'title' => 'Evening Makeup',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000827/makeup.jpg',
                ],
                 [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Photographer',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000829/Photographer.avif',
                ],
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Photographer',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000830/Photographer2.avif',
                ],
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Photographer',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000831/Photographer4.avif',
                ],
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Photographer',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000830/Photographer3.avif',
                ],
            ],

            'omar.saleh@gmail.com' => [
                [
                    'category' => 'Decoration',
                    'type' => 'image',
                    'title' => 'Wedding Decoration',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968126/decoration.jpg',
                ],
                [
                    'category' => 'Decoration',
                    'type' => 'image',
                    'title' => 'Floral Decoration',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968127/Decoration2.jpg',
                ],
            ],

            'sara.ahmad@gmail.com' => [
                [
                    'category' => 'Venue',
                    'type' => 'image',
                    'title' => 'Wedding Hall',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968132/Event_Hall.jpg',
                ],
                [
                    'category' => 'Venue',
                    'type' => 'image',
                    'title' => 'Event Hall',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968133/Event_Hall2.jpg',
                ],
            ],

            'khaled.nasser@gmail.com' => [
                [
                    'category' => 'Music & DJ',
                    'type' => 'image',
                    'title' => 'DJ Setup',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968140/Music_DJ.jpg',
                ],
                [
                    'category' => 'Music & DJ',
                    'type' => 'image',
                    'title' => 'Event Sound System',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968141/Music_DJ2.jpg',
                ],
            ],

            'maya.ibrahim@gmail.com' => [
                [
                    'category' => 'Desserts',
                    'type' => 'image',
                    'title' => 'Wedding Cake',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968131/desserts.jpg',
                ],
                [
                    'category' => 'Desserts',
                    'type' => 'image',
                    'title' => 'Dessert Table',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968131/Desserts2.jpg',
                ],
            ],

            'yazan.mahmoud@gmail.com' => [
                [
                    'category' => 'Catering',
                    'type' => 'image',
                    'title' => 'Wedding Buffet',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968125/catering.jpg',
                ],
                [
                    'category' => 'Catering',
                    'type' => 'image',
                    'title' => 'Event Catering',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968125/Catering2.jpg',
                ],
            ],

            'rana.samir@gmail.com' => [
                [
                    'category' => 'Car Rental',
                    'type' => 'image',
                    'title' => 'Luxury Wedding Car',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968123/Car_Rental.jpg',
                ],
                [
                    'category' => 'Car Rental',
                    'type' => 'image',
                    'title' => 'Premium Car',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968124/Car_Rental2.jpg',
                ],
            ],

            'tarek.ibrahim@gmail.com' => [
                [
                    'category' => 'Photographer',
                    'type' => 'image',
                    'title' => 'Wedding Photography',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968143/Photographer2.jpg',
                ],
                [
                    'category' => 'Music & DJ',
                    'type' => 'image',
                    'title' => 'DJ Performance',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968140/Music_DJ.jpg',
                ],
            ],

            'nour.ali@gmail.com' => [
                [
                    'category' => 'Decoration',
                    'type' => 'image',
                    'title' => 'Event Decoration',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968130/Decorationn3.jpg',
                ],
                [
                    'category' => 'Decoration',
                    'type' => 'image',
                    'title' => 'Event Decoration',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968129/Decorationn2.jpg',
                ],
                [
                    'category' => 'Decoration',
                    'type' => 'image',
                    'title' => 'Event Decoration',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968128/Decorationn.jpg',
                ],
                [
                    'category' => 'Venue',
                    'type' => 'image',
                    'title' => 'Event Venue',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968136/Event_Venue3.jpg',
                ],
                [
                    'category' => 'Venue',
                    'type' => 'image',
                    'title' => 'Event Venue',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968135/Event_Venue2.jpg',
                ],
                [
                    'category' => 'Venue',
                    'type' => 'image',
                    'title' => 'Event Venue',
                    'path' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968134/Event_Venue.jpg',
                ],
            ],
        ];

        foreach ($galleries as $email => $items) {

            $provider = ServiceProvider::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })->first();

            if (!$provider) {
                continue;
            }

            $provider->galleries()->delete();

            foreach ($items as $item) {

                $category = Category::whereHas('translations', function ($query) use ($item) {
                    $query->where('locale', 'en')
                        ->where('name', $item['category']);
                })->first();

                if (!$category) {
                    continue;
                }

                $provider->galleries()->create([
                    'category_id' => $category->id,
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'path' => $item['path'],
                ]);
            }
        }
    }
}