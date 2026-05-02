<?php

use App\Http\Controllers\Admin\AssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/assets')->name('admin.assets.')->middleware(['auth', 'admin.access'])->group(function () {

    Route::get('/',               [AssetController::class, 'index'])->name('index');
    Route::get('/create',         [AssetController::class, 'create'])->name('create');
    Route::post('/',              [AssetController::class, 'store'])->name('store');
    Route::get('/{asset}',        [AssetController::class, 'show'])->name('show');
    Route::get('/{asset}/edit',   [AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}',        [AssetController::class, 'update'])->name('update');
    Route::delete('/{asset}',     [AssetController::class, 'destroy'])->name('destroy');

    Route::post('/{asset}/maintenance', [AssetController::class, 'storeMaintenance'])->name('maintenance.store');
});
