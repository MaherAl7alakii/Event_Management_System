<?php

namespace Database\Seeders;

use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;

class StripeAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            'ahmad.khalil@gmail.com' => 'acct_1U5Wl5HKDEG6faox',
            'lina.hassan@gmail.com' => 'acct_1U5WonHsH3w0MXBX',
            'omar.saleh@gmail.com' => 'acct_1U5WqSHYAVGRuQPy',
            'sara.ahmad@gmail.com' => 'acct_1U5WrvHgwbhPZojz',
            'khaled.nasser@gmail.com' => 'acct_1U5WtWHDOju0tEnK',
            'maya.ibrahim@gmail.com' => 'acct_1U5Wv7HSZRNze63y',
            'yazan.mahmoud@gmail.com' => 'acct_1U5WwqHCxXyU1I2U',
            'rana.samir@gmail.com' => 'acct_1U5WyHQZcbNlIick',
            'tarek.ibrahim@gmail.com' => 'acct_1U5X09QVykWa8vt4',
            'nour.ali@gmail.com' => 'acct_1U5X1aHODoeaIpWR',
        ];

        foreach ($accounts as $email => $stripeAccountId) {

            $provider = ServiceProvider::whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })->first();

            if (!$provider) {
                continue;
            }

            $provider->update([
                'stripe_account_id' => $stripeAccountId,
            ]);
        }
    }
}