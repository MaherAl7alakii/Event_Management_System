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

            //Service
            'create_service',
            'update_service',
            'delete_service',

            // Booking
            'view_bookings',
            'create_booking',
            'estimate_booking_price',
            'update_booking',
            'accept_or_reject_booking',
            'view_event_bookings',

            //calendar
            'update_working_hours',
            'add_time_off',
            'update_time_off',
            'delete_time_off',
            'calendar_view',

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
            'create_service',
            'update_service',
            'delete_service',
            'view_bookings',
            'accept_or_reject_booking',
            'update_working_hours',
            'add_time_off',
            'update_time_off',
            'delete_time_off',
            'calendar_view',

        ]);

        $customerRole->syncPermissions([
            'login',
            'register',
            'logout',
            'refresh',
            'view_bookings',
            'create_booking',
            'estimate_booking_price',
            'update_booking',
            'view_event_bookings',

        ]);

    }
}
