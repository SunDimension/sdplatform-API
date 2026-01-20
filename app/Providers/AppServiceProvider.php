<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Ignore vendor auth migrations
        Passport::ignoreMigrations();
        Sanctum::ignoreMigrations();
    }

    public function boot(): void
    {
        //
    }
}
