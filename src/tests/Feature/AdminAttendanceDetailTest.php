<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //勤怠詳細画面に表示されるデータが選択したものになっている
    public function testAdminAttendanceDetailScreenIsTheOneYouSelected()
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
            'break_start' => '12:00',
            'break_end' => '13:00',
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

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '名前',
            $user->name,
            '日付',
            $today->format('Y年'),
            $today->format('n月j日'),
            '出勤・退勤',
            '09:00',
            '18:00',
            '休憩',
            '12:00',
            '13:00'
        ]);
    }

    //出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function testAdminErrorMessageStartTimeIsLaterThanEndTime()
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

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);

        $this->post('/attendance/' . $attendance->id, [
            'start_time' => Carbon::parse('18:00')->format('H:i'),
            'end_time' => Carbon::parse('09:00')->format('H:i'),
        ]);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です。');
    }

    //休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function testAdminErrorMessageBreakStartIsLaterThanEndTime()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00',
            'break_end' => '13:00',
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

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);

        $requestData = [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => ['19:00'],
            'break_end' => ['20:00'],
        ];

        $response = $this->post('/attendance/' . $attendance->id, $requestData);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['break_start.0']);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertSee('休憩時間が勤務時間外です。');
    }

    //休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function testAdminErrorMessageBreakEndIsLaterThanEndTime()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00',
            'break_end' => '13:00',
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

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);

        $requestData = [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => ['19:00'],
            'break_end' => ['20:00'],
        ];

        $response = $this->post('/attendance/' . $attendance->id, $requestData);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['break_end.0']);

        $response = $this->get('/attendance/' . $attendance->id);

        $response->assertSee('休憩時間が勤務時間外です。');
    }

    //備考欄が未入力の場合のエラーメッセージが表示される
    public function testAdminErrorMessageReasonFieldIsNotFilled()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00',
            'break_end' => '13:00',
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

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);

        $requestData = [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => ['19:00'],
            'break_end' => ['20:00'],
        ];

        $response = $this->post('/attendance/' . $attendance->id, $requestData);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['reason']);

        $response = $this->get('/attendance/' . $attendance->id);

        $response->assertSee('備考を記入してください。');
    }
}
