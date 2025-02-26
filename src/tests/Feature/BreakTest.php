<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Tests\TestCase;

class BreakTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //休憩ボタンが正しく機能する
    public function testAttendanceAbilityBreakStart()
    {
        $user = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $today = Carbon::today();

        $response = $this->post('/login', [
            'email' => "general1@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務外');
        $response->assertSee('出勤');

        $this->post('/attendance/clock-in', [
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $attendance = Attendance::where([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
        ])->first();

        $this->post('/attendance/break-start', [
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s')
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    //休憩は一日に何回でもできる
    public function testBreaksCanBeTakenManyTimesPerDay()
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
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $this->post('/attendance/break-start', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');

        $this->post('/attendance/break-end', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');
    }

    //休憩戻ボタンが正しく機能する
    public function testAttendanceAbilityBreakBack()
    {
        $user = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->post('/login', [
            'email' => "general1@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $this->post('/attendance/break-start', [
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');

        $this->post('/attendance/break-end', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');
    }

    //休憩戻は一日に何回でもできる
    public function testBreakBackCanBeTakenManyTimesPerDay()
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
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $this->post('/attendance/break-start', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');

        $this->post('/attendance/break-end', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $this->post('/attendance/break-start', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');
    }

    //休憩時刻が管理画面で確認できる
    public function testAbilityToBreakTimeOnTheManagementScreen()
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
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $this->post('/attendance/break-start', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response->assertSee('休憩戻');

        $this->post('/attendance/break-end', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $AdminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/attendance/list');

        $this->assertAuthenticatedAs($AdminUser, 'admin');

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('admin/attendance/list');
        $response->assertSee($today->format('Y/m/d'));
        $response->assertSee('01:00');
    }
}
