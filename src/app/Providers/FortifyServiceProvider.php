<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::loginView(function () {
            return view('admin.auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        //デフォルトのログイン機能にあるフォームリクエストを自作のものに代替するため、サービスコンテナにバインド
        app()->bind(FortifyLoginRequest::class, LoginRequest::class);

        Fortify::authenticateUsing(function (LoginRequest $request) {
            $admin = Admin::where('email', $request->email)->first();

            if ($admin && Hash::check($request->password, $admin->password)) {
                return $admin;
            }

            return null;
        });

        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('attendance.index');
                }
            };
        });
    }
}
