<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [

            /*
            |--------------------------------------------------------------------------
            | Mohammad Ali
            |--------------------------------------------------------------------------
            */

            'mohammad.ali@gmail.com' => [

                [
                    'type' => 'Wedding',
                    'title' => 'Mohammad Wedding',
                    'date' => '2026-11-15',
                    'start_time' => '16:00',
                    'end_time' => '23:00',
                    'guests' => 250,
                    'status' => EventStatus::DRAFT,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Birthday',
                    'title' => 'Mohammad Birthday Celebration',
                    'date' => '2026-12-05',
                    'start_time' => '18:00',
                    'end_time' => '22:00',
                    'guests' => 60,
                    'status' => EventStatus::SUBMITTED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
                [
    'type' => 'Wedding',
    'title' => 'Lina Completed Bridal Event',
    'date' => '2026-08-10',
    'start_time' => '16:00',
    'end_time' => '20:00',
    'guests' => 1,
    'status' => EventStatus::COMPLETED,
    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
],

[
    'type' => 'Wedding',
    'title' => 'Lina Tomorrow Bridal Event',
    'date' => '2026-08-19',
    'start_time' => '10:00',
    'end_time' => '14:00',
    'guests' => 1,
    'status' => EventStatus::CONFIRMED,
    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
],

[
    'type' => 'Birthday',
    'title' => 'Lina Tomorrow Event Makeup',
    'date' => '2026-08-19',
    'start_time' => '17:00',
    'end_time' => '20:00',
    'guests' => 5,
    'status' => EventStatus::SUBMITTED,
    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
],

[
    'type' => 'Engagement',
    'title' => 'Lina Future Makeup Event',
    'date' => '2026-08-21',
    'start_time' => '15:00',
    'end_time' => '18:00',
    'guests' => 1,
    'status' => EventStatus::SUBMITTED,
    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
],

[
    'type' => 'Wedding',
    'title' => 'Lina Photography Event',
    'date' => '2026-08-25',
    'start_time' => '16:00',
    'end_time' => '20:00',
    'guests' => 100,
    'status' => EventStatus::DEPOSIT_PAID,
    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
],

[
    'type' => 'Birthday',
    'title' => 'Lina Cake Event',
    'date' => '2026-08-28',
    'start_time' => '18:00',
    'end_time' => '21:00',
    'guests' => 60,
    'status' => EventStatus::CONFIRMED,
    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
],

[
    'type' => 'Private Party',
    'title' => 'Lina Dessert Boxes Event',
    'date' => '2026-09-03',
    'start_time' => '17:00',
    'end_time' => '20:00',
    'guests' => 40,
    'status' => EventStatus::SUBMITTED,
    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
],
            ],

            /*
            |--------------------------------------------------------------------------
            | Sara Hassan
            |--------------------------------------------------------------------------
            */

            'sara.hassan@gmail.com' => [

                [
                    'type' => 'Engagement',
                    'title' => 'Sara Engagement Party',
                    'date' => '2026-11-20',
                    'start_time' => '17:00',
                    'end_time' => '23:00',
                    'guests' => 180,
                    'status' => EventStatus::SUBMITTED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Wedding',
                    'title' => 'Sara Wedding',
                    'date' => '2027-01-10',
                    'start_time' => '16:00',
                    'end_time' => '23:00',
                    'guests' => 300,
                    'status' => EventStatus::PARTIALLY_ACCEPTED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Rami Khalil
            |--------------------------------------------------------------------------
            */

            'rami.khalil@gmail.com' => [

                [
                    'type' => 'Corporate Event',
                    'title' => 'Rami Corporate Event',
                    'date' => '2026-11-25',
                    'start_time' => '10:00',
                    'end_time' => '18:00',
                    'guests' => 120,
                    'status' => EventStatus::PARTIALLY_ACCEPTED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Wedding',
                    'title' => 'Rami Wedding',
                    'date' => '2027-02-14',
                    'start_time' => '17:00',
                    'end_time' => '23:30',
                    'guests' => 350,
                    'status' => EventStatus::AWAITING_PAYMENT,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Dima Ahmad
            |--------------------------------------------------------------------------
            */

            'dima.ahmad@gmail.com' => [

                [
                    'type' => 'Birthday',
                    'title' => 'Dima Birthday Party',
                    'date' => '2026-12-12',
                    'start_time' => '17:00',
                    'end_time' => '22:00',
                    'guests' => 80,
                    'status' => EventStatus::AWAITING_PAYMENT,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Engagement',
                    'title' => 'Dima Engagement',
                    'date' => '2027-01-22',
                    'start_time' => '18:00',
                    'end_time' => '23:00',
                    'guests' => 150,
                    'status' => EventStatus::DEPOSIT_PAID,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Karim Saleh
            |--------------------------------------------------------------------------
            */

            'karim.saleh@gmail.com' => [

                [
                    'type' => 'Wedding',
                    'title' => 'Karim Wedding',
                    'date' => '2027-03-05',
                    'start_time' => '16:00',
                    'end_time' => '23:30',
                    'guests' => 400,
                    'status' => EventStatus::DEPOSIT_PAID,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Graduation',
                    'title' => 'Karim Graduation Party',
                    'date' => '2026-12-20',
                    'start_time' => '17:00',
                    'end_time' => '22:00',
                    'guests' => 100,
                    'status' => EventStatus::CONFIRMED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Lama Omar
            |--------------------------------------------------------------------------
            */

            'lama.omar@gmail.com' => [

                [
                    'type' => 'Birthday',
                    'title' => 'Lama Birthday Celebration',
                    'date' => '2026-11-30',
                    'start_time' => '18:00',
                    'end_time' => '23:00',
                    'guests' => 70,
                    'status' => EventStatus::CONFIRMED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Wedding',
                    'title' => 'Lama Wedding',
                    'date' => '2026-08-01',
                    'start_time' => '16:00',
                    'end_time' => '23:00',
                    'guests' => 280,
                    'status' => EventStatus::COMPLETED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Hussein Nasser
            |--------------------------------------------------------------------------
            */

            'hussein.nasser@gmail.com' => [

                [
                    'type' => 'Corporate Event',
                    'title' => 'Hussein Company Event',
                    'date' => '2026-07-15',
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'guests' => 150,
                    'status' => EventStatus::COMPLETED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Wedding',
                    'title' => 'Hussein Wedding',
                    'date' => '2027-01-30',
                    'start_time' => '17:00',
                    'end_time' => '23:00',
                    'guests' => 220,
                    'status' => EventStatus::CANCELLED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Razan Samir
            |--------------------------------------------------------------------------
            */

            'razan.samir@gmail.com' => [

                [
                    'type' => 'Engagement',
                    'title' => 'Razan Engagement',
                    'date' => '2026-07-10',
                    'start_time' => '18:00',
                    'end_time' => '23:00',
                    'guests' => 130,
                    'status' => EventStatus::CANCELLED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Birthday',
                    'title' => 'Razan Birthday',
                    'date' => '2026-07-20',
                    'start_time' => '17:00',
                    'end_time' => '22:00',
                    'guests' => 50,
                    'status' => EventStatus::EXPIRED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Tamer Ibrahim
            |--------------------------------------------------------------------------
            */

            'tamer.ibrahim@gmail.com' => [

                [
                    'type' => 'Wedding',
                    'title' => 'Tamer Wedding',
                    'date' => '2027-03-20',
                    'start_time' => '16:00',
                    'end_time' => '23:30',
                    'guests' => 320,
                    'status' => EventStatus::DRAFT,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Corporate Event',
                    'title' => 'Tamer Business Event',
                    'date' => '2026-12-28',
                    'start_time' => '10:00',
                    'end_time' => '17:00',
                    'guests' => 100,
                    'status' => EventStatus::SUBMITTED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Jana Mahmoud
            |--------------------------------------------------------------------------
            */

            'jana.mahmoud@gmail.com' => [

                [
                    'type' => 'Graduation',
                    'title' => 'Jana Graduation Party',
                    'date' => '2026-12-15',
                    'start_time' => '17:00',
                    'end_time' => '22:00',
                    'guests' => 120,
                    'status' => EventStatus::SUBMITTED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970276/event.jpg',
                ],

                [
                    'type' => 'Engagement',
                    'title' => 'Jana Engagement',
                    'date' => '2027-02-20',
                    'start_time' => '18:00',
                    'end_time' => '23:00',
                    'guests' => 170,
                    'status' => EventStatus::CONFIRMED,
                    'image' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786970277/event2.jpg',
                ],
            ],
        ];

        foreach ($events as $email => $customerEvents) {

            $customer = User::where('email', $email)->first();

            if (!$customer) {
                continue;
            }

            // Delete old events for this customer
            $customer->events()->delete();

            foreach ($customerEvents as $eventData) {

                $eventType = EventType::whereHas('translations', function ($query) use ($eventData) {
                    $query->where('locale', 'en')
                        ->where('name', $eventData['type']);
                })->first();

                if (!$eventType) {
                    continue;
                }

                $event = $customer->events()->create([
                    'event_type_id' => $eventType->id,

                    // نفس مدينة الـ Customer
                    'city_id' => $customer->profile?->city_id,

                    'other_type' => null,

                    'title' => $eventData['title'],

                    'cover_image' => $eventData['image'],

                    'event_date' => $eventData['date'],

                    'start_time' => $eventData['start_time'],

                    'end_time' => $eventData['end_time'],

                    'guests_count' => $eventData['guests'],

                    'status' => $eventData['status']->value,

                    'submitted_at' => in_array(
                        $eventData['status'],
                        [
                            EventStatus::SUBMITTED,
                            EventStatus::PARTIALLY_ACCEPTED,
                            EventStatus::AWAITING_PAYMENT,
                            EventStatus::DEPOSIT_PAID,
                            EventStatus::CONFIRMED,
                            EventStatus::COMPLETED,
                        ]
                    ) ? now() : null,

                    'confirmed_at' => in_array(
                        $eventData['status'],
                        [
                            EventStatus::CONFIRMED,
                            EventStatus::COMPLETED,
                        ]
                    ) ? now() : null,
                ]);
            }
        }
    }
}