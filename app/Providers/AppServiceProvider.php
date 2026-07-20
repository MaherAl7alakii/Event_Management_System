<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function ($app) {
            return new StripeClient(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(CarbonInterval::minutes(15000));
        Passport::refreshTokensExpireIn(CarbonInterval::days(3));
        Passport::personalAccessTokensExpireIn(CarbonInterval::minutes(10));
    }
}
