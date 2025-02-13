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
        'is_approved',
    ];

    protected $casts = [
        'date' => 'datetime',
        'break_start' => 'string',
        'break_end' => 'string',
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

    public function breakCorrectionRequests()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function isApproved()
    {
        return $this->is_approved;
    }
}
