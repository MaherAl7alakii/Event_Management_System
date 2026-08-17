<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        Favorite::query()->delete();

        $favorites = [

            [
                'user' => 'mohammad.ali@gmail.com',
                'provider' => 'ahmad.khalil@gmail.com',
            ],

            [
                'user' => 'sara.hassan@gmail.com',
                'provider' => 'omar.saleh@gmail.com',
            ],

            [
                'user' => 'rami.khalil@gmail.com',
                'provider' => 'khaled.nasser@gmail.com',
            ],

            [
                'user' => 'dima.ahmad@gmail.com',
                'provider' => 'maya.ibrahim@gmail.com',
            ],

            [
                'user' => 'karim.saleh@gmail.com',
                'provider' => 'sara.ahmad@gmail.com',
            ],

            [
                'user' => 'lama.omar@gmail.com',
                'provider' => 'ahmad.khalil@gmail.com',
            ],

            [
                'user' => 'hussein.nasser@gmail.com',
                'provider' => 'yazan.mahmoud@gmail.com',
            ],

            [
                'user' => 'jana.mahmoud@gmail.com',
                'provider' => 'nour.ali@gmail.com',
            ],
        ];

        foreach ($favorites as $data) {

            $user = User::where('email', $data['user'])->first();

            $provider = ServiceProvider::whereHas('user', function ($query) use ($data) {
                $query->where('email', $data['provider']);
            })->first();

            if (!$user || !$provider) {
                continue;
            }

            Favorite::create([
                'user_id' => $user->id,
                'favoritable_type' => ServiceProvider::class,
                'favoritable_id' => $provider->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Favorites for Services
        |--------------------------------------------------------------------------
        */

        $serviceFavorites = [

            [
                'user' => 'mohammad.ali@gmail.com',
                'title' => 'Wedding Photography Package',
            ],

            [
                'user' => 'sara.hassan@gmail.com',
                'title' => 'Wedding Decoration',
            ],

            [
                'user' => 'karim.saleh@gmail.com',
                'title' => 'Wedding DJ Package',
            ],

            [
                'user' => 'dima.ahmad@gmail.com',
                'title' => 'Custom Wedding Cake',
            ],

            [
                'user' => 'hussein.nasser@gmail.com',
                'title' => 'Wedding Buffet',
            ],
        ];

        foreach ($serviceFavorites as $data) {

            $user = User::where('email', $data['user'])->first();

            $service = Service::whereHas('translations', function ($query) use ($data) {
                $query
                    ->where('locale', 'en')
                    ->where('title', $data['title']);
            })->first();

            if (!$user || !$service) {
                continue;
            }

            Favorite::create([
                'user_id' => $user->id,
                'favoritable_type' => Service::class,
                'favoritable_id' => $service->id,
            ]);
        }
    }
}