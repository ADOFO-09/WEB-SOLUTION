<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Core Routes  —  Dashboard & Profile
|--------------------------------------------------------------------------
|
| All domain-specific routes live in their own files loaded via
| RouteServiceProvider:
|   routes/people.php        — Members, Visitors, Ministries
|   routes/attendance.php    — Attendance sessions, Service types
|   routes/finance.php       — Tithes, Offerings, Donations, Pledges, Expenses
|   routes/communication.php — SMS messages & Templates
|   routes/reports.php       — All reports
|   routes/system.php        — Users, Roles, Settings, Activity logs
|   routes/role_routes.php   — Role-specific dashboards (Elder, Finance, Ministry)
|   routes/member.php        — Member portal
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // =========================================
    // DASHBOARD
    // =========================================
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/finance', [DashboardController::class, 'finance'])->name('dashboard.finance');
    Route::get('/dashboard/attendance', [DashboardController::class, 'attendance'])->name('dashboard.attendance');

    // =========================================
    // ADMIN PROFILE
    // =========================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'password'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // =========================================
    // FORCED FIRST-LOGIN PASSWORD CHANGE
    // (AdminAccess middleware carves this out so the redirect doesn't loop)
    // =========================================
    Route::get('/password/change', [ProfileController::class, 'forceChangePassword'])->name('password.change');
    Route::post('/password/change', [ProfileController::class, 'storeForceChangePassword'])->name('password.change.submit');
});
