<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\Admin\TitheController;
use App\Http\Controllers\Admin\OfferingController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\PledgeController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ActivityLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // ===========================================
    // DASHBOARD
    // ===========================================
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/finance', [DashboardController::class, 'finance'])->name('dashboard.finance');
    Route::get('/dashboard/attendance', [DashboardController::class, 'attendance'])->name('dashboard.attendance');

    // ===========================================
    // PROFILE
    // ===========================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'password'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // ===========================================
    // REPORTS
    // ===========================================
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
        
        // Attendance Reports
        Route::get('/attendance', [ReportController::class, 'attendanceReport'])->name('attendance');
        Route::get('/visitors', [ReportController::class, 'visitorReport'])->name('visitors');
    });

    // ===========================================
    // USER MANAGEMENT
    // ===========================================
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');

    // ===========================================
    // MEMBER MANAGEMENT
    // ===========================================
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('/export', [MemberController::class, 'export'])->name('export');
        Route::get('/create', [MemberController::class, 'create'])->name('create');
        Route::post('/', [MemberController::class, 'store'])->name('store');
        Route::get('/{member}', [MemberController::class, 'show'])->name('show');
        Route::get('/{member}/edit', [MemberController::class, 'edit'])->name('edit');
        Route::put('/{member}', [MemberController::class, 'update'])->name('update');
        Route::delete('/{member}', [MemberController::class, 'destroy'])->name('destroy');
        Route::get('/{member}/card', [MemberController::class, 'printCard'])->name('card');
        Route::get('/{member}/qr', [MemberController::class, 'downloadQr'])->name('qr');
        Route::get('/{member}/qrcode', [MemberController::class, 'downloadQr'])->name('qrcode');
        Route::get('/{member}/family', [MemberController::class, 'family'])->name('family');
        Route::post('/{member}/family', [MemberController::class, 'storeFamily'])->name('family.store');
        Route::delete('/{member}/family/{relationship}', [MemberController::class, 'destroyFamily'])->name('family.destroy');
    });

    // ===========================================
    // VISITOR MANAGEMENT
    // ===========================================
    Route::prefix('visitors')->name('visitors.')->group(function () {
        Route::get('/', [VisitorController::class, 'index'])->name('index');
        Route::get('/create', [VisitorController::class, 'create'])->name('create');
        Route::post('/', [VisitorController::class, 'store'])->name('store');
        Route::get('/{visitor}', [VisitorController::class, 'show'])->name('show');
        Route::get('/{visitor}/edit', [VisitorController::class, 'edit'])->name('edit');
        Route::put('/{visitor}', [VisitorController::class, 'update'])->name('update');
        Route::delete('/{visitor}', [VisitorController::class, 'destroy'])->name('destroy');
        Route::post('/{visitor}/follow-up', [VisitorController::class, 'addFollowUp'])->name('follow-up');
        Route::get('/{visitor}/convert', [VisitorController::class, 'convertForm'])->name('convert.form');
        Route::post('/{visitor}/convert', [VisitorController::class, 'convert'])->name('convert');
    });

    // ===========================================
    // ATTENDANCE MANAGEMENT
    // ===========================================
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [AttendanceController::class, 'create'])->name('create');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
        Route::get('/{session}', [AttendanceController::class, 'show'])->name('show');
        Route::get('/{session}/mark', [AttendanceController::class, 'markAttendance'])->name('mark');
        Route::post('/{session}/mark', [AttendanceController::class, 'storeAttendance'])->name('store-attendance');
        Route::post('/{session}/mark-member', [AttendanceController::class, 'markMember'])->name('mark-member');
        Route::post('/{session}/mark-visitor', [AttendanceController::class, 'markVisitor'])->name('mark-visitor');
        Route::post('/{session}/unmark', [AttendanceController::class, 'unmark'])->name('unmark');
        Route::get('/{session}/scanner', [AttendanceController::class, 'scanner'])->name('scanner');
        Route::post('/{session}/scan', [AttendanceController::class, 'processScan'])->name('scan');
        Route::post('/{session}/close', [AttendanceController::class, 'close'])->name('close');
        Route::post('/{session}/reopen', [AttendanceController::class, 'reopen'])->name('reopen');
        Route::delete('/{session}', [AttendanceController::class, 'destroy'])->name('destroy');
    });

    Route::resource('service-types', ServiceTypeController::class);

    // ===========================================
    // FINANCIAL MANAGEMENT - TITHES
    // ===========================================
    Route::prefix('tithes')->name('tithes.')->group(function () {
        Route::get('/', [TitheController::class, 'index'])->name('index');
        Route::get('/create', [TitheController::class, 'create'])->name('create');
        Route::post('/', [TitheController::class, 'store'])->name('store');
        Route::get('/monthly-report', [TitheController::class, 'monthlyReport'])->name('monthly-report');
        Route::get('/member/{member}', [TitheController::class, 'memberHistory'])->name('member-history');
        Route::get('/{tithe}', [TitheController::class, 'show'])->name('show');
        Route::get('/{tithe}/edit', [TitheController::class, 'edit'])->name('edit');
        Route::put('/{tithe}', [TitheController::class, 'update'])->name('update');
        Route::delete('/{tithe}', [TitheController::class, 'destroy'])->name('destroy');
        Route::get('/{tithe}/receipt', [TitheController::class, 'printReceipt'])->name('receipt');
    });

    // ===========================================
    // FINANCIAL MANAGEMENT - OFFERINGS
    // ===========================================
    Route::prefix('offerings')->name('offerings.')->group(function () {
        Route::get('/', [OfferingController::class, 'index'])->name('index');
        Route::get('/create', [OfferingController::class, 'create'])->name('create');
        Route::post('/', [OfferingController::class, 'store'])->name('store');
        Route::get('/session/{session}', [OfferingController::class, 'sessionSummary'])->name('session-summary');
        Route::get('/{offering}', [OfferingController::class, 'show'])->name('show');
        Route::get('/{offering}/edit', [OfferingController::class, 'edit'])->name('edit');
        Route::put('/{offering}', [OfferingController::class, 'update'])->name('update');
        Route::delete('/{offering}', [OfferingController::class, 'destroy'])->name('destroy');
    });

    // ===========================================
    // FINANCIAL MANAGEMENT - DONATIONS
    // ===========================================
    Route::prefix('donations')->name('donations.')->group(function () {
        Route::get('/', [DonationController::class, 'index'])->name('index');
        Route::get('/create', [DonationController::class, 'create'])->name('create');
        Route::post('/', [DonationController::class, 'store'])->name('store');
        Route::get('/{donation}', [DonationController::class, 'show'])->name('show');
        Route::get('/{donation}/edit', [DonationController::class, 'edit'])->name('edit');
        Route::put('/{donation}', [DonationController::class, 'update'])->name('update');
        Route::delete('/{donation}', [DonationController::class, 'destroy'])->name('destroy');
        Route::get('/{donation}/receipt', [DonationController::class, 'printReceipt'])->name('receipt');
    });

    // ===========================================
    // FINANCIAL MANAGEMENT - PLEDGES
    // ===========================================
    Route::prefix('pledges')->name('pledges.')->group(function () {
        Route::get('/', [PledgeController::class, 'index'])->name('index');
        Route::get('/create', [PledgeController::class, 'create'])->name('create');
        Route::post('/', [PledgeController::class, 'store'])->name('store');
        Route::get('/overdue', [PledgeController::class, 'overdueReport'])->name('overdue');
        Route::get('/{pledge}', [PledgeController::class, 'show'])->name('show');
        Route::get('/{pledge}/edit', [PledgeController::class, 'edit'])->name('edit');
        Route::put('/{pledge}', [PledgeController::class, 'update'])->name('update');
        Route::delete('/{pledge}', [PledgeController::class, 'destroy'])->name('destroy');
        Route::post('/{pledge}/payment', [PledgeController::class, 'recordPayment'])->name('record-payment');
        Route::post('/{pledge}/cancel', [PledgeController::class, 'cancel'])->name('cancel');
    });

    // ===========================================
    // FINANCIAL MANAGEMENT - EXPENSES
    // ===========================================
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('/budget-report', [ExpenseController::class, 'budgetReport'])->name('budget-report');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::post('/{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
        Route::post('/{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
        Route::post('/{expense}/mark-paid', [ExpenseController::class, 'markPaid'])->name('mark-paid');
        Route::get('/{expense}/voucher', [ExpenseController::class, 'printVoucher'])->name('voucher');
    });

    // ===========================================
    // SMS COMMUNICATION
    // ===========================================
    Route::prefix('sms')->name('sms.')->group(function () {
        Route::get('/', [SmsController::class, 'index'])->name('index');
        Route::get('/compose', [SmsController::class, 'compose'])->name('compose');
        Route::post('/', [SmsController::class, 'store'])->name('store');
        Route::get('/templates/manage', [SmsController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [SmsController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [SmsController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{smsTemplate}/edit', [SmsController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{smsTemplate}', [SmsController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{smsTemplate}', [SmsController::class, 'destroyTemplate'])->name('templates.destroy');
        Route::get('/{smsMessage}', [SmsController::class, 'show'])->name('show');
        Route::post('/{smsMessage}/send', [SmsController::class, 'send'])->name('send');
        Route::delete('/{smsMessage}', [SmsController::class, 'destroy'])->name('destroy');
    });

    // ===========================================
// USER MANAGEMENT (with Member Linking)
// ===========================================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        
        // Member linking
        Route::get('/{user}/link-member', [UserController::class, 'linkMemberForm'])->name('link-member.form');
        Route::post('/{user}/link-member', [UserController::class, 'linkMember'])->name('link-member');
        Route::post('/{user}/unlink-member', [UserController::class, 'unlinkMember'])->name('unlink-member');
        
        // User actions
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    });

    // Create user for member (add to members routes section)
    Route::get('/members/{member}/create-user', [UserController::class, 'createForMember'])->name('members.create-user');
    Route::post('/members/{member}/create-user', [UserController::class, 'storeForMember'])->name('members.store-user');



});
