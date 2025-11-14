<?php

namespace App\Livewire;

use Livewire\Component;

class Banner extends Component
{
    public ?array $banner = null;

    public ?array $betaBanner = null;

    public bool $visible = true;

    public bool $betaVisible = true;

    public function mount(): void
    {
        $this->banner = session('banner');

        if ($this->banner && ! isset($this->banner['closable'])) {
            $this->banner['closable'] = false;
        }

        // Clear the session banner after mounting
        session()->forget('banner');

        // Set up the beta banner
        $betaDismissed = false; // session('beta_banner_dismissed');
        if (! $betaDismissed) {
            $this->betaBanner = [
                'type' => 'info',
                'message' => '🚀 Welcome to CollabConnect Public Beta! Help us improve by reporting bugs or suggesting features.',
                'closable' => false,
            ];
        }
    }

    public function close(): void
    {
        $this->visible = false;
    }

    public function closeBeta(): void
    {
        $this->betaVisible = false;
        session(['beta_banner_dismissed' => true]);
    }

    public function render()
    {
        return view('livewire.banner');
    }
}
