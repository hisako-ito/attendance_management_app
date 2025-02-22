<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'admin_id',
        'reason',
    ];

    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceCorrectionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }

    public function getTotalBreakTimeAttribute()
    {
        $totalMinutes = $this->breaks->reduce(function ($carry, $break) {
            if (!$break->break_end) {
                return $carry;
            }

            $breakStart = Carbon::parse($break->break_start);
            $breakEnd = Carbon::parse($break->break_end);

            return $carry + $breakStart->diffInMinutes($breakEnd);
        }, 0);

        return sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60);
    }

    public function getTotalWorkTimeAttribute()
    {
        if (!$this->end_time) {
            return null;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        $workMinutes = $start->diffInMinutes($end);

        $breakMinutes = $this->breaks->reduce(function ($carry, $break) {
            if (!$break->break_end) {
                return $carry;
            }

            $breakStart = Carbon::parse($break->break_start);
            $breakEnd = Carbon::parse($break->break_end);

            return $carry + $breakStart->diffInMinutes($breakEnd);
        }, 0);

        $netMinutes = $workMinutes - $breakMinutes;

        return sprintf('%02d:%02d', intdiv($netMinutes, 60), $netMinutes % 60);
    }
}
