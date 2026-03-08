<?php

use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reports Domain Routes
|--------------------------------------------------------------------------
|
| Financial, membership, attendance, and visitor reports.
| Accessible to roles with reports.view permission.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // =========================================
    // ALL REPORTS
    // =========================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');

        // Financial Reports
        Route::get('/income-statement', [ReportController::class, 'incomeStatement'])->name('income-statement');
        Route::get('/tithes', [ReportController::class, 'titheReport'])->name('tithes');
        Route::get('/offerings', [ReportController::class, 'offeringReport'])->name('offerings');
        Route::get('/expenses', [ReportController::class, 'expenseReport'])->name('expenses');
        Route::get('/pledges', [ReportController::class, 'pledgeReport'])->name('pledges');

        // Membership Reports
        Route::get('/membership', [ReportController::class, 'membershipReport'])->name('membership');
        Route::get('/member-directory', [ReportController::class, 'memberDirectory'])->name('member-directory');
        Route::get('/ministries', [ReportController::class, 'ministryReport'])->name('ministries');
        Route::get('/birthdays', [ReportController::class, 'birthdayReport'])->name('birthdays');

        // Attendance & Visitor Reports
        Route::get('/attendance', [ReportController::class, 'attendanceReport'])->name('attendance');
        Route::get('/visitors', [ReportController::class, 'visitorReport'])->name('visitors');
    });
});
