<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('000000');

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();
        $providerRole = Role::where('name', 'service_provider')->first();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        $admin = User::updateOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'name' => 'System Administrator',
                'password' => $password,
                'email_verified_at' => now(),
                'is_banned' => false,
                'banned_at' => null,
            ]
        );

        if ($adminRole) {
            $admin->syncRoles([$adminRole]);
        }

        /*
        |--------------------------------------------------------------------------
        | SERVICE PROVIDERS
        |--------------------------------------------------------------------------
        */

        $providers = [

            [
                'name' => 'Ahmad Khalil',
                'email' => 'ahmad.khalil@gmail.com',
                'city_id' => 1,
                'account_type' => 'professional',
                'business_name' => 'Ahmad Photography Studio',
                'phone' => '+963944100001',
                'address' => 'Mazzeh, Damascus',
                'approval_status' => 'approved',
                'years_of_experience' => '8 years',
                'description' => 'Professional photography services for weddings, events and special occasions.',                
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956908/provider1.jpg',
            ],

            [
                'name' => 'Lina Hassan',
                'email' => 'lina.hassan@gmail.com',
                'city_id' => 2,
                'account_type' => 'professional',
                'business_name' => 'Lina Beauty Studio',
                'phone' => '+963944100002',
                'address' => 'Mazzeh, Damascus',
                'approval_status' => 'approved',
                'years_of_experience' => '6 years',
                'description' => 'Professional beauty and makeup services for weddings, events and special occasions.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956911/provider2.jpg',
            ],

            [
                'name' => 'Omar Saleh',
                'email' => 'omar.saleh@gmail.com',
                'city_id' => 3,
                'account_type' => 'professional',
                'business_name' => 'Omar Design Studio',
                'phone' => '+963944100003',
                'address' => 'Kafr Sousa, Damascus',
                'approval_status' => 'approved',
                'years_of_experience' => '7 years',
                'description' => 'Creative decoration and event design services for weddings and private events.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956914/provider3.webp',
            ],

            [
                'name' => 'Sara Ahmad',
                'email' => 'sara.ahmad@gmail.com',
                'city_id' => 4,
                'account_type' => 'professional',
                'business_name' => 'Sara Events',
                'phone' => '+963944100004',
                'address' => 'Barzeh, Damascus',
                'approval_status' => 'approved',
                'years_of_experience' => '5 years',
                'description' => 'Professional event and venue services for weddings, celebrations and private occasions.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956916/provider4.jpg',
            ],

            [
                'name' => 'Khaled Nasser',
                'email' => 'khaled.nasser@gmail.com',
                'city_id' => 12,
                'account_type' => 'professional',
                'business_name' => 'Khaled Media Production',
                'phone' => '+963944100005',
                'address' => 'Aleppo',
                'approval_status' => 'approved',
                'years_of_experience' => '9 years',
                'description' => 'Professional event media and entertainment services for weddings and special occasions.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956917/provider5.jpg',
            ],

            [
                'name' => 'Maya Ibrahim',
                'email' => 'maya.ibrahim@gmail.com',
                'city_id' => 5,
                'account_type' => 'individual',
                'business_name' => null,
                'phone' => '+963944100006',
                'address' => 'Douma, Rif Damascus',
                'approval_status' => 'approved',
                'years_of_experience' => '4 years',
                'description' => 'Independent specialist providing beauty and special occasion services.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956957/provider6.jpg',
            ],

            [
                'name' => 'Yazan Mahmoud',
                'email' => 'yazan.mahmoud@gmail.com',
                'city_id' => 19,
                'account_type' => 'professional',
                'business_name' => 'Yazan Digital Studio',
                'phone' => '+963944100007',
                'address' => 'Homs',
                'approval_status' => 'approved',
                'years_of_experience' => '6 years',
                'description' => 'Professional catering and event services for weddings, celebrations and special occasions.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956959/provider7.jpg',
            ],

            [
                'name' => 'Rana Samir',
                'email' => 'rana.samir@gmail.com',
                'city_id' => 36,
                'account_type' => 'professional',
                'business_name' => 'Rana Decoration',
                'phone' => '+963944100008',
                'address' => 'Latakia',
                'approval_status' => 'approved',
                'years_of_experience' => '7 years',
                'description' => 'Professional transportation and event support services for weddings and special occasions.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956960/provider8.jpg',
            ],

            [
                'name' => 'Tarek Ibrahim',
                'email' => 'tarek.ibrahim@gmail.com',
                'city_id' => 39,
                'account_type' => 'professional',
                'business_name' => 'Tarek Sound & DJ',
                'phone' => '+963944100009',
                'address' => 'Tartus',
                'approval_status' => 'approved',
                'years_of_experience' => '10 years',
                'description' => 'Professional music, entertainment and event services for weddings and special occasions.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956962/provider9.jpg',
            ],

            [
                'name' => 'Nour Ali',
                'email' => 'nour.ali@gmail.com',
                'city_id' => 42,
                'account_type' => 'individual',
                'business_name' => null,
                'phone' => '+963944100010',
                'address' => 'As-Suwayda',
                'approval_status' => 'approved',
                'years_of_experience' => '5 years',
                'description' => 'Professional event decoration and venue services for weddings and private celebrations.',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956964/provider10.jpg',
            ],
        ];

        foreach ($providers as $providerData) {

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $provider = User::updateOrCreate(
                [
                    'email' => $providerData['email'],
                ],
                [
                    'name' => $providerData['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'is_banned' => false,
                    'banned_at' => null,
                ]
            );

            if ($providerRole) {
                $provider->syncRoles([$providerRole]);
            }

            /*
            |--------------------------------------------------------------------------
            | Service Provider
            |--------------------------------------------------------------------------
            */

            ServiceProvider::updateOrCreate(
                [
                    'user_id' => $provider->id,
                ],
                [
                    'city_id' => $providerData['city_id'],
                    'account_type' => $providerData['account_type'],
                    'business_name' => $providerData['business_name'],
                    'avatar' => $providerData['avatar'],
                    'phone' => $providerData['phone'],
                    'address' => $providerData['address'],
                    'approval_status' => $providerData['approval_status'],
                    'years_of_experience' => $providerData['years_of_experience'],
                    'description' => $providerData['description'],
                    'verified_at' => now(),
                    'rejection_reason' => null,
                    'stripe_account_id' => null,
                    'stripe_onboarding_completed' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMERS
        |--------------------------------------------------------------------------
        */

        $customers = [

            [
                'name' => 'Mohammad Ali',
                'email' => 'mohammad.ali@gmail.com',
                'city_id' => 1,
                'phone' => '+963944200001',
                'gender' => 'male',
                'birth_of_date' => '1998-05-12',
                'address' => 'Damascus, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956833/costumer1.jpg',
            ],

            [
                'name' => 'Sara Hassan',
                'email' => 'sara.hassan@gmail.com',
                'city_id' => 2,
                'phone' => '+963944200002',
                'gender' => 'female',
                'birth_of_date' => '1999-08-21',
                'address' => 'Mazzeh, Damascus',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956861/costumer2.jpg',
            ],

            [
                'name' => 'Rami Khalil',
                'email' => 'rami.khalil@gmail.com',
                'city_id' => 12,
                'phone' => '+963944200003',
                'gender' => 'male',
                'birth_of_date' => '1997-02-17',
                'address' => 'Aleppo, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956875/costumer3.jpg',
            ],

            [
                'name' => 'Dima Ahmad',
                'email' => 'dima.ahmad@gmail.com',
                'city_id' => 19,
                'phone' => '+963944200004',
                'gender' => 'female',
                'birth_of_date' => '2000-11-03',
                'address' => 'Homs, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956895/costumer4.jpg',
            ],

            [
                'name' => 'Karim Saleh',
                'email' => 'karim.saleh@gmail.com',
                'city_id' => 22,
                'phone' => '+963944200005',
                'gender' => 'male',
                'birth_of_date' => '1996-07-28',
                'address' => 'Hama, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956897/costumer5.jpg',
            ],

            [
                'name' => 'Lama Omar',
                'email' => 'lama.omar@gmail.com',
                'city_id' => 36,
                'phone' => '+963944200006',
                'gender' => 'female',
                'birth_of_date' => '2001-03-15',
                'address' => 'Latakia, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956898/costumer6.jpg',
            ],

            [
                'name' => 'Hussein Nasser',
                'email' => 'hussein.nasser@gmail.com',
                'city_id' => 39,
                'phone' => '+963944200007',
                'gender' => 'male',
                'birth_of_date' => '1995-09-10',
                'address' => 'Tartus, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956900/costumer7.jpg',
            ],

            [
                'name' => 'Razan Samir',
                'email' => 'razan.samir@gmail.com',
                'city_id' => 42,
                'phone' => '+963944200008',
                'gender' => 'female',
                'birth_of_date' => '1999-12-07',
                'address' => 'As-Suwayda, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956902/costumer8.jpg',
            ],

            [
                'name' => 'Tamer Ibrahim',
                'email' => 'tamer.ibrahim@gmail.com',
                'city_id' => 8,
                'phone' => '+963944200009',
                'gender' => 'male',
                'birth_of_date' => '1998-06-19',
                'address' => 'Idlib, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956904/costumer9.jpg',
            ],

            [
                'name' => 'Jana Mahmoud',
                'email' => 'jana.mahmoud@gmail.com',
                'city_id' => 13,
                'phone' => '+963944200010',
                'gender' => 'female',
                'birth_of_date' => '2000-01-25',
                'address' => 'Aleppo, Syria',
                'avatar' => 'https://res.cloudinary.com/kzrnsaw4/image/upload/v1786956905/costumer10.jpg',
            ],
        ];

        foreach ($customers as $customerData) {

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $customer = User::updateOrCreate(
                [
                    'email' => $customerData['email'],
                ],
                [
                    'name' => $customerData['name'],
                    'password' => $password,
                    'email_verified_at' => now(),
                    'is_banned' => false,
                    'banned_at' => null,
                ]
            );

            if ($customerRole) {
                $customer->syncRoles([$customerRole]);

            }

            /*
            |--------------------------------------------------------------------------
            | Customer Profile
            |--------------------------------------------------------------------------
            */

            Profile::updateOrCreate(
                [
                    'user_id' => $customer->id,
                ],
                [
                    'city_id' => $customerData['city_id'],
                    'phone' => $customerData['phone'],
                    'avatar' => $customerData['avatar'],
                    'address' => $customerData['address'],
                    'gender' => $customerData['gender'],
                    'birth_of_date' => $customerData['birth_of_date'],
                ]
            );
        }
    }
}

