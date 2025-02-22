<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BreaksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $breaks = [];
        $attendances = DB::table('attendances')->get();

        foreach ($attendances as $attendance) {
            $startTime = Carbon::parse($attendance->start_time);

            $breakStart = $startTime->copy()->setHour(12)->setMinute(0)->setSecond(0);
            $breakEnd = $startTime->copy()->setHour(13)->setMinute(0)->setSecond(0);

            $breaks[] = [
                'attendance_id' => $attendance->id,
                'break_start' => $breakStart->format('Y-m-d H:i:s'),
                'break_end' => $breakEnd->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('breaks')->insert($breaks);
    }
}
