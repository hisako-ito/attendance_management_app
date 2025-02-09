<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class StampCorrectionRequestController extends Controller
{
    public function stampCorrectionRequestCreate($attendance_id)
    {
        $user = Auth::user();
        $attendance = Attendance::with('breaks')->find($attendance_id);
        return view('attendance_detail', compact('user', 'attendance'));
    }
}
