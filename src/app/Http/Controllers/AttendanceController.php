<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $now = Carbon::now();

        return view('index', compact('now'));
    }

    public function clockIn(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }
        $user = Auth::user();
        $today = Carbon::now();
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['start_time' => Carbon::now()]
        );
        return redirect()->route('attendance.index')->with('message', '出勤しました');
    }

    public function breakStart(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $user = Auth::user();
        $today = Carbon::today();


        return redirect()->route('attendance.index')->with('message', '休憩に入りました');
    }
}
