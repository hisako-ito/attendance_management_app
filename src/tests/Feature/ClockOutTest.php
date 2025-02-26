<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //退勤ボタンが正しく機能する
    public function testAttendanceAbilityClockOut()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $today = Carbon::today();

        Attendance::create([
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
        $response->assertSee('退勤');

        $this->post('/attendance/clock-out', [
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }

    //退勤時刻が管理画面で確認できる
    public function testAbilityToClockOutTimeOnTheManagementScreen()
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

        $this->post('/attendance/clock-in', [
            'start_time' => Carbon::parse('09:00')->format('H:i'),
        ]);

        $this->post('/attendance/clock-out', [
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);
        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤済');

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

        $response = $this->get('admin/attendance/list');
        $response->assertSee($today->format('Y/m/d'));
        $response->assertSee('18:00');
    }
}
