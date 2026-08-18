<?php

namespace Database\Seeders;

use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class SearchHistorySeeder extends Seeder
{
    public function run(): void
    {
        SearchHistory::query()->delete();

        $searches = [

            'mohammad.ali@gmail.com' => [
                'Wedding Photographer',
                'Wedding Hall',
                'DJ',
            ],

            'sara.hassan@gmail.com' => [
                'Wedding Decoration',
                'Makeup Artist',
                'Wedding Hall',
            ],

            'rami.khalil@gmail.com' => [
                'Wedding Photography',
                'Catering',
                'Wedding Decoration',
            ],

            'dima.ahmad@gmail.com' => [
                'Birthday Decoration',
                'Desserts',
                'Makeup',
            ],

            'karim.saleh@gmail.com' => [
                'Wedding Hall',
                'DJ',
                'Wedding Car',
            ],

            'lama.omar@gmail.com' => [
                'Wedding Photography',
                'Desserts',
                'Event Decoration',
            ],

            'hussein.nasser@gmail.com' => [
                'Corporate Event',
                'Catering',
                'DJ',
            ],

            'razan.samir@gmail.com' => [
                'Birthday Party',
                'Desserts',
                'Decoration',
            ],

            'tamer.ibrahim@gmail.com' => [
                'Wedding Photographer',
                'Event Decoration',
                'DJ',
            ],

            'jana.mahmoud@gmail.com' => [
                'Graduation Party',
                'Makeup Artist',
                'Event Venue',
            ],
        ];

        foreach ($searches as $email => $keywords) {

            $user = User::where('email', $email)->first();

            if (!$user) {
                continue;
            }

            foreach ($keywords as $keyword) {
                SearchHistory::create([
                    'user_id' => $user->id,
                    'keyword' => $keyword,
                ]);
            }
        }
    }
}