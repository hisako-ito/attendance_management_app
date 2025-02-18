<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;

class AdminCorrectionRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function showRequestApprove($id)
    {
        $correctionRequest = AttendanceCorrectionRequest::with('user', 'breakCorrectionRequests')->find($id);

        return view('admin.request_approve', compact('correctionRequest'));
    }

    public function storeRequestApprove($id, Request $request)
    {
        $correctionRequest = AttendanceCorrectionRequest::find($id);
        $correctionRequest->update([
            'is_approved' => true,
        ]);

        $attendance = Attendance::findOrFail($correctionRequest->attendance_id);
        $breakTimes = BreakTime::where('attendance_id', $correctionRequest->attendance_id)->get();
        $attendance->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
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

        return redirect()->route('request.approve', ['id' => $correctionRequest->id])->with('message', '承認されました');
    }
}
