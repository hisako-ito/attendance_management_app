<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    private const GUARD_USER = 'users';
    private const GUARD_ADMIN = 'admins';

    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::guard(self::GUARD_ADMIN)->check() && $request->routIs('admin.*')) {
            return redirect(RouteServiceProvider::ADMIN_HOME);
        }
        if (Auth::guard(self::GUARD_USER)->check() && $request->routIs('user.*')) {
            return redirect(RouteServiceProvider::HOME);
        }
        return $next($request);
    }
}
