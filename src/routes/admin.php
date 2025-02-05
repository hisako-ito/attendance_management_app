<?php

use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
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

Route::get('/login', [AdminLoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/login', [AdminLoginController::class, 'adminLogin']);
Route::post('/logout', [AdminLoginController::class, 'adminLogout']);

Route::middleware(['auth:admins'])->group(function () {
    Route::get('/attendance/list', [AdminAttendanceController::class, 'show'])->name('admin.attendance.list');
});
