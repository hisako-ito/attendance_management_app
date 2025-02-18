<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            return redirect()->route('attendance.index')->with('message', 'ログインしました');
        }

        return back()->withErrors(['email' => 'ログイン情報が正しくありません']);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect('/login')->with('message', 'ログアウトしました');
    }
}
