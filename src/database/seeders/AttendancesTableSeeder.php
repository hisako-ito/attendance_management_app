<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendances = [];

        $users = [1, 2];

        $startDate = Carbon::yesterday()->subMonth();
        $endDate = Carbon::yesterday();

        foreach ($users as $user_id) {
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                $startTime = $date->copy()->setHour(9)->setMinute(0)->setSecond(0);
                $endTime = $date->copy()->setHour(18)->setMinute(0)->setSecond(0);

                $attendances[] = [
                    'user_id' => $user_id,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $startTime->format('Y-m-d H:i:s'),
                    'end_time' => $endTime->format('Y-m-d H:i:s'),
                    'admin_id' => null,
                    'reason' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $date->addDay();
            }
        }

        DB::table('attendances')->insert($attendances);
    }
}
