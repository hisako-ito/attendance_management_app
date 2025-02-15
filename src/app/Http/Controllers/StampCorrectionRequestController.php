<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use App\Models\BreakCorrectionRequest;
use App\Http\Requests\StampCorrectionRequest;
use Illuminate\Support\Carbon;

class StampCorrectionRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stampCorrectionRequestCreate($attendance_id)
    {
        $attendance = Attendance::with('user', 'breaks', 'attendanceCorrectionRequests')->findOrFail($attendance_id);
        $user = $attendance->user;

        return view('attendance_detail', compact('user', 'attendance'));
    }

    public function stampCorrectionRequestStore($attendance_id, StampCorrectionRequest $request)
    {
        $user = Auth::user();
        $attendance = Attendance::with('breaks')->find($attendance_id);

        $formattedDate = preg_replace('/\s+/', '', $request->date1) . preg_replace('/\s+/', '', $request->date2);
        $formattedDate = mb_convert_encoding($formattedDate, 'UTF-8', 'auto');

        try {
            $date = Carbon::createFromFormat('Y年m月d日', trim($formattedDate))->format('Y-m-d');
        } catch (\Exception $e) {
            dd('日付変換エラー', $formattedDate);
            return back()->withErrors(['date' => '日付の形式が正しくありません。']);
        }

        if (auth('web')->check()) {
            $correctionRequest = AttendanceCorrectionRequest::create([
                'attendance_id' => $attendance_id,
                'user_id' => $user->id,
                'date' => $date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'reason' => $request->reason,
            ]);

            if (!empty($request->break_start) && is_array($request->break_start)) {
                foreach ($request->break_start as $key => $breakStart) {
                    if (!empty($breakStart) && !empty($request->break_end[$key] ?? '')) {
                        BreakCorrectionRequest::create([
                            'attendance_correction_request_id' => $correctionRequest->id,
                            'break_start' => $breakStart,
                            'break_end' => $request->break_end[$key] ?? '',
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

    public function stampCorrectionRequestShow(Request $request)
    {
        if (auth('web')->check()) {
            $user = Auth::user();
            $tab = $request->query('tab', 'pending_approval');

            if ($tab === 'approved') {
                $requests = AttendanceCorrectionRequest::where('user_id', $user->id)
                    ->where('is_approved', true)
                    ->get();
            } elseif ($tab === 'pending_approval') {
                $requests = AttendanceCorrectionRequest::where('user_id', $user->id)
                    ->where('is_approved', false)
                    ->get();
            } else {
                $requests = collect();
            }

            return view('request_list_user', compact('user', 'requests', 'tab'));
        } elseif (auth('admin')->check()) {
            $tab = $request->query('tab', 'pending_approval');

            if ($tab === 'approved') {
                $requests = AttendanceCorrectionRequest::with('user')
                    ->where('is_approved', true)
                    ->get();
            } elseif ($tab === 'pending_approval') {
                $requests = AttendanceCorrectionRequest::with('user')
                    ->where('is_approved', false)
                    ->get();
            } else {
                $requests = collect();
            }

            return view('admin.request_list_admin', compact('requests', 'tab'));
        } else {
            abort(403, 'アクセス権限がありません');
        }
    }
}
