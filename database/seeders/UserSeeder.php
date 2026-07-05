<?php

namespace Database\Seeders;

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
        }


        $customerRole = Role::where('name', 'customer')->first();
        for ($i = 1; $i <= 10; $i++) {
            $customer = User::updateOrCreate(
                ['email' => "customer{$i}@gmail.com"],
                [
                    'name'              => "Customer {$i}",
                    'password'          => $defaultPassword,
                    'email_verified_at' => $now,
                ]
            );

            if ($customerRole) {
                $customer->assignRole($customerRole);

                 $customer->syncPermissions($customerRole->permissions()->pluck('name')->toArray());
            }
        }



    }
}
