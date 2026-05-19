<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaffPositionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| System Domain Routes
|--------------------------------------------------------------------------
|
| User management, roles, permissions, settings, and activity logs.
| Settings sub-group is restricted further by 'settings.manage' permission.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // =========================================
    // USER MANAGEMENT
    // =========================================
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

    // =========================================
    // STAFF POSITIONS
    // =========================================
    Route::prefix('staff-positions')->name('staff-positions.')->group(function () {
        Route::get('/',                              [StaffPositionController::class, 'index'])->name('index');
        Route::get('/{role}/assign',                 [StaffPositionController::class, 'assignForm'])->name('assign');
        Route::post('/{role}/assign',                [StaffPositionController::class, 'assign'])->name('assign.submit');
    });

    // =========================================
    // ROLE & PERMISSION MANAGEMENT
    // =========================================
    Route::resource('roles', RoleController::class);

    // Dedicated permission assignment page for a role
    Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])
        ->name('roles.permissions');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
        ->name('roles.permissions.update');

    // =========================================
    // ACTIVITY LOGS
    // =========================================
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
    });

    // =========================================
    // SETTINGS (requires settings.manage permission)
    // =========================================
    Route::prefix('settings')->name('settings.')->middleware('check.permission:settings.manage')->group(function () {
        // General Settings
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');

        // Financial Settings
        Route::get('/financial', [SettingsController::class, 'financial'])->name('financial');
        Route::put('/financial', [SettingsController::class, 'updateFinancial'])->name('financial.update');

        // SMS Settings
        Route::get('/sms', [SettingsController::class, 'sms'])->name('sms');
        Route::put('/sms', [SettingsController::class, 'updateSms'])->name('sms.update');
        Route::post('/sms/test', [SettingsController::class, 'testSms'])->name('sms.test');
        Route::post('/sms/balance', [SettingsController::class, 'checkSmsBalance'])->name('sms.balance');
        Route::post('/sms/birthday/run-now', [SettingsController::class, 'runBirthdaySmsNow'])->name('sms.birthday.run-now');

        // System Settings
        Route::get('/system', [SettingsController::class, 'system'])->name('system');
        Route::put('/system', [SettingsController::class, 'updateSystem'])->name('system.update');

        // Backup Management
        Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [SettingsController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/download/{filename}', [SettingsController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{filename}', [SettingsController::class, 'deleteBackup'])->name('backup.delete');
        Route::post('/backup/restore/{filename}', [SettingsController::class, 'restoreBackup'])->name('backup.restore');

        // Cache & Optimization
        Route::post('/cache/clear', [SettingsController::class, 'clearCache'])->name('cache.clear');
        Route::post('/optimize', [SettingsController::class, 'optimize'])->name('optimize');
    });
});
