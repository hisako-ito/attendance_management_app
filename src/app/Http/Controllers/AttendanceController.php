<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        $breakTime = $attendance
            ? BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest('break_start')
            ->first()
            : null;

        return view('index', compact('today', 'attendance', 'breakTime'));
    }

    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today->format('Y-m-d')],
            ['start_time' => Carbon::parse($request->input('start_time', Carbon::now()))]
        );
        return redirect()->route('attendance.index')->with('message', '出勤しました');
    }

    public function breakStart(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        BreakTime::create(
            [
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::parse($request->input('break_start', Carbon::now()))
            ]
        );

        return redirect()->route('attendance.index')->with('message', '休憩に入りました');
    }

    public function breakEnd(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        if ($attendance) {
            $breakTime = $attendance ? BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->latest('break_start')
                ->first() : null;
            if ($breakTime) {
                $breakTime->update(['break_end' => Carbon::parse($request->input('break_end', Carbon::now()))]);
            }
        }

        return redirect()->route('attendance.index')->with('message', '休憩から戻りました');
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        if ($attendance) {
            Attendance::where('id', $attendance->id)->update(
                ['end_time' => Carbon::parse($request->input('end_time', Carbon::now()))]
            );
        }

        $breakTime = BreakTime::where('attendance_id', $attendance->id)->exists();

        if (!$breakTime) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => null,
                'break_end' => null,
            ]);
        }

        return redirect()->route('attendance.index')->with('message', '退勤しました');
    }

    public function showAttendance($year = null, $month = null)
    {
        $user = Auth::user();
        $currentDate = Carbon::now();
        $year = $year ?? $currentDate->year;
        $month = $month ?? $currentDate->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])->get();

        $previousMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();

        return view('attendances_list', [
            'attendances' => $attendances,
            'currentMonth' => $startOfMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }
}
