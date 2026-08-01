<?php

namespace App\Services;

use App\Models\User;

class UserService
{

    public function getCustomers(array $filters)
    {
        $filters['role'] = 'customer';

        return User::query()
            ->with(['profile', 'roles'])
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 50);
    }


    public function getServiceProviders(array $filters)
    {
        $filters['role'] = 'service_provider';

        return User::query()
            ->with(['serviceProvider', 'roles'])
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 50);
    }

}
