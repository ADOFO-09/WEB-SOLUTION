<?php

use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\MinistryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BiometricController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| People Domain Routes
|--------------------------------------------------------------------------
|
| Members, Visitors, and Ministries management.
| Accessible to roles with members.view / visitors.view permissions.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // =========================================
    // MEMBER MANAGEMENT
    // =========================================
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

        // Create user account for an existing member
        Route::get('/{member}/create-user', [UserController::class, 'createForMember'])->name('create-user');
        Route::post('/{member}/create-user', [UserController::class, 'storeForMember'])->name('store-user');

        // Biometric enrollment
        Route::get('/{member}/biometric', [BiometricController::class, 'showEnrollment'])->name('biometric');
        Route::get('/{member}/biometric/enrolled-templates', [BiometricController::class, 'enrolledTemplates'])->name('biometric.enrolled-templates');
        Route::post('/{member}/biometric/enroll', [BiometricController::class, 'enroll'])->name('biometric.enroll');
        Route::delete('/{member}/biometric', [BiometricController::class, 'remove'])->name('biometric.remove');

        // All enrolled templates — used by the create-member page (no member scope yet)
        Route::get('/biometric/all-templates', [BiometricController::class, 'allEnrolledTemplates'])->name('biometric.all-templates');

        // Bridge installer download (admin only)
        Route::get('/biometric/download-bridge', [BiometricController::class, 'downloadBridge'])->name('biometric.download-bridge');

        // Authenticated photo download (private disk)
        Route::get('/{member}/photo', [MemberController::class, 'photo'])->name('photo');
    });

    // =========================================
    // VISITOR MANAGEMENT
    // =========================================
    Route::prefix('visitors')->name('visitors.')->group(function () {
        Route::get('/', [VisitorController::class, 'index'])->name('index');
        Route::get('/create', [VisitorController::class, 'create'])->name('create');
        Route::post('/', [VisitorController::class, 'store'])->name('store');
        Route::get('/{visitor}', [VisitorController::class, 'show'])->name('show');
        Route::get('/{visitor}/edit', [VisitorController::class, 'edit'])->name('edit');
        Route::put('/{visitor}', [VisitorController::class, 'update'])->name('update');
        Route::delete('/{visitor}', [VisitorController::class, 'destroy'])->name('destroy');
        Route::post('/{visitor}/follow-up', [VisitorController::class, 'addFollowUp'])->name('follow-up');
        Route::post('/{visitor}/record-visit', [VisitorController::class, 'recordVisit'])->name('record-visit');
        Route::get('/{visitor}/convert', [VisitorController::class, 'showConvertForm'])->name('convert.form');
        Route::post('/{visitor}/convert', [VisitorController::class, 'convert'])->name('convert');
    });

    // =========================================
    // MINISTRY MANAGEMENT
    // =========================================
    Route::prefix('ministries')->name('ministries.')->group(function () {
        Route::get('/', [MinistryController::class, 'index'])->name('index');
        Route::get('/create', [MinistryController::class, 'create'])->name('create');
        Route::post('/', [MinistryController::class, 'store'])->name('store');
        Route::get('/{ministry}', [MinistryController::class, 'show'])->name('show');
        Route::get('/{ministry}/edit', [MinistryController::class, 'edit'])->name('edit');
        Route::put('/{ministry}', [MinistryController::class, 'update'])->name('update');
        Route::delete('/{ministry}', [MinistryController::class, 'destroy'])->name('destroy');
        Route::get('/{ministry}/members', [MinistryController::class, 'members'])->name('members');
        Route::post('/{ministry}/members', [MinistryController::class, 'addMember'])->name('members.add');
        Route::delete('/{ministry}/members/{member}', [MinistryController::class, 'removeMember'])->name('members.remove');
    });
});
