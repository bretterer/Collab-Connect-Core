<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class ForgotPassword extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    public string $status = '';

    public function mount() {}

    public function sendResetLink(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // We will attempt to send the password reset link to this user. For security
        // purposes, we always show a success message regardless of whether the email
        // exists in our system. This prevents email enumeration attacks.
        Password::sendResetLink(['email' => $this->email]);

        RateLimiter::clear($this->throttleKey());

        $this->status = 'If an account exists, you will receive an email with a link to reset your password.';
    }

    /**
     * Ensure the password reset request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return 'forgot-password:'.$this->email.'|'.request()->ip();
    }
}
