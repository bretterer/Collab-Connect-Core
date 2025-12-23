<?php

namespace App\Livewire\Collaborations;

use App\Enums\CollaborationStatus;
use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends BaseComponent
{
    public string $filter = 'active';

    public function getCollaborationsProperty(): Collection
    {
        $user = $this->getAuthenticatedUser();

        $query = Collaboration::query()
            ->with(['campaign', 'business', 'influencer', 'deliverables']);

        // Filter based on user type
        if ($user->isBusinessAccount()) {
            $query->where('business_id', $user->currentBusiness->id);
        } else {
            $query->where('influencer_id', $user->id);
        }

        // Apply status filter
        match ($this->filter) {
            'active' => $query->where('status', CollaborationStatus::ACTIVE),
            'completed' => $query->where('status', CollaborationStatus::COMPLETED),
            'cancelled' => $query->where('status', CollaborationStatus::CANCELLED),
            default => null,
        };

        return $query->orderBy('updated_at', 'desc')->get();
    }

    public function getCountsProperty(): array
    {
        $user = $this->getAuthenticatedUser();

        $baseQuery = Collaboration::query();

        if ($user->isBusinessAccount()) {
            $baseQuery->where('business_id', $user->currentBusiness->id);
        } else {
            $baseQuery->where('influencer_id', $user->id);
        }

        return [
            'active' => (clone $baseQuery)->where('status', CollaborationStatus::ACTIVE)->count(),
            'completed' => (clone $baseQuery)->where('status', CollaborationStatus::COMPLETED)->count(),
            'cancelled' => (clone $baseQuery)->where('status', CollaborationStatus::CANCELLED)->count(),
        ];
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render(): View
    {
        return view('livewire.collaborations.index');
    }
}
