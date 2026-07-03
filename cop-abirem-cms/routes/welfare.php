<?php

use App\Http\Controllers\Admin\WelfareRateController;
use App\Http\Controllers\Admin\FuneralRateController;
use App\Http\Controllers\Admin\WelfareContributionController;
use App\Http\Controllers\Admin\FuneralContributionController;
use App\Http\Controllers\Admin\WelfareBenefitController;
use App\Http\Controllers\Admin\FuneralBenefitController;
use App\Http\Controllers\Admin\WelfareReportController;
use App\Http\Controllers\Admin\FuneralReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // WELFARE
    Route::prefix('welfare')->name('welfare.')->group(function () {
        Route::get('/rates', [WelfareRateController::class, 'index'])->name('rates.index');
        Route::post('/rates', [WelfareRateController::class, 'store'])->name('rates.store');

        Route::get('/contributions', [WelfareContributionController::class, 'index'])->name('contributions.index');
        Route::get('/contributions/create', [WelfareContributionController::class, 'create'])->name('contributions.create');
        Route::post('/contributions', [WelfareContributionController::class, 'store'])->name('contributions.store');
        Route::get('/contributions/{welfareContribution}', [WelfareContributionController::class, 'show'])->name('contributions.show');
        Route::get('/contributions/{welfareContribution}/edit', [WelfareContributionController::class, 'edit'])->name('contributions.edit');
        Route::put('/contributions/{welfareContribution}', [WelfareContributionController::class, 'update'])->name('contributions.update');
        Route::delete('/contributions/{welfareContribution}', [WelfareContributionController::class, 'destroy'])->name('contributions.destroy');

        Route::get('/benefits', [WelfareBenefitController::class, 'index'])->name('benefits.index');
        Route::get('/benefits/create', [WelfareBenefitController::class, 'create'])->name('benefits.create');
        Route::post('/benefits', [WelfareBenefitController::class, 'store'])->name('benefits.store');
        Route::get('/benefits/{welfareBenefit}', [WelfareBenefitController::class, 'show'])->name('benefits.show');
        Route::get('/benefits/{welfareBenefit}/edit', [WelfareBenefitController::class, 'edit'])->name('benefits.edit');
        Route::put('/benefits/{welfareBenefit}', [WelfareBenefitController::class, 'update'])->name('benefits.update');
        Route::delete('/benefits/{welfareBenefit}', [WelfareBenefitController::class, 'destroy'])->name('benefits.destroy');

        Route::get('/reports/balances', [WelfareReportController::class, 'balances'])->name('reports.balances');
        Route::get('/reports/spending', [WelfareReportController::class, 'spending'])->name('reports.spending');
        Route::get('/reports/fund', [WelfareReportController::class, 'fundSummary'])->name('reports.fund');
    });

    // FUNERAL
    Route::prefix('funeral')->name('funeral.')->group(function () {
        Route::get('/rates', [FuneralRateController::class, 'index'])->name('rates.index');
        Route::post('/rates', [FuneralRateController::class, 'store'])->name('rates.store');

        Route::get('/contributions', [FuneralContributionController::class, 'index'])->name('contributions.index');
        Route::get('/contributions/create', [FuneralContributionController::class, 'create'])->name('contributions.create');
        Route::post('/contributions', [FuneralContributionController::class, 'store'])->name('contributions.store');
        Route::get('/contributions/{funeralContribution}', [FuneralContributionController::class, 'show'])->name('contributions.show');
        Route::get('/contributions/{funeralContribution}/edit', [FuneralContributionController::class, 'edit'])->name('contributions.edit');
        Route::put('/contributions/{funeralContribution}', [FuneralContributionController::class, 'update'])->name('contributions.update');
        Route::delete('/contributions/{funeralContribution}', [FuneralContributionController::class, 'destroy'])->name('contributions.destroy');

        Route::get('/benefits', [FuneralBenefitController::class, 'index'])->name('benefits.index');
        Route::get('/benefits/create', [FuneralBenefitController::class, 'create'])->name('benefits.create');
        Route::post('/benefits', [FuneralBenefitController::class, 'store'])->name('benefits.store');
        Route::get('/benefits/{funeralBenefit}', [FuneralBenefitController::class, 'show'])->name('benefits.show');
        Route::get('/benefits/{funeralBenefit}/edit', [FuneralBenefitController::class, 'edit'])->name('benefits.edit');
        Route::put('/benefits/{funeralBenefit}', [FuneralBenefitController::class, 'update'])->name('benefits.update');
        Route::delete('/benefits/{funeralBenefit}', [FuneralBenefitController::class, 'destroy'])->name('benefits.destroy');

        Route::get('/reports/spending', [FuneralReportController::class, 'spending'])->name('reports.spending');
        Route::get('/reports/fund', [FuneralReportController::class, 'fundSummary'])->name('reports.fund');
    });
});
