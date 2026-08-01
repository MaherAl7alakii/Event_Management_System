<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Portfolio;
use App\Models\Profile;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $defaultPassword = bcrypt('000000');
        $now = Carbon::now();


        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Admin User',
                'password'          => $defaultPassword,
                'email_verified_at' => $now,
            ]
        );

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
            $admin->syncPermissions($adminRole->permissions()->pluck('name')->toArray());
        }


        $providerRole = Role::where('name', 'service_provider')->first();
        for ($i = 1; $i <= 10; $i++) {
            $provider = User::updateOrCreate(
                ['email' => "provider{$i}@gmail.com"],
                [
                    'name'              => "Provider {$i}",
                    'password'          => $defaultPassword,
                    'email_verified_at' => $now,
                ]
            );

            if ($providerRole) {
                $provider->assignRole($providerRole);
                $provider->syncPermissions($providerRole->permissions()->pluck('name')->toArray());
            }
            $randomCategories = Category::inRandomOrder()->take(rand(1, 3))->get();
            ServiceProvider::factory()
                ->has(ServiceProviderDocument::factory()->count(2), 'documents')
                ->has(Portfolio::factory()->count(2), 'portfolios')
                ->hasAttached($randomCategories, [], 'categories')
                ->create([
                    'user_id' => $provider->id,
                ]);
        }





        $customerRole = Role::where('name', 'customer')->first();
        for ($i = 1; $i <= 10; $i++) {
            $customer = User::factory()
                ->has(Profile::factory())
                ->create([
                    'name'              => "Customer {$i}",
                    'email'             => "customer{$i}@gmail.com",
                    'password'          => $defaultPassword,
                    'email_verified_at' => $now,
                ]);

            if ($customerRole) {
                $customer->assignRole($customerRole);

                 $customer->syncPermissions($customerRole->permissions()->pluck('name')->toArray());
            }
        }



    }
}
