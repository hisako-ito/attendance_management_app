<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function store(
        Request $request,
        CreateNewUser $creator
    ) {
        event(new Registered($user = $creator->create($request->all())));
        session()->put('unauthenticated_user', $user);
        return redirect()->route('verification.notice');
    }
}
