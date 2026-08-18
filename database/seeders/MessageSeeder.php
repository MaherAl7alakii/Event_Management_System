<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        Message::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        $getUser = function (string $email) {
            return User::where('email', $email)->firstOrFail();
        };

        $getConversation = function (string $emailOne, string $emailTwo) use ($getUser) {

            $userOne = $getUser($emailOne);
            $userTwo = $getUser($emailTwo);

            $ids = [
                $userOne->id,
                $userTwo->id,
            ];

            sort($ids);

            return Conversation::where('user_one_id', $ids[0])
                ->where('user_two_id', $ids[1])
                ->firstOrFail();
        };

        /*
        |--------------------------------------------------------------------------
        | Conversation 1
        | Lama ↔ Ahmad
        |--------------------------------------------------------------------------
        */

        $conversation = $getConversation(
            'lama.omar@gmail.com',
            'ahmad.khalil@gmail.com'
        );

        $lama = $getUser('lama.omar@gmail.com');
        $ahmad = $getUser('ahmad.khalil@gmail.com');

        $messages = [

            [
                'sender_id' => $lama->id,
                'type' => 'text',
                'body' => 'Hello, I am interested in your wedding photography service.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 180,
            ],

            [
                'sender_id' => $ahmad->id,
                'type' => 'text',
                'body' => 'Hello Lama! Thank you for contacting me. I would be happy to help with your wedding.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 170,
            ],

            [
                'sender_id' => $lama->id,
                'type' => 'text',
                'body' => 'The wedding will have around 280 guests. Is your package suitable for this size?',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 160,
            ],

            [
                'sender_id' => $ahmad->id,
                'type' => 'text',
                'body' => 'Yes, absolutely. My wedding photography package supports events up to 300 guests.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 150,
            ],

            [
                'sender_id' => $ahmad->id,
                'type' => 'image',
                'body' => 'Here is an example from a recent wedding.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 140,
            ],

            [
                'sender_id' => $lama->id,
                'type' => 'text',
                'body' => 'Looks great! I really like the photography style.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 130,
            ],
        ];

        $this->createMessages($conversation, $messages);

        /*
        |--------------------------------------------------------------------------
        | Conversation 2
        | Rami ↔ Khaled
        |--------------------------------------------------------------------------
        */

        $conversation = $getConversation(
            'rami.khalil@gmail.com',
            'khaled.nasser@gmail.com'
        );

        $rami = $getUser('rami.khalil@gmail.com');
        $khaled = $getUser('khaled.nasser@gmail.com');

        $messages = [

            [
                'sender_id' => $rami->id,
                'type' => 'text',
                'body' => 'Hi, I am planning a wedding and I need a DJ and sound system.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 300,
            ],

            [
                'sender_id' => $khaled->id,
                'type' => 'text',
                'body' => 'Hi Rami! I can provide a complete DJ and sound system for your event.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 290,
            ],

            [
                'sender_id' => $rami->id,
                'type' => 'text',
                'body' => 'The event will be from 5 PM until around 11:30 PM.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 280,
            ],

            [
                'sender_id' => $khaled->id,
                'type' => 'text',
                'body' => 'That works perfectly. I recommend the Wedding DJ Package for this event.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 270,
            ],
        ];

        $this->createMessages($conversation, $messages);

        /*
        |--------------------------------------------------------------------------
        | Conversation 3
        | Sara ↔ Venue
        |--------------------------------------------------------------------------
        */

        $conversation = $getConversation(
            'sara.hassan@gmail.com',
            'sara.ahmad@gmail.com'
        );

        $saraCustomer = $getUser('sara.hassan@gmail.com');
        $saraProvider = $getUser('sara.ahmad@gmail.com');

        $messages = [

            [
                'sender_id' => $saraCustomer->id,
                'type' => 'text',
                'body' => 'Hello, is the wedding hall available for my event?',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 240,
            ],

            [
                'sender_id' => $saraProvider->id,
                'type' => 'text',
                'body' => 'Hello! Yes, the hall is suitable for weddings and can accommodate up to 250 guests.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 230,
            ],

            [
                'sender_id' => $saraCustomer->id,
                'type' => 'text',
                'body' => 'Great. We are expecting around 180 guests.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 220,
            ],

            [
                'sender_id' => $saraProvider->id,
                'type' => 'text',
                'body' => 'Perfect. The hall will comfortably accommodate your guests.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 210,
            ],
        ];

        $this->createMessages($conversation, $messages);

        /*
        |--------------------------------------------------------------------------
        | Conversation 4
        | Dima ↔ Lina
        |--------------------------------------------------------------------------
        */

        $conversation = $getConversation(
            'dima.ahmad@gmail.com',
            'lina.hassan@gmail.com'
        );

        $dima = $getUser('dima.ahmad@gmail.com');
        $lina = $getUser('lina.hassan@gmail.com');

        $messages = [

            [
                'sender_id' => $dima->id,
                'type' => 'text',
                'body' => 'Hi Lina, I would like to book bridal makeup for my engagement.',
                'media_url' =>'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 200,
            ],

            [
                'sender_id' => $lina->id,
                'type' => 'text',
                'body' => 'Hello Dima! Of course. Please let me know the event date and time.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 190,
            ],

            [
                'sender_id' => $dima->id,
                'type' => 'text',
                'body' => 'The event starts at 6 PM. I would like to start makeup around 2 PM.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 180,
            ],

            [
                'sender_id' => $lina->id,
                'type' => 'text',
                'body' => 'That timing works well. I will make sure everything is ready before the event.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 170,
            ],
        ];

        $this->createMessages($conversation, $messages);

        /*
        |--------------------------------------------------------------------------
        | Conversation 5
        | Karim ↔ Omar
        |--------------------------------------------------------------------------
        */

        $conversation = $getConversation(
            'karim.saleh@gmail.com',
            'omar.saleh@gmail.com'
        );

        $karim = $getUser('karim.saleh@gmail.com');
        $omar = $getUser('omar.saleh@gmail.com');

        $messages = [

            [
                'sender_id' => $karim->id,
                'type' => 'text',
                'body' => 'I am interested in your wedding decoration service.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 120,
            ],

            [
                'sender_id' => $omar->id,
                'type' => 'text',
                'body' => 'Thank you! We provide complete wedding decoration including tables, lighting and entrance design.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 110,
            ],

            [
                'sender_id' => $karim->id,
                'type' => 'image',
                'body' => 'I would like something similar to this style.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 100,
            ],

            [
                'sender_id' => $omar->id,
                'type' => 'text',
                'body' => 'Yes, we can create a similar decoration style for your wedding.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 90,
            ],
        ];

        $this->createMessages($conversation, $messages);

        /*
        |--------------------------------------------------------------------------
        | Conversation 6
        | Jana ↔ Nour
        |--------------------------------------------------------------------------
        */

        $conversation = $getConversation(
            'jana.mahmoud@gmail.com',
            'nour.ali@gmail.com'
        );

        $jana = $getUser('jana.mahmoud@gmail.com');
        $nour = $getUser('nour.ali@gmail.com');

        $messages = [

            [
                'sender_id' => $jana->id,
                'type' => 'text',
                'body' => 'Hello, I am looking for an outdoor venue for my engagement.',
                'media_url' =>'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 80,
            ],

            [
                'sender_id' => $nour->id,
                'type' => 'text',
                'body' => 'Hello Jana! Our outdoor venue is suitable for small weddings, engagements and birthdays.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 70,
            ],

            [
                'sender_id' => $jana->id,
                'type' => 'text',
                'body' => 'We are expecting around 170 guests. Would that work?',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 60,
            ],

            [
                'sender_id' => $nour->id,
                'type' => 'text',
                'body' => 'Our venue supports up to 120 guests, so unfortunately it would not be suitable for 170 guests.',
                'media_url' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786973835/message.png',
                'minutes_ago' => 50,
            ],
        ];

        $this->createMessages($conversation, $messages);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Messages
    |--------------------------------------------------------------------------
    */

    private function createMessages(
        Conversation $conversation,
        array $messages
    ): void {

        $lastMessage = null;

        foreach ($messages as $data) {

            $lastMessage = Message::create([
                'conversation_id' => $conversation->id,

                'sender_id' => $data['sender_id'],

                'type' => $data['type'],

                'body' => $data['body'],

                'media_url' => $data['media_url'],

                'is_edited' => false,

                'edited_at' => null,

                'deleted_at' => null,

                /*
                 * Some messages are read,
                 * some are intentionally unread.
                 */

                'read_at' => $data['minutes_ago'] > 55
                    ? now()->subMinutes($data['minutes_ago'] - 20)
                    : null,

                'created_at' => now()->subMinutes($data['minutes_ago']),

                'updated_at' => now()->subMinutes($data['minutes_ago']),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update conversation last message
        |--------------------------------------------------------------------------
        */

        if ($lastMessage) {
            $conversation->update([
                'last_message_id' => $lastMessage->id,
                'last_message_at' => $lastMessage->created_at,
            ]);
        }
    }
}