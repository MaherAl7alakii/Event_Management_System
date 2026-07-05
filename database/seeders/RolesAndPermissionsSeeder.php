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
            'refresh',
            'create_service',
            'update_service',
            'delete_service'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission,'api');
        }
        $adminRole          = Role::findOrCreate('admin', 'api');
        $serviceProviderRole = Role::findOrCreate('service_provider', 'api');
        $customerRole       = Role::findOrCreate('customer', 'api');


        $adminRole->syncPermissions($permissions);

        $serviceProviderRole->syncPermissions([
            'login','register','logout','refresh',
            'create_service','update_service','delete_service'

        ]);

        $customerRole->syncPermissions(['login','register','logout','refresh']);

    }
}
