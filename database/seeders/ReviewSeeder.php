<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Review::query()->delete();

        $reviews = [

            [
                'customer' => 'lama.omar@gmail.com',
                'provider' => 'ahmad.khalil@gmail.com',
                'rating' => 5,
                'comment' =>
                    'Excellent photography service. The photos were professional and beautifully edited.',
            ],

            [
                'customer' => 'mohammad.ali@gmail.com',
                'provider' => 'lina.hassan@gmail.com',
                'rating' => 5,
                'comment' =>
                    'Very professional makeup service and great attention to detail.',
            ],

            [
                'customer' => 'sara.hassan@gmail.com',
                'provider' => 'omar.saleh@gmail.com',
                'rating' => 4,
                'comment' =>
                    'Beautiful decoration and good coordination with the event requirements.',
            ],

            [
                'customer' => 'karim.saleh@gmail.com',
                'provider' => 'khaled.nasser@gmail.com',
                'rating' => 5,
                'comment' =>
                    'Great DJ and excellent sound quality throughout the event.',
            ],

            [
                'customer' => 'dima.ahmad@gmail.com',
                'provider' => 'maya.ibrahim@gmail.com',
                'rating' => 5,
                'comment' =>
                    'The desserts were delicious and beautifully presented.',
            ],

            [
                'customer' => 'hussein.nasser@gmail.com',
                'provider' => 'yazan.mahmoud@gmail.com',
                'rating' => 4,
                'comment' =>
                    'Good catering service with a nice variety of food.',
            ],

            [
                'customer' => 'razan.samir@gmail.com',
                'provider' => 'nour.ali@gmail.com',
                'rating' => 4,
                'comment' =>
                    'Nice decoration and good communication with the provider.',
            ],

            [
                'customer' => 'tamer.ibrahim@gmail.com',
                'provider' => 'tarek.ibrahim@gmail.com',
                'rating' => 5,
                'comment' =>
                    'Professional service and excellent event coverage.',
            ],
        ];

        foreach ($reviews as $data) {

            $customer = User::where('email', $data['customer'])->first();

            $provider = ServiceProvider::whereHas('user', function ($query) use ($data) {
                $query->where('email', $data['provider']);
            })->first();

            if (!$customer || !$provider) {
                continue;
            }

            Review::create([
                'user_id' => $customer->id,
                'service_provider_id' => $provider->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
            ]);
        }
    }
}