<?php

use App\Http\Controllers\Member\PortalController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\GivingController;
use App\Http\Controllers\Member\PledgeController;
use App\Http\Controllers\Member\AttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Member Portal Routes
|--------------------------------------------------------------------------
|
| Routes for the member self-service portal. Members can view their
| profile, giving history, pledges, and attendance records.
|
*/

Route::middleware(['auth', 'member.access'])->prefix('member')->name('member.')->group(function () {
    
    // Dashboard
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'password'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo.update');
    });
    
    // Giving History
    Route::prefix('giving')->name('giving.')->group(function () {
        Route::get('/', [GivingController::class, 'index'])->name('index');
        Route::get('/tithes', [GivingController::class, 'tithes'])->name('tithes');
        Route::get('/offerings', [GivingController::class, 'offerings'])->name('offerings');
        Route::get('/donations', [GivingController::class, 'donations'])->name('donations');
        Route::get('/statement', [GivingController::class, 'statement'])->name('statement');
        Route::get('/statement/download', [GivingController::class, 'downloadStatement'])->name('statement.download');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/contributions', [GivingController::class, 'contributionsReport'])->name('contributions');
    });
    
    // Pledges
    Route::prefix('pledges')->name('pledges.')->group(function () {
        Route::get('/', [PledgeController::class, 'index'])->name('index');
        Route::get('/{pledge}', [PledgeController::class, 'show'])->name('show');
    });
    
    // Attendance History & QR Scanning
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/scan', [AttendanceController::class, 'showScanner'])->name('scan');
        Route::get('/verify/{token}', [AttendanceController::class, 'verifyQr'])->name('verify');
        Route::post('/record', [AttendanceController::class, 'recordAttendance'])->name('record');
    });
});
