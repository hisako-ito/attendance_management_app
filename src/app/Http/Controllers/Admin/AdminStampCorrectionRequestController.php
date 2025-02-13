<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\AttendanceCorrectionRequest;
use App\Models\BreakCorrectionRequest;
use App\Http\Requests\StampCorrectionRequest;

class AdminStampCorrectionRequestController extends Controller
{
    public function adminStampCorrectionRequestCreate($attendance_id)
    {
        $attendance = Attendance::with('user', 'breaks', 'attendanceCorrectionRequests')->findOrFail($attendance_id);
        $user = $attendance->user;

        return view('admin.attendance_detail', compact('user', 'attendance'));
    }

    public function adminStampCorrectionRequestStore($attendance_id, StampCorrectionRequest $request)
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
}
