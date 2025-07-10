<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

Route::middleware(['auth'])->group(function () {
    // Onboarding routes (accessible before onboarding is completed)
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/account-type', App\Livewire\Onboarding\AccountTypeSelection::class)->name('account-type');
        Route::get('/business', App\Livewire\Onboarding\BusinessOnboarding::class)->name('business');
        Route::get('/influencer', App\Livewire\Onboarding\InfluencerOnboarding::class)->name('influencer');
    });

    // Main dashboard (protected by onboarding middleware)
    Route::middleware('ensure.onboarding.completed')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });
});

require __DIR__.'/auth.php';
