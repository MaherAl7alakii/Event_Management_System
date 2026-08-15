<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeAccountService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }


    public function createVerifiedAccount(string $name , string $email): string
    {
        $nameParts = explode(' ', trim($name), 2);
        $firstName = $nameParts[0] ?? 'Provider';
        $lastName  = $nameParts[1] ?? 'Service';


        $websiteUrl = str_contains(config('app.url'), 'localhost') || str_contains(config('app.url'), '127.0.0.1')
            ? 'https://www.my-platform.com'
            : config('app.url');

        try {
            $account = @$this->stripe->accounts->create([
                'type' => 'custom',
                'country' => 'US',
                'email' => $email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers'     => ['requested' => true],
                ],
                'business_type' => 'individual',
                'business_profile' => [
                    'mcc' => '5734',
                    'url' => $websiteUrl,
                ],
                'individual' => [
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'phone'      => '+10000000000',
                    'dob'        => ['day' => 1, 'month' => 1, 'year' => 1990],
                    'address'    => [
                        'line1'       => '123 Test St',
                        'city'        => 'San Francisco',
                        'state'       => 'CA',
                        'postal_code' => '94111',
                        'country'     => 'US',
                    ],
                    'ssn_last_4' => '0000',
                ],
                'tos_acceptance' => [
                    'date' => time(),
                    'ip'   => request()->ip() ?? '127.0.0.1',
                ],
                'external_account' => 'btok_us',
            ]);

            Log::info('Stripe Connect account created successfully', [
                'account_id' => $account->id,
                'email'      => $email,
            ]);

            return $account->id;

        } catch (ApiErrorException $e) {
            Log::error('Failed to create Stripe Connect account', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Stripe Account Creation Error: ' . $e->getMessage());
        }
    }

}
