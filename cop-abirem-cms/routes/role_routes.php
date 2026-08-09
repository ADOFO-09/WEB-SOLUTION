<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Roles\ElderDashboardController;
use App\Http\Controllers\Admin\Roles\FinanceDashboardController;
use App\Http\Controllers\Admin\Roles\MinistryDashboardController;
use App\Http\Controllers\Admin\Roles\MinistryFinanceController;
use App\Http\Controllers\Admin\Roles\SecretaryDashboardController;
use App\Http\Controllers\Admin\StaffHomeController;

/*
|--------------------------------------------------------------------------
| Role-Based Dashboard Routes
|--------------------------------------------------------------------------
| These routes provide dedicated dashboards for each user role.
| Middleware 'auth' is applied at the route group level.
*/

Route::middleware(['auth', 'two_fa', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {

    // ==========================================
    // STAFF HOME (generic permission-aware dashboard)
    // ==========================================
    Route::get('/home', [StaffHomeController::class, 'index'])->name('home');

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
    // LOCAL SECRETARY ROUTES
    // ==========================================
    Route::prefix('secretary')->name('secretary.')->group(function () {
        Route::get('/dashboard', [SecretaryDashboardController::class, 'index'])->name('dashboard');
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

        // Finance
        Route::get('/finance',                       [MinistryFinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/offerings',             [MinistryFinanceController::class, 'offerings'])->name('finance.offerings');
        Route::post('/finance/offerings',            [MinistryFinanceController::class, 'storeOffering'])->name('finance.offerings.store');
        Route::delete('/finance/offerings/{id}',     [MinistryFinanceController::class, 'deleteOffering'])->name('finance.offerings.delete');
        Route::get('/finance/expenses',              [MinistryFinanceController::class, 'expenses'])->name('finance.expenses');
        Route::post('/finance/expenses',             [MinistryFinanceController::class, 'storeExpense'])->name('finance.expenses.store');
        Route::delete('/finance/expenses/{id}',      [MinistryFinanceController::class, 'deleteExpense'])->name('finance.expenses.delete');
        Route::get('/finance/report',                [MinistryFinanceController::class, 'report'])->name('finance.report');

        // Welfare Contributions
        Route::get('/finance/welfare/contributions',        [MinistryFinanceController::class, 'welfareContributions'])->name('finance.welfare.contributions');
        Route::post('/finance/welfare/contributions',       [MinistryFinanceController::class, 'storeWelfareContribution'])->name('finance.welfare.contributions.store');
        Route::delete('/finance/welfare/contributions/{id}',[MinistryFinanceController::class, 'deleteWelfareContribution'])->name('finance.welfare.contributions.delete');

        // Welfare Benefits
        Route::get('/finance/welfare/benefits',             [MinistryFinanceController::class, 'welfareBenefits'])->name('finance.welfare.benefits');
        Route::get('/finance/welfare/benefits/create',      [MinistryFinanceController::class, 'createWelfareBenefit'])->name('finance.welfare.benefits.create');
        Route::post('/finance/welfare/benefits',            [MinistryFinanceController::class, 'storeWelfareBenefit'])->name('finance.welfare.benefits.store');
        Route::get('/finance/welfare/benefits/{id}',        [MinistryFinanceController::class, 'showWelfareBenefit'])->name('finance.welfare.benefits.show');
        Route::delete('/finance/welfare/benefits/{id}',     [MinistryFinanceController::class, 'deleteWelfareBenefit'])->name('finance.welfare.benefits.delete');

        // Welfare Due Rates
        Route::get('/finance/welfare/due-rates',            [MinistryFinanceController::class, 'welfareDueRates'])->name('finance.welfare.due-rates');
        Route::post('/finance/welfare/due-rates',           [MinistryFinanceController::class, 'storeWelfareDueRate'])->name('finance.welfare.due-rates.store');

        // Welfare Balances
        Route::get('/finance/welfare/balances',             [MinistryFinanceController::class, 'welfareBalances'])->name('finance.welfare.balances');

        // Welfare Fund Summary
        Route::get('/finance/welfare/fund-summary',         [MinistryFinanceController::class, 'welfareFundSummary'])->name('finance.welfare.fund-summary');

        // Welfare Member Records
        Route::get('/finance/welfare/member-records',       [MinistryFinanceController::class, 'welfareMembers'])->name('finance.welfare.members');
        Route::get('/finance/welfare/member-records/{memberId}', [MinistryFinanceController::class, 'welfareMemberShow'])->name('finance.welfare.members.show');
    });
});