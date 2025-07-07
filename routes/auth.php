<?php

use App\Livewire\Auth;

use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::middleware('guest')->group(function() {
    Route::get('login', Auth\Login::class)->name('login')->middleware(ProtectAgainstSpam::class);
    Route::get('register', Auth\Register::class)->name('register');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');





if(app()->isLocal()) {
    Route::get('clearSession', function() {
        AuthFacade::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    });
}