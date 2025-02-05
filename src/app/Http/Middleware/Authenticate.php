<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected function redirectTo($request)
    // {
    //     if (! $request->expectsJson()) {
    //         return route('login');
    //     }
    // }

    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if (Route::is(config('route.admin_wild'))) {
                return route(config('route.admin_login_route'));
            }
            return route(config('route.user_login_route'));
        }
    }
}
