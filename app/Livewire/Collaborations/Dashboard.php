<?php

namespace App\Livewire\Collaborations;

use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use App\Services\CollaborationDeliverableService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends BaseComponent
{
    public Collaboration $collaboration;

    public function mount(Collaboration $collaboration): void
    {
        $user = $this->getAuthenticatedUser();

        // Authorization check
        if (! $collaboration->hasParticipant($user)) {
            abort(403, 'You are not authorized to view this collaboration.');
        }

        $this->collaboration = $collaboration->load([
            'campaign',
            'business',
            'influencer',
            'deliverables.files',
            'activities.user',
        ]);
    }

    public function getProgressStatsProperty(): array
    {
        return CollaborationDeliverableService::getProgressStats($this->collaboration);
    }

    public function getUserRoleProperty(): ?string
    {
        return $this->collaboration->getUserRole($this->getAuthenticatedUser());
    }

    public function getIsInfluencerProperty(): bool
    {
        return $this->collaboration->isInfluencer($this->getAuthenticatedUser());
    }

    public function getIsBusinessProperty(): bool
    {
        return $this->collaboration->isBusinessMember($this->getAuthenticatedUser());
    }

    public function getListeners(): array
    {
        return [
            "echo-presence:collaboration.{$this->collaboration->id},.deliverable.status.changed" => '$refresh',
            "echo-presence:collaboration.{$this->collaboration->id},.activity.created" => '$refresh',
        ];
    }

    public function render(): View
    {
        return view('livewire.collaborations.dashboard');
    }
}
