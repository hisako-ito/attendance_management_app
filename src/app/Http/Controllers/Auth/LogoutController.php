<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect('/login')->with('message', 'ログアウトしました');
    }
}
