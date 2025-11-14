<?php

namespace App\Livewire\Admin\Markets;

use App\Settings\RegistrationMarkets;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MarketSettings extends Component
{
    public bool $enabled;

    public function mount(RegistrationMarkets $settings)
    {
        $this->enabled = $settings->enabled;
    }

    public function save(RegistrationMarkets $settings)
    {
        $settings->enabled = $this->enabled;
        $settings->save();

        Flux::toast('Settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.admin.markets.market-settings');
    }
}
