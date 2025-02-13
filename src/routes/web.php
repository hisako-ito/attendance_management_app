<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break-start');
    Route::post('/attendance/end-break', [AttendanceController::class, 'breakEnd'])->name('attendance.break-end');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    Route::get('/attendance/list', [AttendanceController::class, 'attendanceShow'])->name('attendance.list.default');
    Route::get('/attendance/list/{year?}/{month?}', [AttendanceController::class, 'attendanceShow'])->name('attendance.list');

    Route::get('/attendance/{id}', [\App\Http\Controllers\StampCorrectionRequestController::class, 'stampCorrectionRequestCreate'])
        ->name('attendance.detail');
    Route::post('/attendance/{id}', [\App\Http\Controllers\StampCorrectionRequestController::class, 'stampCorrectionRequestStore'])
        ->name('attendance.detail.store');

    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'stampCorrectionRequestShow'])->name('requests.list');
});
