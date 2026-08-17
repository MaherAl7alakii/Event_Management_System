<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [

            'ahmad.khalil@gmail.com' => [
                [
                    'name' => 'Complete Wedding Photography',
                    'description' => 'A complete photography package for weddings and special occasions.',
                    'discount' => 15,
                    'services' => [
                        'Wedding Photography Package',
                        'Event Photography',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'lina.hassan@gmail.com' => [
                [
                    'name' => 'Bridal Beauty Package',
                    'description' => 'Complete bridal makeup services for the wedding day.',
                    'discount' => 10,
                    'services' => [
                        'Bridal Makeup',
                        'Event Makeup',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'omar.saleh@gmail.com' => [
                [
                    'name' => 'Wedding Decoration Package',
                    'description' => 'Complete decoration package including wedding and floral decoration.',
                    'discount' => 15,
                    'services' => [
                        'Wedding Decoration',
                        'Floral Decoration Service',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'sara.ahmad@gmail.com' => [
                [
                    'name' => 'Wedding Venue Package',
                    'description' => 'Elegant venue options for weddings and private events.',
                    'discount' => 10,
                    'services' => [
                        'Wedding Hall',
                        'Private Event Hall',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'khaled.nasser@gmail.com' => [
                [
                    'name' => 'Complete DJ Package',
                    'description' => 'Professional DJ and sound services for weddings and events.',
                    'discount' => 15,
                    'services' => [
                        'DJ and Sound System',
                        'Wedding DJ Package',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'maya.ibrahim@gmail.com' => [
                [
                    'name' => 'Wedding Dessert Package',
                    'description' => 'Wedding cake and dessert table package.',
                    'discount' => 10,
                    'services' => [
                        'Custom Wedding Cake',
                        'Dessert Table',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'yazan.mahmoud@gmail.com' => [
                [
                    'name' => 'Wedding Catering Package',
                    'description' => 'Complete catering service for weddings and private events.',
                    'discount' => 10,
                    'services' => [
                        'Wedding Buffet',
                        'Event Catering Service',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'rana.samir@gmail.com' => [
                [
                    'name' => 'Wedding Car Package',
                    'description' => 'Luxury transportation for weddings and special occasions.',
                    'discount' => 15,
                    'services' => [
                        'Luxury Car Rental',
                        'Wedding Car Service',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'tarek.ibrahim@gmail.com' => [
                [
                    'name' => 'Photography and DJ Package',
                    'description' => 'Photography and music services for complete event coverage.',
                    'discount' => 15,
                    'services' => [
                        'Event Photography',
                        'DJ and Event Sound',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],

            'nour.ali@gmail.com' => [
                [
                    'name' => 'Event Decoration and Venue Package',
                    'description' => 'Complete event setup combining decoration and venue services.',
                    'discount' => 15,
                    'services' => [
                        'Event Decoration',
                        'Outdoor Event Venue',
                    ],
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786968800/package.jpg',
                ],
            ],
        ];

        foreach ($packages as $email => $providerPackages) {

            $provider = ServiceProvider::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })->first();

            if (!$provider) {
                continue;
            }

            $provider->packages()->delete();

            foreach ($providerPackages as $packageData) {

                $services = Service::whereHas('translations', function ($query) use ($packageData) {
                    $query->where('locale', 'en')
                        ->whereIn('title', $packageData['services']);
                })
                ->where('provider_id', $provider->user_id)
                ->get();

                if ($services->count() !== count($packageData['services'])) {
                    continue;
                }

                $totalPrice = $services->sum('base_price');

                $discountAmount = $totalPrice * $packageData['discount'] / 100;

                $finalPrice = $totalPrice - $discountAmount;

                $package = $provider->packages()->create([
                    'image' => $packageData['image'],
                    'name' => $packageData['name'],
                    'description' => $packageData['description'],
                    'total_price' => $totalPrice,
                    'discount' => $packageData['discount'],
                    'final_price' => $finalPrice,
                    'status' => 'active',
                ]);

                $package->services()->sync($services->pluck('id'));
            }
        }
    }
}