<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
    public function testAdminShowUserList()
    {
        $user1 = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
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

        $response = $this->get('/admin/staff/list');

        $response->assertSee('名前', 'メールアドレス');
        $response->assertSee($user1->name, $user1->email);
        $response->assertSee($user2->name, $user1->email);
    }

    //ユーザーの勤怠情報が正しく表示される
    public function testUserAttendanceInformationIsDisplayedCorrectly()
    {
        $user = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
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

        $response = $this->get('/admin/staff/list');

        $response->assertSee('詳細');
        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name . "さんの勤怠");
    }

    //「前月」を押下した時に表示月の前月の情報が表示される
    public function testAdminPreviousMonthInformationDisplayedPreviousMonthIsPressed()
    {
        $user = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $year = $today->year;
        $month = $today->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $previousMonth = $startOfMonth->copy()->subMonth();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $previousMonth->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
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

        $response = $this->get('/admin/staff/list');

        $response->assertSee('詳細');
        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name . "さんの勤怠");
        $response = $this->get("/admin/attendance/staff/{$user->id}/{$previousMonth->year}/{$previousMonth->month}");

        $attendanceDate = Carbon::parse($attendance->date)->isoFormat('MM/DD(ddd)');
        $response->assertSeeInOrder([
            $attendanceDate,
            '09:00',
            '18:00',
            '01:00',
            '08:00',
            '詳細'
        ]);
    }

    //「翌月」を押下した時に表示月の前月の情報が表示される
    public function testAdminNextMonthInformationDisplayedPreviousMonthIsPressed()
    {
        $user = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $today = Carbon::today();

        $year = $today->year;
        $month = $today->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $NextMonth = $startOfMonth->copy()->addMonth();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $NextMonth->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
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

        $response = $this->get('/admin/staff/list');

        $response->assertSee('詳細');
        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name . "さんの勤怠");
        $response = $this->get("/admin/attendance/staff/{$user->id}/{$NextMonth->year}/{$NextMonth->month}");

        $attendanceDate = Carbon::parse($attendance->date)->isoFormat('MM/DD(ddd)');
        $response->assertSeeInOrder([
            $attendanceDate,
            '09:00',
            '18:00',
            '01:00',
            '08:00',
            '詳細'
        ]);
    }

    //「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function testAttendanceDetailInformationDisplayedDetailButtonIsPressed()
    {
        $user = User::factory()->create([
            'email' => 'general1@gmail.com',
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
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
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

        $response = $this->get('/admin/staff/list');

        $response->assertSee('詳細');
        $response = $this->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name . "さんの勤怠");
        $response = $this->get("/admin/attendance/staff/{$user->id}/{$today->year}/{$today->month}");

        $attendanceDate = Carbon::parse($attendance->date)->isoFormat('MM/DD(ddd)');
        $response->assertSeeInOrder([
            $attendanceDate,
            '09:00',
            '18:00',
            '01:00',
            '08:00',
            '詳細'
        ]);

        $response = $this->get('/attendance/' . $attendance->id);

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
            '13:00',
        ]);
        $response->assertStatus(200);
    }
}
