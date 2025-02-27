<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\BreakCorrectionRequest;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminAttendanceCorrectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //承認待ちの修正申請が全て表示されている
    public function testAwaitingApprovalRequestsDisplayed()
    {
        $today = Carbon::today();

        $user1 = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $correctionRequest1 = AttendanceCorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('19:00')->format('Y-m-d H:i:s'),
            'reason' => '残業のため',
            'is_approved' => false,
        ]);

        $correctionRequest2 = AttendanceCorrectionRequest::create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance2->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('10:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('21:00')->format('Y-m-d H:i:s'),
            'reason' => '遅延のため',
            'is_approved' => false,
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

        $response = $this->get('/stamp_correction_request/list?tab=pending_approval');

        $response->assertSeeInOrder([
            '承認待ち',
            $user1->name,
            Carbon::parse($correctionRequest1['date'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '残業のため',
            Carbon::parse($correctionRequest1['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
        ]);

        $response->assertSeeInOrder([
            '承認待ち',
            $user1->name,
            Carbon::parse($correctionRequest2['date'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '遅延のため',
            Carbon::parse($correctionRequest2['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
        ]);
    }

    //承認済みの修正申請が全て表示されている
    public function testAwaitingApprovedRequestsDisplayed()
    {
        $today = Carbon::today();

        $user1 = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $correctionRequest1 = AttendanceCorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('19:00')->format('Y-m-d H:i:s'),
            'reason' => '残業のため',
            'is_approved' => true,
        ]);

        $correctionRequest2 = AttendanceCorrectionRequest::create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance2->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('10:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('21:00')->format('Y-m-d H:i:s'),
            'reason' => '遅延のため',
            'is_approved' => true,
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

        $response = $this->get('/stamp_correction_request/list?tab=approved');

        $response->assertSeeInOrder([
            '承認済み',
            $user1->name,
            Carbon::parse($correctionRequest1['date'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '残業のため',
            Carbon::parse($correctionRequest1['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
        ]);

        $response->assertSeeInOrder([
            '承認済み',
            $user1->name,
            Carbon::parse($correctionRequest2['date'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '遅延のため',
            Carbon::parse($correctionRequest2['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
        ]);
    }

    //修正申請の詳細内容が正しく表示されている
    public function testCorrectionRequestAreCorrectAttendanceDetailScreen()
    {
        $today = Carbon::today();

        $user1 = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('18:00')->format('Y-m-d H:i:s'),
        ]);

        $correctionRequest1 = AttendanceCorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('19:00')->format('Y-m-d H:i:s'),
            'reason' => '残業のため',
            'is_approved' => true,
        ]);

        $correctionRequest2 = AttendanceCorrectionRequest::create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance2->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('10:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('21:00')->format('Y-m-d H:i:s'),
            'reason' => '遅延のため',
            'is_approved' => true,
        ]);

        $AdminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($AdminUser, 'admin');

        $response = $this->get('/stamp_correction_request/list?tab=approved');

        $response->assertSeeInOrder([
            '承認済み',
            $user1->name,
            Carbon::parse($correctionRequest1['date'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '残業のため',
            Carbon::parse($correctionRequest1['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '詳細'
        ]);

        $response->assertSeeInOrder([
            '承認済み',
            $user1->name,
            Carbon::parse($correctionRequest2['date'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '遅延のため',
            Carbon::parse($correctionRequest2['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d'),
            '詳細'
        ]);

        $response = $this->get('/stamp_correction_request/approve/' . $correctionRequest1->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '名前',
            $user1->name,
            '日付',
            $today->format('Y年'),
            $today->format('n月j日'),
            '出勤・退勤',
            '09:00',
            '19:00',
            '備考',
            '残業のため',
        ]);

        $response = $this->get('/stamp_correction_request/approve/' . $correctionRequest2->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '名前',
            $user2->name,
            '日付',
            $today->format('Y年'),
            $today->format('n月j日'),
            '出勤・退勤',
            '10:00',
            '21:00',
            '備考',
            '遅延のため',
        ]);
    }

    //修正申請の承認処理が正しく行われる
    public function testCorrectApprovalProcessingOfCorrectionRequest()
    {
        $today = Carbon::today();

        $user = User::factory()->create([
            'email' => 'general1@gmail.com',
            'password' => Hash::make('password'),
        ]);

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

        $attendanceCorrectionRequest = AttendanceCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('19:00')->format('Y-m-d H:i:s'),
            'reason' => '残業のため',
            'is_approved' => false,
        ]);

        BreakCorrectionRequest::create([
            'attendance_correction_request_id' => $attendanceCorrectionRequest->id,
            'break_start' => Carbon::parse('12:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
        ]);

        $adminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($adminUser, 'admin');

        $response = $this->get('/stamp_correction_request/approve/' . $attendanceCorrectionRequest->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '名前',
            $user->name,
            '日付',
            $today->format('Y年'),
            $today->format('n月j日'),
            '出勤・退勤',
            '09:00',
            '19:00',
            '備考',
            '残業のため',
        ]);

        $requestData = [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '19:00',
            'break_start' => ['13:00'],
            'break_end' => ['14:00'],
            'admin_id' => $adminUser->id,
            'reason' => '残業のため',
            'is_approved' => true,
        ];

        $this->post('/stamp_correction_request/approve/' . $attendanceCorrectionRequest->id, $requestData);


        $response = $this->get('/stamp_correction_request/approve/' . $attendanceCorrectionRequest->id);
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '承認済み',
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
            'start_time' => Carbon::parse('09:00')->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse('19:00')->format('Y-m-d H:i:s'),
            'reason' => '残業のため',
        ]);

        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('13:00')->format('Y-m-d H:i:s'),
            'break_end' => Carbon::parse('14:00')->format('Y-m-d H:i:s'),
        ]);
    }
}
