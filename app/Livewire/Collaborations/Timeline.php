<?php

namespace App\Livewire\Collaborations;

use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use App\Services\CollaborationActivityService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class Timeline extends BaseComponent
{
    public Collaboration $collaboration;

    public int $limit = 10;

    public function mount(Collaboration $collaboration): void
    {
        $this->collaboration = $collaboration;
    }

    public function getActivitiesProperty(): Collection
    {
        return CollaborationActivityService::getTimeline($this->collaboration, $this->limit);
    }

    public function loadMore(): void
    {
        $this->limit += 10;
    }

    public function getHasMoreProperty(): bool
    {
        return $this->collaboration->activities()->count() > $this->limit;
    }

    public function getListeners(): array
    {
        return [
            "echo-presence:collaboration.{$this->collaboration->id},.activity.created" => '$refresh',
        ];
    }

    public function render(): View
    {
        return view('livewire.collaborations.timeline');
    }
}
