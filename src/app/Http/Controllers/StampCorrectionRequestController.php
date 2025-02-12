<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\BreakCorrectionRequest;
use App\Http\Requests\StampCorrectionRequest;
use Illuminate\Support\Carbon;

class StampCorrectionRequestController extends Controller
{
    public function stampCorrectionRequestCreate($attendance_id)
    {
        $attendance = Attendance::with('user', 'breaks', 'attendanceCorrectionRequests')->findOrFail($attendance_id);
        $user = $attendance->user;

        // 管理者か一般ユーザーか判定
        if (auth('admins')->check()) {
            // 管理者用の処理
            return view('admin.attendance_detail_admin', compact('user', 'attendance'));
        } elseif (auth('web')->check()) {
            // 一般ユーザー用の処理
            return view('attendance_detail_user', compact('user', 'attendance'));
        } else {
            abort(403, 'アクセス権限がありません');
        }

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

        return redirect("/attendance/{$attendance_id}")->with('message', '勤怠修正依頼が完了しました')->withInput();
    }

    public function stampCorrectionRequestShow(Request $request)
    {
        $user = Auth::user();
        $requests = AttendanceCorrectionRequest::where('user_id', $user->id)->get();
        $tab = $request->query('tab', 'pending_approval');

        if ($tab === 'approved') {
            $requests = AttendanceCorrectionRequest::where('is_approved', true)->get();
        } else {
            $requests = AttendanceCorrectionRequest::where('is_approved', false)->get();
        }

        return view('request_list', compact('user', 'requests', 'tab'));
    }
}
