<?php

use App\Http\Controllers\Admin\TitheController;
use App\Http\Controllers\Admin\OfferingController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\PledgeController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\FinancialParticularController;
use App\Http\Controllers\Admin\LedgerCorrectionController;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Finance Domain Routes
|--------------------------------------------------------------------------
|
| Tithes, Offerings, Donations, Pledges, and Expenses management.
| Accessible to roles with finance.view / tithes.view / expenses.view etc.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {

    // =========================================
    // TITHES
    // =========================================
    Route::prefix('tithes')->name('tithes.')->group(function () {
        Route::get('/', [TitheController::class, 'index'])->name('index');
        Route::get('/create', [TitheController::class, 'create'])->name('create');
        Route::post('/', [TitheController::class, 'store'])->name('store');
        Route::get('/session/create', [TitheController::class, 'createForSession'])->name('session.create');
        Route::post('/session', [TitheController::class, 'storeForSession'])->name('session.store');
        Route::get('/monthly-report', [TitheController::class, 'monthlyReport'])->name('monthly-report');
        Route::get('/member/{member}', [TitheController::class, 'memberHistory'])->name('member-history');
        Route::get('/{tithe}', [TitheController::class, 'show'])->name('show');
        Route::get('/{tithe}/edit', [TitheController::class, 'edit'])->name('edit');
        Route::put('/{tithe}', [TitheController::class, 'update'])->name('update');
        Route::delete('/{tithe}', [TitheController::class, 'destroy'])->name('destroy');
        Route::get('/{tithe}/receipt', [TitheController::class, 'printReceipt'])->name('receipt');
    });

    // =========================================
    // OFFERINGS
    // =========================================
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

    // =========================================
    // DONATIONS
    // =========================================
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

    // =========================================
    // PLEDGES
    // =========================================
    Route::prefix('pledges')->name('pledges.')->group(function () {
        Route::get('/', [PledgeController::class, 'index'])->name('index');
        Route::get('/create', [PledgeController::class, 'create'])->name('create');
        Route::post('/', [PledgeController::class, 'store'])->name('store');
        Route::get('/overdue', [PledgeController::class, 'overdueReport'])->name('overdue');
        Route::get('/{pledge}', [PledgeController::class, 'show'])->name('show');
        Route::get('/{pledge}/edit', [PledgeController::class, 'edit'])->name('edit');
        Route::put('/{pledge}', [PledgeController::class, 'update'])->name('update');
        Route::delete('/{pledge}', [PledgeController::class, 'destroy'])->name('destroy');
        Route::get('/{pledge}/payment', fn($pledge) => redirect()->route('admin.pledges.show', $pledge))->name('record-payment.get');
        Route::post('/{pledge}/payment', [PledgeController::class, 'recordPayment'])->name('record-payment');
        Route::post('/{pledge}/cancel', [PledgeController::class, 'cancel'])->name('cancel');
    });

    // =========================================
    // EXPENSES
    // =========================================
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

    // =========================================
    // FINANCIAL PARTICULARS MANAGEMENT
    // =========================================
    Route::prefix('finance/particulars')->name('finance.particulars.')->group(function () {
        Route::get('/', [FinancialParticularController::class, 'index'])->name('index');
        Route::post('/income', [FinancialParticularController::class, 'storeIncome'])->name('store.income');
        Route::patch('/income/{category}', [FinancialParticularController::class, 'toggleIncome'])->name('toggle.income');
        Route::delete('/income/{category}', [FinancialParticularController::class, 'destroyIncome'])->name('destroy.income');
        Route::post('/expense', [FinancialParticularController::class, 'storeExpense'])->name('store.expense');
        Route::delete('/expense/{category}', [FinancialParticularController::class, 'destroyExpense'])->name('destroy.expense');
        Route::post('/ajax', [FinancialParticularController::class, 'storeAjax'])->name('store.ajax');
    });

    // =========================================
    // LEDGER CORRECTIONS & AUDIT
    // =========================================
    Route::prefix('finance/corrections')->name('finance.corrections.')->group(function () {
        Route::get('/',                                       [LedgerCorrectionController::class, 'index'])->name('index');
        Route::post('/tithe/{tithe}/void',                   [LedgerCorrectionController::class, 'voidTithe'])->name('void.tithe');
        Route::post('/offering/{offering}/void',             [LedgerCorrectionController::class, 'voidOffering'])->name('void.offering');
        Route::post('/donation/{donation}/void',             [LedgerCorrectionController::class, 'voidDonation'])->name('void.donation');
        Route::post('/expense/{expense}/void',               [LedgerCorrectionController::class, 'voidExpense'])->name('void.expense');
        Route::post('/{type}/{id}/restore',                  [LedgerCorrectionController::class, 'restore'])->name('restore');
        Route::post('/{type}/{id}/adjust',                   [LedgerCorrectionController::class, 'createAdjustment'])->name('adjust');
        Route::get('/{type}/{id}/history',                   [LedgerCorrectionController::class, 'auditHistory'])->name('history');
    });
});
