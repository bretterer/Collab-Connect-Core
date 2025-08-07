<?php

use App\Livewire\Auth;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Spatie\Honeypot\ProtectAgainstSpam;

if (app()->isLocal()) {
    Route::middleware('guest')->group(function () {
        Route::get('login', Auth\Login::class)->name('login')->middleware(ProtectAgainstSpam::class);
        Route::get('register', Auth\Register::class)->name('register');
        Route::get('forgot-password', Auth\ForgotPassword::class)->name('password.request');
        Route::get('reset-password/{token}', Auth\ResetPassword::class)->name('password.reset');
    });

    Route::post('logout', App\Livewire\Actions\Logout::class)
        ->name('logout');

    Route::get('clearSession', function () {
        AuthFacade::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    });
}
