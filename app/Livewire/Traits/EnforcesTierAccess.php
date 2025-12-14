<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Provides server-side enforcement of subscription tier access.
 *
 * Use this trait in Livewire components that have tier-locked features.
 * It validates that users don't bypass client-side restrictions.
 */
trait EnforcesTierAccess
{
    /**
     * Check if user lacks access to a feature.
     *
     * @param  string  $feature  The feature key to check access for
     * @return bool True if user does NOT have access
     */
    protected function lacksFeatureAccess(string $feature): bool
    {
        $user = auth()->user();

        if (! $user) {
            return true;
        }

        // Check influencer profile
        $influencer = $user->influencer;
        if ($influencer && $influencer->hasFeatureAccess($feature)) {
            return false;
        }

        // Check business profile
        $business = $user->currentBusiness;
        if ($business && $business->hasFeatureAccess($feature)) {
            return false;
        }

        return true;
    }

    /**
     * Handle an unauthorized tier access attempt.
     * Logs the user out and redirects with a message.
     */
    protected function handleUnauthorizedTierAccess(): void
    {
        $user = auth()->user();

        // Log the attempt for security monitoring
        logger()->warning('Unauthorized tier access attempt detected', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'component' => static::class,
        ]);

        // Log out the user
        Auth::logout();

        // Invalidate the session
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // Redirect to violation page (Livewire will handle the response)
        $this->redirectRoute('tier-violation');
    }

    /**
     * Enforce tier access - if user lacks access, handle as violation.
     * Call this when a tier-locked property is being modified.
     *
     * @param  string  $feature  The feature key to check access for
     */
    protected function enforceTierAccess(string $feature): void
    {
        if ($this->lacksFeatureAccess($feature)) {
            $this->handleUnauthorizedTierAccess();
        }
    }
}
