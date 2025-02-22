<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClockInTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //出勤ボタンが正しく機能する
    public function testAttendanceAbilityClockIn()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務外');
        $response->assertSee('出勤');

        $this->post('/attendance/clock-in', [
            'start_time' => Carbon::parse('09:00')->format('H:i'),
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
        ]);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    //出勤は一日一回のみできる
    public function testAbilityToProcessAttendanceOnlyOncePerDay()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
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

        $this->post('/attendance/clock-out', [
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertDontSee('出勤');
    }

    //管理画面に出勤時刻が正確に記録されている
    public function testAbilityToCheckAttendanceTimeOnTheManagementScreen()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
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

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('admin/attendance/list');
        $response->assertSee($today->format('Y/m/d'));
        $response->assertSee('09:00');
    }
}
