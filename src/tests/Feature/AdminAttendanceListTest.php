<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //その日になされた全ユーザーの勤怠情報が正確に確認できる
    public function testAllUsersAttendanceInformationDisplayedOnThatDay()
    {
        $user1 = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'general3@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $today = Carbon::today();

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '19:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'break_start' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('14:00')->format('Y-m-d H:i:s'),
        ]);

        $adminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/attendance/list');

        $this->assertAuthenticatedAs($adminUser, 'admin');

        $response = $this->get('/admin/attendance/list');

        $response->assertSee($user1->name, '09:00', '18:00', '01:00', '08:00');
        $response->assertSee($user2->name, '10:00', '19:00', '01:00', '08:00');
    }

    //遷移した際に現在の日付が表示される
    public function testCurrentDateDisplayedWhenTransitioningAdminAtｔｔendanceList()
    {
        $adminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $today = Carbon::today()->format('Y/m/d');

        $response->assertRedirect('/admin/attendance/list');

        $this->assertAuthenticatedAs($adminUser, 'admin');

        $response = $this->get('/admin/attendance/list');
        $response->assertSee($today);
    }

    //「前日」を押下した時に前の日の勤怠情報が表示される
    public function testPreviousDateDisplayedWhenTransitioningAdminAttendanceList()
    {
        $adminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $today = Carbon::today();
        $previousDate = $today->copy()->subDay();
        $year = $previousDate->year;
        $month = $previousDate->month;
        $day = $previousDate->day;

        $response->assertRedirect('/admin/attendance/list');

        $this->assertAuthenticatedAs($adminUser, 'admin');

        $response = $this->get('/admin/attendance/list');
        $response->assertSee('前日');

        $response = $this->get("admin/attendance/list/{$year}/{$month}/{$day}");

        $response->assertStatus(200);
        $response->assertSee($previousDate->format('Y/m/d'));
    }

    //「翌日」を押下した時に次の日の勤怠情報が表示される
    public function testNextDateDisplayedWhenTransitioningAdminAttendanceList()
    {
        $adminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $today = Carbon::today();
        $nextDate = $today->copy()->addDay();
        $year = $nextDate->year;
        $month = $nextDate->month;
        $day = $nextDate->day;

        $response->assertRedirect('/admin/attendance/list');

        $this->assertAuthenticatedAs($adminUser, 'admin');

        $response = $this->get('/admin/attendance/list');
        $response->assertSee('翌日');

        $response = $this->get("admin/attendance/list/{$year}/{$month}/{$day}");

        $response->assertStatus(200);
        $response->assertSee($nextDate->format('Y/m/d'));
    }
}
