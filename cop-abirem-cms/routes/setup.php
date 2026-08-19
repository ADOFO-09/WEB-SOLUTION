<?php

use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| First-Run Setup Wizard
|--------------------------------------------------------------------------
| These routes are accessible before the system is fully configured.
| They sit behind 'auth' but intentionally bypass 'admin.access' to
| avoid the setup-redirect loop.
*/

Route::middleware(['auth'])->prefix('setup')->name('setup.')->group(function () {
    Route::get('/',          [SetupController::class, 'index']   )->name('index');
    Route::get('/account',   [SetupController::class, 'account'] )->name('account');
    Route::post('/account',  [SetupController::class, 'saveAccount'])->name('account.save');
    Route::get('/church',    [SetupController::class, 'church']  )->name('church');
    Route::post('/church',   [SetupController::class, 'saveChurch'])->name('church.save');
    Route::get('/financial', [SetupController::class, 'financial'])->name('financial');
    Route::post('/financial',[SetupController::class, 'saveFinancial'])->name('financial.save');
    Route::get('/sms',       [SetupController::class, 'sms']     )->name('sms');
    Route::post('/sms',      [SetupController::class, 'saveSms'] )->name('sms.save');
    Route::get('/complete',  [SetupController::class, 'complete'])->name('complete');
});
