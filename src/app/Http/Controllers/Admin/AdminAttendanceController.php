<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function adminAttendanceShow($year = null, $month = null, $day = null)
    {
        $currentDate = Carbon::now();
        $year = $year ?? $currentDate->year;
        $month = $month ?? $currentDate->month;
        $day = $day ?? $currentDate->day;

        $selectedDate = Carbon::create($year, $month, $day);

        $attendances = Attendance::with('user')
            ->whereDate('date', $selectedDate)->get();
        $previousDate = $selectedDate->copy()->subDay();
        $nextDate = $selectedDate->copy()->addDay();

        return view('admin.attendances_list', [
            'attendances' => $attendances,
            'selectedDate' => $selectedDate,
            'previousDate' => $previousDate,
            'nextDate' => $nextDate,
        ]);
    }

    public function userAttendanceShow($id, $year = null, $month = null)
    {
        $currentDate = Carbon::now();
        $year = $year ?? $currentDate->year;
        $month = $month ?? $currentDate->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $attendances = Attendance::with('user', 'breaks')
            ->where('user_id', $id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $user = User::find($id);

        $previousMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();

        return view('admin.user_attendances_list', [
            'user' => $user,
            'attendances' => $attendances,
            'currentMonth' => $startOfMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }
}
