<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Notification::extend('verifyEmail', function ($app) {
            return new CustomVerifyEmail;
        });
    }
}
