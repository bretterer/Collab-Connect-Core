<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class CookieConsent extends Component
{
    public bool $showBanner = false;

    public bool $showPreferences = false;

    public bool $essentialEnabled = true;

    public bool $analyticsEnabled = false;

    public bool $marketingEnabled = false;

    public function mount(): void
    {
        $consent = $this->getConsent();

        if (! $consent) {
            $this->showBanner = true;
        } else {
            $this->analyticsEnabled = $consent['analytics'] ?? false;
            $this->marketingEnabled = $consent['marketing'] ?? false;
        }
    }

    public function showPreferencesModal(): void
    {
        $this->showPreferences = true;
    }

    public static function isConsentGiven(string $type): bool
    {
        $cookie = request()->cookie('cookie_consent');

        if (! $cookie) {
            return false;
        }

        try {
            $consent = json_decode($cookie, true);

            return $consent[$type] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function acceptAll(): void
    {
        $this->analyticsEnabled = true;
        $this->marketingEnabled = true;
        $this->save();
    }

    public function denyAll(): void
    {
        $this->analyticsEnabled = false;
        $this->marketingEnabled = false;
        $this->save();
    }

    public function savePreferences(): void
    {
        $this->save();
    }

    public function resetConsent(): void
    {
        Cookie::queue(Cookie::forget('cookie_consent'));
        $this->analyticsEnabled = false;
        $this->marketingEnabled = false;
        $this->showBanner = true;
    }

    private function save(): void
    {
        $consent = [
            'essential' => true,
            'analytics' => $this->analyticsEnabled,
            'marketing' => $this->marketingEnabled,
            'timestamp' => now()->toISOString(),
        ];

        // Store consent in cookie for 1 year
        Cookie::queue('cookie_consent', json_encode($consent), 60 * 24 * 365);

        $this->showBanner = false;
        $this->modal('cookie-preferences')->close();

        // Dispatch event to load scripts if needed
        $this->dispatch('cookie-consent-saved', consent: $consent);
    }

    private function getConsent(): ?array
    {
        $cookie = request()->cookie('cookie_consent');

        if (! $cookie) {
            return null;
        }

        try {
            return json_decode($cookie, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function render()
    {
        return view('livewire.cookie-consent');
    }
}
