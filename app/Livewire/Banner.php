<?php

namespace App\Livewire;

use Livewire\Component;

class Banner extends Component
{
    public ?array $banner = null;

    public bool $visible = true;

    public function mount(): void
    {
        $this->banner = session('banner');

        if ($this->banner && ! isset($this->banner['closable'])) {
            $this->banner['closable'] = false;
        }

        // Clear the session banner after mounting
        session()->forget('banner');
    }

    public function close(): void
    {
        $this->visible = false;
    }

    public function render()
    {
        return view('livewire.banner');
    }
}
