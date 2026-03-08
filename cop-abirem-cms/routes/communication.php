<?php

use App\Http\Controllers\Admin\SmsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Communication Domain Routes
|--------------------------------------------------------------------------
|
| SMS messaging and template management.
| Accessible to roles with sms.view permission.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // =========================================
    // SMS MESSAGES & TEMPLATES
    // =========================================
    Route::prefix('sms')->name('sms.')->group(function () {
        Route::get('/', [SmsController::class, 'index'])->name('index');
        Route::get('/compose', [SmsController::class, 'compose'])->name('compose');
        Route::post('/', [SmsController::class, 'store'])->name('store');

        // Templates
        Route::get('/templates/manage', [SmsController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [SmsController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [SmsController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{smsTemplate}/edit', [SmsController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{smsTemplate}', [SmsController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{smsTemplate}', [SmsController::class, 'destroyTemplate'])->name('templates.destroy');

        // Individual messages
        Route::get('/{smsMessage}', [SmsController::class, 'show'])->name('show');
        Route::post('/{smsMessage}/send', [SmsController::class, 'send'])->name('send');
        Route::delete('/{smsMessage}', [SmsController::class, 'destroy'])->name('destroy');
    });
});
