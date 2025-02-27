<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;

class UserAttendanceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testAttendanceOutOfWorkDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();
        Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->delete();

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    public function testAttendanceAtWorkDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();
        Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('H:i'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    public function testAttendanceOnBreakDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('H:i'),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('H:i'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    public function testAttendanceClockedOutDisplayed()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('H:i'),
            'end_time' => Carbon::parse('18:00')->format('H:i'),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('H:i'),
            'break_end' => Carbon::parse('13:00')->format('H:i'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
