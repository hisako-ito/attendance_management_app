<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionRequestController;
use App\Http\Controllers\Admin\AdminCorrectionRequestController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LogoutController::class, 'logout']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()) {
        $request->user()->sendEmailVerificationNotification();
        session()->put('resent', true);
        return back()->with('message', 'Verification link sent!');
    }
    return back()->withErrors(['email' => 'ログインしてからメール認証してください']);
})->middleware('auth')->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance')->with('message', 'メール認証が完了しました');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break-start');
    Route::post('/attendance/end-break', [AttendanceController::class, 'breakEnd'])->name('attendance.break-end');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    Route::get('/attendance/list', [AttendanceController::class, 'showAttendance'])->name('attendance.list.default');
    Route::get('/attendance/list/{year?}/{month?}', [AttendanceController::class, 'showAttendance'])->name('attendance.list');
});

Route::middleware(['auth:admin,web', 'verified'])->group(function () {
    Route::get('/attendance/{id}', [CorrectionRequestController::class, 'showCorrectionRequestForm'])
        ->name('attendance.detail');
    Route::post('/attendance/{id}', [CorrectionRequestController::class, 'storeCorrectionRequest'])
        ->name('attendance.detail.store');

    Route::get('/stamp_correction_request/list', [CorrectionRequestController::class, 'showCorrectionRequest'])->name('requests.list');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/stamp_correction_request/approve/{id}', [AdminCorrectionRequestController::class, 'showRequestApprove'])
        ->name('request.approve');
    Route::post('/stamp_correction_request/approve/{id}', [AdminCorrectionRequestController::class, 'storeRequestApprove'])
        ->name('request.approve.store');
});
