<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notifications')->delete();

        $notifications = [

            [
                'email' => 'mohammad.ali@gmail.com',
                'type' => 'booking',
                'title' => 'Booking Accepted',
                'message' => 'Your booking request has been accepted by the service provider.',
                'read_at' => null,
            ],

            [
                'email' => 'sara.hassan@gmail.com',
                'type' => 'booking',
                'title' => 'Booking Confirmed',
                'message' => 'Your booking has been confirmed successfully.',
                'read_at' => now()->subHours(3),
            ],

            [
                'email' => 'rami.khalil@gmail.com',
                'type' => 'payment',
                'title' => 'Deposit Payment',
                'message' => 'Your deposit payment has been received successfully.',
                'read_at' => null,
            ],

            [
                'email' => 'dima.ahmad@gmail.com',
                'type' => 'booking',
                'title' => 'Booking Update',
                'message' => 'Your booking status has been updated.',
                'read_at' => now()->subDay(),
            ],

            [
                'email' => 'karim.saleh@gmail.com',
                'type' => 'payment',
                'title' => 'Payment Confirmed',
                'message' => 'Your payment has been processed successfully.',
                'read_at' => null,
            ],

            [
                'email' => 'lama.omar@gmail.com',
                'type' => 'booking',
                'title' => 'Booking Completed',
                'message' => 'Your booking has been marked as completed.',
                'read_at' => now()->subDays(2),
            ],

            [
                'email' => 'hussein.nasser@gmail.com',
                'type' => 'complaint',
                'title' => 'Complaint Received',
                'message' => 'Your complaint has been received and is being reviewed.',
                'read_at' => null,
            ],

            [
                'email' => 'jana.mahmoud@gmail.com',
                'type' => 'event',
                'title' => 'Event Confirmed',
                'message' => 'Your event has been confirmed successfully.',
                'read_at' => now()->subHours(5),
            ],
        ];

        foreach ($notifications as $data) {

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                continue;
            }

            DB::table('notifications')->insert([
                'type' => $data['type'],
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => $data['title'],
                    'message' => $data['message'],
                ]),
                'read_at' => $data['read_at'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}