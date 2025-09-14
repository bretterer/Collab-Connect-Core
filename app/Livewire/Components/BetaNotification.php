<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BetaNotification extends Component
{
    public bool $showModal = false;

    public function mount(): void
    {
        $this->showModal = Session::pull('login_success', false);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.components.beta-notification');
    }
}
