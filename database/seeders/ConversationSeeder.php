<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        Conversation::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Helper
        |--------------------------------------------------------------------------
        */

        $getUser = function (string $email) {
            return User::where('email', $email)->firstOrFail();
        };

        /*
        |--------------------------------------------------------------------------
        | Conversations
        |--------------------------------------------------------------------------
        */

        $conversations = [

            /*
            |------------------------------------------------------------------
            | Lama ↔ Ahmad Photographer
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'lama.omar@gmail.com',
                'user_two' => 'ahmad.khalil@gmail.com',
            ],

            /*
            |------------------------------------------------------------------
            | Rami ↔ Khaled DJ
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'rami.khalil@gmail.com',
                'user_two' => 'khaled.nasser@gmail.com',
            ],

            /*
            |------------------------------------------------------------------
            | Sara ↔ Sara Venue Provider
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'sara.hassan@gmail.com',
                'user_two' => 'sara.ahmad@gmail.com',
            ],

            /*
            |------------------------------------------------------------------
            | Dima ↔ Lina Makeup Artist
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'dima.ahmad@gmail.com',
                'user_two' => 'lina.hassan@gmail.com',
            ],

            /*
            |------------------------------------------------------------------
            | Karim ↔ Omar Decoration
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'karim.saleh@gmail.com',
                'user_two' => 'omar.saleh@gmail.com',
            ],

            /*
            |------------------------------------------------------------------
            | Jana ↔ Nour Decoration
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'jana.mahmoud@gmail.com',
                'user_two' => 'nour.ali@gmail.com',
            ],

            /*
            |------------------------------------------------------------------
            | Mohammad ↔ Maya Desserts
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'mohammad.ali@gmail.com',
                'user_two' => 'maya.ibrahim@gmail.com',
            ],

            /*
            |------------------------------------------------------------------
            | Tamer ↔ Tarek
            |------------------------------------------------------------------
            */

            [
                'user_one' => 'tamer.ibrahim@gmail.com',
                'user_two' => 'tarek.ibrahim@gmail.com',
            ],
        ];

        foreach ($conversations as $data) {

            $userOne = $getUser($data['user_one']);
            $userTwo = $getUser($data['user_two']);

            /*
             * Always store the smaller user ID first.
             * This keeps the unique constraint consistent.
             */

            $userIds = [
                $userOne->id,
                $userTwo->id,
            ];

            sort($userIds);

            Conversation::create([
                'user_one_id' => $userIds[0],
                'user_two_id' => $userIds[1],
                'last_message_id' => null,
                'last_message_at' => null,
            ]);
        }
    }
}