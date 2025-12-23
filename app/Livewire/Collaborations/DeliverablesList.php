<?php

namespace App\Livewire\Collaborations;

use App\Enums\CollaborationActivityType;
use App\Enums\CollaborationDeliverableStatus;
use App\Enums\DeliverableType;
use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use App\Services\CollaborationActivityService;
use App\Services\CollaborationDeliverableService;
use App\Services\CollaborationService;
use Flux\Flux;
use Illuminate\View\View;

class DeliverablesList extends BaseComponent
{
    public Collaboration $collaboration;

    public ?int $selectedDeliverableId = null;

    public bool $showSubmissionModal = false;

    public bool $showRevisionModal = false;

    public bool $showApprovalModal = false;

    public bool $showAddDeliverableModal = false;

    public bool $showDeleteModal = false;

    public ?int $deliverableToDeleteId = null;

    public string $revisionFeedback = '';

    public string $newDeliverableType = '';

    public int $newDeliverableQuantity = 1;

    public function mount(Collaboration $collaboration): void
    {
        $this->collaboration = $collaboration;
    }

    public function getDeliverablesProperty()
    {
        return $this->collaboration->deliverables()->with('files')->get();
    }

    public function getProgressStatsProperty(): array
    {
        return CollaborationDeliverableService::getProgressStats($this->collaboration);
    }

    public function getIsInfluencerProperty(): bool
    {
        return $this->collaboration->isInfluencer($this->getAuthenticatedUser());
    }

    public function getIsBusinessProperty(): bool
    {
        return $this->collaboration->isBusinessMember($this->getAuthenticatedUser());
    }

    public function openSubmissionModal(int $deliverableId): void
    {
        $this->selectedDeliverableId = $deliverableId;
        $this->showSubmissionModal = true;
        $this->dispatch('open-submission-modal', deliverableId: $deliverableId);
    }

    public function closeSubmissionModal(): void
    {
        $this->showSubmissionModal = false;
        $this->selectedDeliverableId = null;
    }

    public function getSelectedDeliverableProperty(): ?CollaborationDeliverable
    {
        if (! $this->selectedDeliverableId) {
            return null;
        }

        return CollaborationDeliverable::with('files')->find($this->selectedDeliverableId);
    }

    public function openApprovalModal(int $deliverableId): void
    {
        $this->selectedDeliverableId = $deliverableId;
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->selectedDeliverableId = null;
    }

    public function approveDeliverable(): void
    {
        $deliverable = CollaborationDeliverable::findOrFail($this->selectedDeliverableId);

        if (! $deliverable->canApprove()) {
            Flux::toast('This deliverable cannot be approved in its current state.');

            return;
        }

        if (! $this->isBusiness) {
            Flux::toast('Only the business can approve deliverables.');

            return;
        }

        CollaborationDeliverableService::approve($deliverable, $this->getAuthenticatedUser());

        Flux::toast('Deliverable approved successfully!');

        $this->showApprovalModal = false;
        $this->selectedDeliverableId = null;

        $this->collaboration->refresh();
    }

    public function openRevisionModal(int $deliverableId): void
    {
        $this->selectedDeliverableId = $deliverableId;
        $this->revisionFeedback = '';
        $this->showRevisionModal = true;
    }

    public function requestRevision(): void
    {
        $this->validate([
            'revisionFeedback' => 'required|string|min:10|max:2000',
        ]);

        $deliverable = CollaborationDeliverable::findOrFail($this->selectedDeliverableId);

        if (! $deliverable->canRequestRevision()) {
            Flux::toast('Cannot request revision for this deliverable.');

            return;
        }

        if (! $this->isBusiness) {
            Flux::toast('Only the business can request revisions.');

            return;
        }

        CollaborationDeliverableService::requestRevision(
            $deliverable,
            $this->getAuthenticatedUser(),
            $this->revisionFeedback
        );

        Flux::toast('Revision request sent to influencer.');

        $this->showRevisionModal = false;
        $this->selectedDeliverableId = null;
        $this->revisionFeedback = '';

        $this->collaboration->refresh();
    }

    public function closeRevisionModal(): void
    {
        $this->showRevisionModal = false;
        $this->selectedDeliverableId = null;
        $this->revisionFeedback = '';
    }

    public function openAddDeliverableModal(): void
    {
        $this->newDeliverableType = '';
        $this->newDeliverableQuantity = 1;
        $this->showAddDeliverableModal = true;
    }

    public function closeAddDeliverableModal(): void
    {
        $this->showAddDeliverableModal = false;
        $this->newDeliverableType = '';
        $this->newDeliverableQuantity = 1;
    }

    public function addDeliverables(): void
    {
        $this->validate([
            'newDeliverableType' => ['required', 'string', DeliverableType::validationRule()],
            'newDeliverableQuantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        if (! $this->isBusiness) {
            Flux::toast('Only the business can add deliverables.');

            return;
        }

        $deliverableType = DeliverableType::from($this->newDeliverableType);

        for ($i = 0; $i < $this->newDeliverableQuantity; $i++) {
            CollaborationDeliverable::create([
                'collaboration_id' => $this->collaboration->id,
                'deliverable_type' => $deliverableType,
                'status' => CollaborationDeliverableStatus::NOT_STARTED,
            ]);
        }

        Flux::toast("Added {$this->newDeliverableQuantity} {$deliverableType->label()}(s)!");

        $this->closeAddDeliverableModal();
        $this->collaboration->refresh();
    }

    public function openDeleteModal(int $deliverableId): void
    {
        $this->deliverableToDeleteId = $deliverableId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deliverableToDeleteId = null;
    }

    public function getDeliverableToDeleteProperty(): ?CollaborationDeliverable
    {
        if (! $this->deliverableToDeleteId) {
            return null;
        }

        return CollaborationDeliverable::find($this->deliverableToDeleteId);
    }

    public function confirmDeleteDeliverable(): void
    {
        if (! $this->isBusiness) {
            Flux::toast('Only the business can remove deliverables.');

            return;
        }

        $deliverable = CollaborationDeliverable::find($this->deliverableToDeleteId);

        if (! $deliverable || $deliverable->collaboration_id !== $this->collaboration->id) {
            Flux::toast('Deliverable not found.');

            return;
        }

        // Only allow removing NOT_STARTED deliverables
        if ($deliverable->status !== CollaborationDeliverableStatus::NOT_STARTED) {
            Flux::toast('Can only remove deliverables that haven\'t been started.');

            return;
        }

        $deliverable->delete();
        Flux::toast('Deliverable removed.');

        $this->closeDeleteModal();
        $this->collaboration->refresh();
    }

    public function getDeliverableTypesProperty(): array
    {
        return DeliverableType::toOptions();
    }

    public function getAllDeliverablesApprovedProperty(): bool
    {
        return CollaborationDeliverableService::areAllDeliverablesApproved($this->collaboration);
    }

    public function completeCollaboration(): void
    {
        if (! $this->isBusiness) {
            Flux::toast('Only the business can complete the collaboration.');

            return;
        }

        CollaborationService::complete($this->collaboration);

        CollaborationActivityService::log(
            $this->collaboration,
            $this->getAuthenticatedUser(),
            CollaborationActivityType::COMPLETED
        );

        Flux::toast('Collaboration completed successfully!');

        $this->collaboration->refresh();
    }

    public function getListeners(): array
    {
        return [
            'deliverable-submitted' => '$refresh',
            "echo-presence:collaboration.{$this->collaboration->id},.deliverable.status.changed" => '$refresh',
        ];
    }

    public function render(): View
    {
        return view('livewire.collaborations.deliverables-list');
    }
}
