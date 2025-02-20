<?php

use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Auth\AdminLogoutController;
use App\Http\Controllers\Admin\UserController;
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

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'adminLogin']);
    Route::post('/logout', [AdminLogoutController::class, 'adminLogout']);
});

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/attendance/list/{year?}/{month?}/{day?}', [AdminAttendanceController::class, 'showAdminAttendance'])->name('admin.attendance.list');
    Route::get('/attendance/staff/{id}/{year?}/{month?}', [AdminAttendanceController::class, 'showUserAttendance'])->name('user.attendance.list');
    Route::get('/attendance/staff/{id}/{year?}/{month?}/export', [AdminAttendanceController::class, 'exportUserAttendance'])->name('user.attendance.export');
    Route::get('/staff/list', [UserController::class, 'showUserList'])->name('users.list');
});
