<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Roles\ElderDashboardController;
use App\Http\Controllers\Admin\Roles\FinanceDashboardController;
use App\Http\Controllers\Admin\Roles\MinistryDashboardController;

/*
|--------------------------------------------------------------------------
| Role-Based Dashboard Routes
|--------------------------------------------------------------------------
| These routes provide dedicated dashboards for each user role.
| Middleware 'auth' is applied at the route group level.
*/

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // ==========================================
    // PRESIDING ELDER ROUTES
    // ==========================================
    Route::prefix('elder')->name('elder.')->group(function () {
        Route::get('/dashboard', [ElderDashboardController::class, 'index'])->name('dashboard');
        Route::post('/expense/{id}/approve', [ElderDashboardController::class, 'approveExpense'])->name('expense.approve');
        Route::post('/expense/{id}/reject', [ElderDashboardController::class, 'rejectExpense'])->name('expense.reject');
    });

    // ==========================================
    // FINANCIAL SECRETARY ROUTES
    // ==========================================
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', [FinanceDashboardController::class, 'index'])->name('dashboard');
        Route::post('/quick-tithe', [FinanceDashboardController::class, 'quickTithe'])->name('quick-tithe');
        Route::post('/quick-offering', [FinanceDashboardController::class, 'quickOffering'])->name('quick-offering');
        Route::post('/quick-expense', [FinanceDashboardController::class, 'quickExpense'])->name('quick-expense');
    });

    // ==========================================
    // MINISTRY LEADER ROUTES
    // ==========================================
    Route::prefix('ministry')->name('ministry.')->group(function () {
        Route::get('/dashboard', [MinistryDashboardController::class, 'index'])->name('dashboard');
        Route::get('/members', [MinistryDashboardController::class, 'members'])->name('members');
        Route::get('/attendance', [MinistryDashboardController::class, 'attendance'])->name('attendance');
        Route::post('/attendance', [MinistryDashboardController::class, 'saveAttendance'])->name('save-attendance');
        Route::get('/sms', [MinistryDashboardController::class, 'composeSms'])->name('compose-sms');
        Route::post('/sms', [MinistryDashboardController::class, 'sendSms'])->name('send-sms');
    });
});