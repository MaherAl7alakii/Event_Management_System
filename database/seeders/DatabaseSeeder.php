<?php

namespace Database\Seeders;

use App\Http\Resources\WorkingHourResource;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Psy\Readline\Hoa\Event;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        $this->call([
            GovernorateSeeder::class,
            CitySeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            EventTypeSeeder::class,
//            ServiceProviderSeeder::class,
            WorkingHoursSeeder::class,
             ServiceOfferSeeder::class,
             ServiceProviderGallerySeeder::class,
              ReviewSeeder::class,
              PackageSeeder::class,
        ]);
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

    }
}
