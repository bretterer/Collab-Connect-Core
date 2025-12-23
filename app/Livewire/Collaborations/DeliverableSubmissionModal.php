<?php

namespace App\Livewire\Collaborations;

use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use App\Services\CollaborationDeliverableService;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class DeliverableSubmissionModal extends BaseComponent
{
    use WithFileUploads;

    public Collaboration $collaboration;

    public ?int $deliverableId = null;

    public ?CollaborationDeliverable $deliverable = null;

    public string $postUrl = '';

    public string $notes = '';

    public array $screenshots = [];

    public bool $showModal = false;

    protected $rules = [
        'postUrl' => 'required|url',
        'notes' => 'nullable|string|max:2000',
        'screenshots.*' => 'nullable|image|max:10240',
    ];

    protected $messages = [
        'postUrl.required' => 'Please provide a URL to your post.',
        'postUrl.url' => 'Please provide a valid URL.',
        'screenshots.*.image' => 'Only image files are allowed.',
        'screenshots.*.max' => 'Each image must be less than 10MB.',
    ];

    public function mount(Collaboration $collaboration): void
    {
        $this->collaboration = $collaboration;
    }

    #[On('open-submission-modal')]
    public function openModal(int $deliverableId): void
    {
        $this->deliverableId = $deliverableId;
        $this->deliverable = CollaborationDeliverable::find($deliverableId);

        if ($this->deliverable) {
            // Pre-fill with existing data if resubmitting
            $this->postUrl = $this->deliverable->post_url ?? '';
            $this->notes = $this->deliverable->notes ?? '';
        }

        $this->screenshots = [];
        $this->showModal = true;
    }

    public function updatedScreenshots(): void
    {
        $this->validate([
            'screenshots.*' => 'nullable|image|max:10240',
        ]);
    }

    public function removeScreenshot(int $index): void
    {
        unset($this->screenshots[$index]);
        $this->screenshots = array_values($this->screenshots);
    }

    public function submit(): void
    {
        $this->validate();

        if (! $this->deliverable) {
            Flux::toast('Deliverable not found.');

            return;
        }

        if (! $this->deliverable->canSubmit()) {
            Flux::toast('This deliverable cannot be submitted in its current state.');

            return;
        }

        // Process file uploads
        $files = [];
        foreach ($this->screenshots as $screenshot) {
            $path = 'deliverables/'.$this->deliverable->id.'/'.uniqid().'.'.$screenshot->extension();
            Storage::disk('linode')->put($path, file_get_contents($screenshot->getRealPath()), 'public');
            $files[] = [
                'path' => $path,
                'name' => $screenshot->getClientOriginalName(),
                'type' => $screenshot->getMimeType(),
            ];
        }

        CollaborationDeliverableService::submit(
            $this->deliverable,
            $this->getAuthenticatedUser(),
            $this->postUrl,
            $this->notes ?: null,
            ! empty($files) ? $files : null
        );

        Flux::toast('Deliverable submitted successfully!');

        $this->closeModal();
        $this->dispatch('deliverable-submitted');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->deliverableId = null;
        $this->deliverable = null;
        $this->postUrl = '';
        $this->notes = '';
        $this->screenshots = [];
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.collaborations.deliverable-submission-modal');
    }
}
