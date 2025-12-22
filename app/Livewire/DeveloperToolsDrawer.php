<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class DeveloperToolsDrawer extends Component
{
    public ?array $cookieConsent = null;

    public function mount(): void
    {
        $this->loadCookieConsent();
    }

    #[On('reset-cookie-consent')]
    public function loadCookieConsent(): void
    {
        $cookie = request()->cookie('cookie_consent');

        if ($cookie) {
            try {
                $this->cookieConsent = json_decode($cookie, true);
            } catch (\Exception $e) {
                $this->cookieConsent = null;
            }
        } else {
            $this->cookieConsent = null;
        }
    }

    public function render()
    {
        if (! app()->environment('local')) {
            return '';
        }

        return view('livewire.developer-tools-drawer');
    }
}
