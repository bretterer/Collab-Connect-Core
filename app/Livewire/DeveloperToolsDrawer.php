<?php

namespace App\Livewire;

use Livewire\Component;

class DeveloperToolsDrawer extends Component
{
    public function render()
    {
        if (! app()->environment('local')) {
            return '';
        }

        return view('livewire.developer-tools-drawer');
    }
}
