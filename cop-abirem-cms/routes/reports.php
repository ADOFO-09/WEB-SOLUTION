<?php

use App\Http\Controllers\Admin\ExpenseLedgerController;
use App\Http\Controllers\Admin\IncomeLedgerController;
use App\Http\Controllers\Admin\MonthlyReportController;
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

        // Ledger Views
        Route::get('/income-ledger', [IncomeLedgerController::class, 'index'])->name('income-ledger');
        Route::get('/income-ledger/export', [IncomeLedgerController::class, 'export'])->name('income-ledger.export');
        Route::get('/expense-ledger', [ExpenseLedgerController::class, 'index'])->name('expense-ledger');
        Route::get('/expense-ledger/export', [ExpenseLedgerController::class, 'export'])->name('expense-ledger.export');

        // Monthly Reports
        Route::resource('monthly-report', MonthlyReportController::class)
            ->parameters(['monthly-report' => 'monthlyReport']);
        Route::get('/monthly-report/{monthlyReport}/print', [MonthlyReportController::class, 'print'])
            ->name('monthly-report.print');
        Route::get('/monthly-report/{monthlyReport}/pdf', [MonthlyReportController::class, 'pdf'])
            ->name('monthly-report.pdf');
    });
});
