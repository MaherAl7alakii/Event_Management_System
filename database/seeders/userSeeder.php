<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'admin',
                'password' => bcrypt('000000'),
            ]
        );


        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {

            $admin->assignRole($adminRole);

            $permissions = $adminRole->permissions()->pluck('name')->toArray();
            $admin->syncPermissions($permissions);
        }
    }

}
