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

class UserAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //自分が行った勤怠情報が全て表示されている
    public function testAllAttendanceInformationDisplayed()
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

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($today->isoformat('MM/DD(ddd)'));
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('01:00');
        $response->assertSee('08:00');
    }

    //勤怠一覧画面に遷移した際に現在の月が表示される
    public function testCurrentMonthDisplayedAttendanceListScreen()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $currentMonth = Carbon::now()->month;

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($currentMonth);
    }

    //「前月」を押下した時に表示月の前月の情報が表示される
    public function testPreviousMonthDisplayedAttendanceListScreen()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $previousMonthDate = Carbon::now()->subMonth()->day(1);

        $year = $previousMonthDate->year;
        $previousMonth = $previousMonthDate->month;

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $previousMonthDate->format('Y-m-d'),
            'start_time' => $previousMonthDate->setTime(9, 0, 0)->format('Y-m-d H:i:s'),
            'end_time' => $previousMonthDate->setTime(18, 0, 0)->format('Y-m-d H:i:s'),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => $previousMonthDate->setTime(12, 0, 0)->format('Y-m-d H:i:s'),
            'break_end' => $previousMonthDate->setTime(13, 0, 0)->format('Y-m-d H:i:s'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/list/' . $year . '/' . $previousMonth);
        $response->assertStatus(200);
        $response->assertSee($previousMonthDate->setTime(9, 0, 0)->format('H:i'));
        $response->assertSee($previousMonthDate->setTime(1, 0, 0)->format('H:i'));
        $response->assertSee($previousMonthDate->setTime(18, 0, 0)->format('H:i'));
    }

    //「翌月」を押下した時に表示月の前月の情報が表示される
    public function testNextMonthDisplayedAttendanceListScreen()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $nextMonthDate = Carbon::now()->addMonth()->day(1);

        $year = $nextMonthDate->year;
        $previousMonth = $nextMonthDate->month;

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $nextMonthDate->format('Y-m-d'),
            'start_time' => $nextMonthDate->setTime(9, 0, 0)->format('Y-m-d H:i:s'),
            'end_time' => $nextMonthDate->setTime(18, 0, 0)->format('Y-m-d H:i:s'),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => $nextMonthDate->setTime(12, 0, 0)->format('Y-m-d H:i:s'),
            'break_end' => $nextMonthDate->setTime(13, 0, 0)->format('Y-m-d H:i:s'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/list/' . $year . '/' . $previousMonth);
        $response->assertStatus(200);
        $response->assertSee($nextMonthDate->setTime(9, 0, 0)->format('H:i'));
        $response->assertSee($nextMonthDate->setTime(1, 0, 0)->format('H:i'));
        $response->assertSee($nextMonthDate->setTime(18, 0, 0)->format('H:i'));
    }

    //「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function testPressingDetailAttendanceDetailScreenDisplayed()
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

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee($today->format('Y年'));
        $response->assertSee($today->format('n月j日'));
        $response->assertSee('09:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
        $response->assertSee('18:00');
    }
}
