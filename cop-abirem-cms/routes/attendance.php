<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BiometricAttendanceController;
use App\Http\Controllers\Admin\ServiceTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Attendance Domain Routes
|--------------------------------------------------------------------------
|
| Attendance session management and service type configuration.
| Accessible to roles with attendance.view permission.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // =========================================
    // ATTENDANCE SESSIONS
    // =========================================
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
        // Session QR code management
        Route::get('/{session}/qr-display', [AttendanceController::class, 'qrDisplay'])->name('qr-display');
        Route::get('/{session}/qr-scan-count', [AttendanceController::class, 'qrScanCount'])->name('qr-scan-count');
        Route::post('/{session}/regenerate-qr', [AttendanceController::class, 'regenerateQr'])->name('regenerate-qr');
        Route::post('/{session}/toggle-qr', [AttendanceController::class, 'toggleQr'])->name('toggle-qr');
        // Biometric attendance station
        Route::get('/{session}/biometric', [BiometricAttendanceController::class, 'showStation'])->name('biometric');
        Route::get('/{session}/biometric/members', [BiometricAttendanceController::class, 'getEnrolledMembers'])->name('biometric.members');
        Route::post('/biometric/verify', [BiometricAttendanceController::class, 'verify'])->name('biometric.verify');
    });

    // =========================================
    // SERVICE TYPES
    // =========================================
    Route::resource('service-types', ServiceTypeController::class);
});
