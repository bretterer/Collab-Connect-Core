<?php

namespace App\Livewire\Admin\Markets;

use App\Models\Market;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class MarketIndex extends Component
{
    use WithPagination;

    public $showCreateModal = false;

    public $name = '';

    public $description = '';

    public $editingMarket = null;

    public function createMarket()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Market::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => false,
        ]);

        $this->reset(['name', 'description', 'showCreateModal']);

        Flux::toast('Market created successfully!');
    }

    public function toggleActive(Market $market)
    {
        $market->update(['is_active' => ! $market->is_active]);

        $status = $market->is_active ? 'activated' : 'deactivated';
        Flux::toast("Market {$status} successfully!");
    }

    public function deleteMarket(Market $market)
    {
        $market->delete();

        Flux::toast('Market deleted successfully!');
    }

    public function render()
    {
        $markets = Market::withCount('zipcodes')->latest()->paginate(15);

        return view('livewire.admin.markets.market-index', [
            'markets' => $markets,
        ])->layout('layouts.app');
    }
}
