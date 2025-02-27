<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function showAdminLoginForm(): View
    {
        return view('admin.auth.admin_login');
    }

    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect(RouteServiceProvider::ADMIN_HOME)->with('message', 'ログインしました');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }
}
