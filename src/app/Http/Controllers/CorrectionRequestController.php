<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use App\Models\BreakCorrectionRequest;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CorrectionRequestController extends Controller
{
    public function showCorrectionRequestForm($attendance_id)
    {
        $attendance = Attendance::with('user', 'breaks', 'attendanceCorrectionRequests')
            ->findOrFail($attendance_id);

        $user = $attendance->user;
        $latestCorrectionRequest = $attendance
            ->attendanceCorrectionRequests
            ->sortByDesc('created_at')
            ->first();

        return view('attendance_detail', compact('user', 'attendance', 'latestCorrectionRequest'));
    }

    public function storeCorrectionRequest($attendance_id, CorrectionRequest $request)
    {
        $user = Auth::user();
        $attendance = Attendance::with('breaks')
            ->find($attendance_id);

        $formattedDate = trim($request->date1) . ' ' . trim($request->date2);

        try {
            $date = Carbon::createFromFormat('Y年n月j日', str_replace(' ', '', $formattedDate))->format('Y-m-d');
        } catch (\Exception $e) {
            return back()->withErrors(['date' => '日付の形式が正しくありません。']);
        }

        $formattedStartTime = Carbon::parse($date . ' ' . $request->start_time)->format('Y-m-d H:i:s');
        $formattedEndTime = Carbon::parse($date . ' ' . $request->end_time)->format('Y-m-d H:i:s');

        if (auth('web')->check()) {
            $correctionRequest = AttendanceCorrectionRequest::create([
                'attendance_id' => $attendance_id,
                'user_id' => $user->id,
                'date' => $date,
                'start_time' => $formattedStartTime,
                'end_time' => $formattedEndTime,
                'reason' => $request->reason,
            ]);

            if (!empty($request->break_start) && is_array($request->break_start)) {
                foreach ($request->break_start as $key => $breakStart) {
                    if (!empty($breakStart) && !empty($request->break_end[$key] ?? '')) {
                        BreakCorrectionRequest::create([
                            'attendance_correction_request_id' => $correctionRequest->id,
                            'break_start' => Carbon::parse($date . ' ' . $breakStart)->format('Y-m-d H:i:s'),
                            'break_end' => Carbon::parse($date . ' ' . $request->break_end[$key])->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            return redirect()->route('attendance.detail', ['id' => $attendance_id])->with('message', '勤怠修正依頼が完了しました')->withInput();
        } elseif (auth('admin')->check()) {
            $attendance = Attendance::findOrFail($attendance_id);
            $breakTimes = BreakTime::where('attendance_id', $attendance_id)->get();
            $attendance->update([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'admin_id' => $user->id,
                'reason' => $request->reason,
            ]);

            if (!empty($request->break_start) && is_array($request->break_start)) {
                $breakTimes->each(function ($breakTime, $key) use ($request) {
                    if (!empty($request->break_start[$key]) && !empty($request->break_end[$key])) {
                        $breakTime->update([
                            'break_start' => $request->break_start[$key],
                            'break_end' => $request->break_end[$key],
                        ]);
                    }
                });
            }

            return redirect()->route('attendance.detail', ['id' => $attendance_id])->with('message', '勤怠修正が完了しました')->withInput();
        } else {
            abort(403, 'アクセス権限がありません');
        }
    }

    public function showCorrectionRequests(Request $request)
    {
        if (auth('web')->check()) {
            $user = Auth::user();
            $tab = $request->query('tab', 'pending_approval');

            if ($tab === 'approved') {
                $correctionRequests = AttendanceCorrectionRequest::where('user_id', $user->id)
                    ->where('is_approved', true)
                    ->get();
            } elseif ($tab === 'pending_approval') {
                $correctionRequests = AttendanceCorrectionRequest::where('user_id', $user->id)
                    ->where('is_approved', false)
                    ->get();
            } else {
                $correctionRequests = collect();
            }

            return view('request_list_user', compact('user', 'correctionRequests', 'tab'));
        } elseif (auth('admin')->check()) {
            $tab = $request->query('tab', 'pending_approval');

            if ($tab === 'approved') {
                $correctionRequests = AttendanceCorrectionRequest::with('user')
                    ->where('is_approved', true)
                    ->get();
            } elseif ($tab === 'pending_approval') {
                $correctionRequests = AttendanceCorrectionRequest::with('user')
                    ->where('is_approved', false)
                    ->get();
            } else {
                $correctionRequests = collect();
            }

            return view('admin.request_list_admin', compact('correctionRequests', 'tab'));
        } else {
            abort(403, 'アクセス権限がありません');
        }
    }
}
