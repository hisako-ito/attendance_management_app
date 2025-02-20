<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAttendanceController extends Controller
{
    public function showAdminAttendance($year = null, $month = null, $day = null)
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

        return view('admin.attendances_list_admin', [
            'attendances' => $attendances,
            'selectedDate' => $selectedDate,
            'previousDate' => $previousDate,
            'nextDate' => $nextDate,
            'tody' => $currentDate
        ]);
    }

    public function showUserAttendance($id, $year = null, $month = null)
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

    public function exportUserAttendance($id, $year = null, $month = null, Request $request)
    {
        $currentDate = Carbon::now();
        $year = $year ?? $currentDate->year;
        $month = $month ?? $currentDate->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $attendances = Attendance::with('user', 'breaks')
            ->where('user_id', $id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->get();

        $user = User::find($id);

        $previousMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();

        $csvHeader = ['日付', '出勤', '退勤', '休憩', '合計'];

        $response = new StreamedResponse(function () use ($attendances, $csvHeader, $user, $year, $month) {
            $createCsvFile = fopen('php://output', 'w');

            $userHeader = [$user->name . 'さんの' . $year . '年' . $month . '月の勤怠'];
            mb_convert_variables('SJIS-win', 'UTF-8', $userHeader);
            fputcsv($createCsvFile, $userHeader);

            mb_convert_variables('SJIS-win', 'UTF-8', $csvHeader);
            fputcsv($createCsvFile, $csvHeader);

            foreach ($attendances as $attendance) {
                $startTime = $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '';
                $endTime = $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '';

                $workTime = $attendance->total_work_time;
                $breakTime = $attendance->total_break_time;

                $row = [
                    $attendance->date->format('Y/m/d'),
                    $startTime,
                    $endTime,
                    $breakTime,
                    $workTime,
                ];

                mb_convert_variables('SJIS-win', 'UTF-8', $row);
                fputcsv($createCsvFile, $row);
            }
            fclose($createCsvFile);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="monthly_attendance.csv"',
        ]);

        return $response;
    }
}
