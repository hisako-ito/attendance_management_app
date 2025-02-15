<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Admin\AdminStampCorrectionRequestController;
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
Route::post('/logout', [LoginController::class, 'logout']);

Route::middleware(['auth:web'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break-start');
    Route::post('/attendance/end-break', [AttendanceController::class, 'breakEnd'])->name('attendance.break-end');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    Route::get('/attendance/list', [AttendanceController::class, 'attendanceShow'])->name('attendance.list.default');
    Route::get('/attendance/list/{year?}/{month?}', [AttendanceController::class, 'attendanceShow'])->name('attendance.list');
});

Route::middleware(['auth:admin,web'])->group(function () {
    Route::get('/attendance/{id}', [StampCorrectionRequestController::class, 'stampCorrectionRequestCreate'])
        ->name('attendance.detail');
    Route::post('/attendance/{id}', [StampCorrectionRequestController::class, 'stampCorrectionRequestStore'])
        ->name('attendance.detail.store');

    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'stampCorrectionRequestShow'])->name('requests.list');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/stamp_correction_request/approve/{id}', [AdminStampCorrectionRequestController::class, 'showRequestApprove'])
        ->name('request.approve');
    Route::post('/stamp_correction_request/approve/{id}', [AdminStampCorrectionRequestController::class, 'storeRequestApprove'])
        ->name('request.approve.store');
});
