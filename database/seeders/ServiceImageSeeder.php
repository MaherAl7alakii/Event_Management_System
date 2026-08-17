<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [

            'Wedding Photography Package' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966227/Wedding_Photography_Package.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966227/Wedding_Photography_Package2.jpg',
            ],

            'Event Photography' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966212/Event_Photography.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966225/Wedding_DJ_Package.jpg',
            ],

            'Bridal Makeup' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000827/makeup.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000828/makeup2.jpg',
            ],

            'Event Makeup' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000828/makeup3.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000829/makeup4.jpg',
            ],

            'Wedding Decoration' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966223/Wedding_Decoration2.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966223/Wedding_Decoration.jpg',
            ],

            'Floral Decoration Service' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966213/Floral_Decoration_Service.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966214/Floral_Decoration_Service2.jpg',
            ],

            'Wedding Hall' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966226/Wedding_Hall.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966226/Wedding_Hall2.jpg',
            ],

            'Private Event Hall' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966217/Private_Event_Hall.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966218/Private_Event_Hall2.jpg',
            ],

            'DJ and Sound System' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966210/DJ_and_Sound_System2.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966208/DJ_and_Sound_System.jpg',
            ],

            'Wedding DJ Package' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966225/Wedding_DJ_Package.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966225/Wedding_DJ_Package2.jpg',
            ],

            'Custom Wedding Cake' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966204/Custom_Wedding_Cake.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966204/Custom_Wedding_Cake2.jpg',
            ],

            'Dessert Table' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966205/Dessert_Table.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966206/Dessert_Table2.jpg',
            ],

            'Wedding Buffet' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966220/Wedding_Buffet.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966221/Wedding_Buffet2.jpg',
            ],

            'Event Catering Service' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966210/Event_Catering_Service.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966210/Event_Catering_Service2.jpg',
            ],

            'Luxury Car Rental' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966215/Luxury_Car_Rental.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966215/Luxury_Car_Rental2.jpg',
            ],

            'Wedding Car Service' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966223/Wedding_Car_Service.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966222/Wedding_Car_Service2.jpg',
            ],

            'DJ and Event Sound' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966206/DJ_and_Event_Sound.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966207/DJ_and_Event_Sound2.jpg',
            ],

            'Outdoor Event Venue' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966217/Outdoor_Event_Venue2.jpg',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786966216/Outdoor_Event_Venue.jpg',
            ],

            'Event Decoration' => [
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000824/decoration3.avif',
                'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000825/decoration5.avif',
                 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000825/decoration6.avif',
            ],

'Professional Event Makeup' => [
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000827/makeup.jpg',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000828/makeup2.jpg',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000828/makeup3.jpg',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000829/makeup4.jpg',
],

'Bridal Photography Package' => [
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000832/photography2.avif',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000832/photography.avif',
],

' Photography' => [
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000829/Photographer.avif',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000830/Photographer2.avif',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000831/Photographer4.avif',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000830/Photographer3.avif',
],

'Custom Celebration Cake' => [
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787001589/Cake2.avif',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787001590/Cake4.avif',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787001589/cake.avif',
],

'Dessert Boxes' => [
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000826/desserts.avif',
    'https://res.cloudinary.com/kzrnsaw4/image/upload/v1787000827/Desserts2.avif',
],
        ];

        foreach ($images as $title => $urls) {

            $service = Service::whereHas('translations', function ($query) use ($title) {
                $query->where('locale', 'en')
                    ->where('title', $title);
            })->first();

            if (!$service) {
                continue;
            }

            $service->images()->delete();

            foreach ($urls as $index => $url) {

                $service->images()->create([
                    'url' => $url,
                    'is_primary' => $index === 0,
                ]);
            }
        }
    }
}