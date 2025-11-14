<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::middleware('guest')->group(function () {
    Route::get('login', Auth\Login::class)->name('login')->middleware(ProtectAgainstSpam::class);
    Route::get('forgot-password', Auth\ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', Auth\ResetPassword::class)->name('password.reset');
});

Route::middleware('auth', App\Http\Middleware\EnsureMarketApproved::class)->group(function () {
    Route::get('verify-email', App\Livewire\Auth\VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

if (config('collabconnect.registration_enabled')) {
    if (config('collabconnect.beta_registration_only')) {
        Route::get('register', Auth\Register::class)->name('register')->middleware('signed');
    } else {
        Route::get('register', Auth\Register::class)->name('register');
    }
}

Route::get('accept-business-invite', Auth\AcceptInvite::class)->name('accept-business-invite')->middleware('signed');

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');

Route::get('clearSession', function () {
    AuthFacade::guard('web')->logout();

    Session::invalidate();
    Session::regenerateToken();

    return redirect('/');
});
