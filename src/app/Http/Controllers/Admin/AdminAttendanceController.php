<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AdminAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins');
    }

    public function show()
    {
        $now = Carbon::now();

        return view('admin.admin_list', compact('now'));
    }
}
