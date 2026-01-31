<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MinistryController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\Admin\TitheController;
use App\Http\Controllers\Admin\OfferingController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\PledgeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and are protected
| by the 'auth' and 'admin.access' middleware.
|
*/

Route::middleware(['auth', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Forced Password Change (accessible even with must_change_password flag)
    Route::get('/password/change', [ProfileController::class, 'showForceChangePasswordForm'])
        ->name('password.change')
        ->withoutMiddleware(['admin.access']);
    Route::put('/password/change', [ProfileController::class, 'updatePassword'])
        ->name('password.change.update')
        ->withoutMiddleware(['admin.access']);

    // User Management (requires users.* permissions)
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    Route::middleware(['permission:users.create'])->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware(['permission:users.edit'])->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/unlock', [UserController::class, 'unlock'])->name('users.unlock');
    });

    Route::middleware(['permission:users.delete'])->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    });

    // Role Management (requires roles.* permissions)
    Route::middleware(['permission:roles.view'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });

    Route::middleware(['permission:roles.create'])->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware(['permission:roles.edit'])->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::middleware(['permission:roles.delete'])->group(function () {
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Permissions (view only - permissions are system-defined)
    Route::middleware(['permission:roles.view'])->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    });

    // Activity Logs (requires settings.logs permission)
    Route::middleware(['permission:settings.logs'])->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

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
        Route::get('/{member}/qrcode', [MemberController::class, 'showQrCode'])->name('qrcode');
        Route::get('/{member}/card', [MemberController::class, 'printCard'])->name('card');
        Route::match(['get', 'post', 'delete'], '/{member}/family', [MemberController::class, 'familyRelationships'])->name('family');
    });

    // ===========================================
    // MINISTRY MANAGEMENT
    // ===========================================
    Route::prefix('ministries')->name('ministries.')->group(function () {
        Route::get('/', [MinistryController::class, 'index'])->name('index');
        Route::get('/create', [MinistryController::class, 'create'])->name('create');
        Route::post('/', [MinistryController::class, 'store'])->name('store');
        Route::get('/{ministry}', [MinistryController::class, 'show'])->name('show');
        Route::get('/{ministry}/edit', [MinistryController::class, 'edit'])->name('edit');
        Route::put('/{ministry}', [MinistryController::class, 'update'])->name('update');
        Route::delete('/{ministry}', [MinistryController::class, 'destroy'])->name('destroy');
        Route::match(['get', 'post', 'delete'], '/{ministry}/members', [MinistryController::class, 'members'])->name('members');
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
        Route::post('/{visitor}/followup', [VisitorController::class, 'addFollowUp'])->name('followup');
        Route::post('/{visitor}/record-visit', [VisitorController::class, 'recordVisit'])->name('record-visit');
        Route::get('/{visitor}/convert', [VisitorController::class, 'showConvertForm'])->name('convert.form');
        Route::post('/{visitor}/convert', [VisitorController::class, 'convert'])->name('convert');
    });

    // ===========================================
    // ATTENDANCE MANAGEMENT
    // ===========================================
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [AttendanceController::class, 'create'])->name('create');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
        Route::get('/{attendance}', [AttendanceController::class, 'show'])->name('show');
        Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/{attendance}/mark', [AttendanceController::class, 'mark'])->name('mark');
        Route::post('/{attendance}/mark-member', [AttendanceController::class, 'markMember'])->name('mark-member');
        Route::post('/{attendance}/mark-visitor', [AttendanceController::class, 'markVisitor'])->name('mark-visitor');
        Route::post('/{attendance}/unmark', [AttendanceController::class, 'unmark'])->name('unmark');
        Route::post('/{attendance}/close', [AttendanceController::class, 'close'])->name('close');
        Route::post('/{attendance}/reopen', [AttendanceController::class, 'reopen'])->name('reopen');
        Route::get('/{attendance}/scanner', [AttendanceController::class, 'scanner'])->name('scanner');
        Route::post('/{attendance}/process-scan', [AttendanceController::class, 'processScan'])->name('process-scan');
    });

    // ===========================================
    // SERVICE TYPE MANAGEMENT
    // ===========================================
    Route::prefix('service-types')->name('service-types.')->group(function () {
        Route::get('/', [ServiceTypeController::class, 'index'])->name('index');
        Route::get('/create', [ServiceTypeController::class, 'create'])->name('create');
        Route::post('/', [ServiceTypeController::class, 'store'])->name('store');
        Route::get('/{serviceType}', [ServiceTypeController::class, 'show'])->name('show');
        Route::get('/{serviceType}/edit', [ServiceTypeController::class, 'edit'])->name('edit');
        Route::put('/{serviceType}', [ServiceTypeController::class, 'update'])->name('update');
        Route::delete('/{serviceType}', [ServiceTypeController::class, 'destroy'])->name('destroy');
    });

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
});
