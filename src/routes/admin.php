<?php

use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminStampCorrectionRequestController;

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

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'adminLogin']);
    Route::post('/logout', [AdminLoginController::class, 'adminLogout']);
});

Route::prefix('admin')->middleware(['auth:admins'])->group(function () {
    Route::get('/attendance/list/{year?}/{month?}/{day?}', [AdminAttendanceController::class, 'adminAttendanceShow'])->name('admin.attendance.list');
    Route::get('/attendance/staff/{id}/{year?}/{month?}', [AdminAttendanceController::class, 'userAttendanceShow'])->name('user.attendance.list');
    Route::get('/staff/list', [UserController::class, 'userListShow']);
});
