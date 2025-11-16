<?php

namespace App\Livewire\Admin\Funnels;

use App\Models\Funnel;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FunnelIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function deleteFunnel(Funnel $funnel): void
    {
        $funnel->delete();

        Flux::toast(text: 'Funnel deleted', variant: 'success');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $funnels = Funnel::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->withCount('emailSequences')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.funnels.funnel-index', [
            'funnels' => $funnels,
        ]);
    }
}
