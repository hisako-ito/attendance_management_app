<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AttendanceDetailCorrectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function testErrorMessageStartTimeIsLaterThanEndTime()
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

        $this->post('/attendance/' . $attendance->id, [
            'start_time' => Carbon::parse('18:00')->format('H:i'),
            'end_time' => Carbon::parse('09:00')->format('H:i'),
        ]);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です。');
    }

    //休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function testErrorMessageBreakStartIsLaterThanEndTime()
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

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

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
    public function testErrorMessageBreakEndIsLaterThanEndTime()
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

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

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
    public function testErrorMessageReasonFieldIsNotFilled()
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

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

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

    //修正申請処理が実行される
    public function testCorrectRequestExecution()
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

        $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $requestData = [
            'date1' => $today->format('Y年'),
            'date2' => $today->format('n月j日'),
            'start_time' => '10:00',
            'end_time' => '19:00',
            'break_start' => ['13:00'],
            'break_end' => ['14:00'],
            'reason' => '残業のため',
        ];

        $this->post('/attendance/' . $attendance->id, $requestData);

        $correctionRequest = AttendanceCorrectionRequest::latest()->first();

        $response = $this->get('/stamp_correction_request/list');

        $correctionRequests = AttendanceCorrectionRequest::all();
        dump(['管理者ログイン時のデータ' => $correctionRequests->toArray()]);

        $response->assertSeeInOrder([
            '承認待ち',
            $user->name,
            Carbon::parse($correctionRequest['date'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '残業のため',
            Carbon::parse($correctionRequest['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
        ]);
    }


    //「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function testCorrectRequestDisplayedInTheWaitingForApproval()
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

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);

        $requestData = [
            'date1' => $today->format('Y年'),
            'date2' => $today->format('n月j日'),
            'start_time' => '10:00',
            'end_time' => '19:00',
            'break_start' => ['13:00'],
            'break_end' => ['14:00'],
            'reason' => '残業のため',
        ];

        $response = $this->post('/attendance/' . $attendance->id, $requestData);

        $expectedData = [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('10:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('19:00')->format('Y-m-d H:i:s'),
            'is_approved' => 0,
            'reason' => '残業のため',
        ];

        $this->assertDatabaseHas('attendance_correction_requests', $expectedData);

        $response = $this->get('/stamp_correction_request/list');

        $response->assertSeeInOrder([
            '承認待ち',
            $user->name,
            $today->format('Y/m/d'),
            '残業のため',
            $today->format('Y/m/d'),
        ]);
    }
}
