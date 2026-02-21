<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // If user is admin, redirect to admin dashboard
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    
    // If user has a linked member profile, redirect to member portal
    if ($user->member_id) {
        return redirect()->route('member.dashboard');
    }
    
    // For users without member profile, show default dashboard
    return view('dashboard');
    
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/member.php';
require __DIR__.'/auth.php';