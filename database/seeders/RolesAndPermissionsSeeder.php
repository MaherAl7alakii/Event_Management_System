<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        $permissions = [
            'login',
            'register',
            'logout',
            'refresh'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission,'api');
        }
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $servesProviderRole = Role::create(['name' => 'service_provider', 'guard_name' => 'api']);
        $customerRole = Role::create(['name' => 'customer','guard_name' => 'api']);


        $adminRole->syncPermissions($permissions);

        $servesProviderRole->syncPermissions(['login','register','logout','refresh']);

        $customerRole->syncPermissions(['login','register','logout','refresh']);

    }
}
