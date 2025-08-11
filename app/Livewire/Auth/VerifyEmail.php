<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.auth')]
class VerifyEmail extends Component
{
    public function mount(): void
    {
        // Redirect if the user is already verified
        if (Auth::user()->hasVerifiedEmail()) {
            session()->flash('toast', [
                'message' => 'Your email address is already verified.',
                'type' => 'info',
            ]);
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }

    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            session()->flash('toast', [
                'message' => 'Your email address is already verified.',
                'type' => 'info',
            ]);
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        $cacheKey = 'email_verification_sent_'.Auth::id();

        if (Cache::has($cacheKey)) {
            $remainingTime = Cache::get($cacheKey) - now()->timestamp;
            Toaster::warning("Please wait {$remainingTime} seconds before requesting another verification email.");

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        // Cache for 60 seconds with expiration timestamp
        Cache::put($cacheKey, now()->addSeconds(60)->timestamp, 60);

        Toaster::success('A fresh verification link has been sent to your email address.');
    }

    /**
     * Log the user out and redirect to the login page.
     */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect(route('login', absolute: false), navigate: true);
    }
}
