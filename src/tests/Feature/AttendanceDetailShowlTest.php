<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceDetailShowTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function testAttendanceDetailScreenNameDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder(['名前', $user->name]);
    }

    //勤怠詳細画面の「日付」が選択した日付になっている
    public function testAttendanceDetailScreenDateDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder(['日付', $today->format('Y年'), $today->format('n月j日')]);
    }

    //「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function testAttendanceDetailScreenClockInAndClockOutTimeDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder(['出勤・退勤', '09:00', '18:00']);
    }

    //「休憩」にて記されている時間がログインユーザーの打刻と一致している
    public function testAttendanceDetailScreenBreakStartAndBreakEndTimeDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder(['休憩', '12:00', '13:00']);
    }
}
