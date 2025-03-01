<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'admin_id',
        'is_approved',
    ];

    protected $casts = [
        'date' => 'date',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'is_approved' => 'boolean',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(admin::class);
    }

    public function breakCorrectionRequests()
    {
        return $this->hasMany(BreakCorrectionRequest::class, 'attendance_correction_request_id');
    }

    public function isApproved()
    {
        return $this->is_approved;
    }
}
