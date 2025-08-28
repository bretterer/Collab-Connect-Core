<?php

namespace App\Livewire\Admin;

use App\Models\Feedback as FeedbackModel;
use App\Services\GitHubService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Feedback extends Component
{
    use WithPagination;

    #[Url(as: 'type')]
    public string $selectedType = '';

    #[Url(as: 'status')]
    public string $selectedStatus = '';

    #[Url(as: 'search')]
    public string $search = '';

    public bool $showModal = false;

    public ?FeedbackModel $selectedFeedback = null;

    public string $adminNotes = '';

    public function mount()
    {
        //
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
    }

    #[Computed]
    public function feedbacks()
    {
        return FeedbackModel::query()
            ->with(['user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('subject', 'like', '%'.$this->search.'%')
                        ->orWhere('message', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->selectedType, function ($query) {
                $query->where('type', $this->selectedType);
            })
            ->when($this->selectedStatus === 'resolved', function ($query) {
                $query->where('resolved', true);
            })
            ->when($this->selectedStatus === 'unresolved', function ($query) {
                $query->where('resolved', false);
            })
            ->latest()
            ->paginate(10);
    }

    public function viewFeedback(FeedbackModel $feedback)
    {
        $this->selectedFeedback = $feedback;
        $this->adminNotes = $feedback->admin_notes ?? '';
        $this->showModal = true;
    }

    public function markAsResolved()
    {
        if ($this->selectedFeedback) {
            $this->selectedFeedback->markAsResolved($this->adminNotes);

            session()->flash('banner', [
                'type' => 'success',
                'message' => 'Feedback marked as resolved.',
                'closable' => true,
            ]);

            $this->closeModal();
        }
    }

    public function markAsUnresolved()
    {
        if ($this->selectedFeedback) {
            $this->selectedFeedback->update([
                'resolved' => false,
                'resolved_at' => null,
                'admin_notes' => $this->adminNotes,
            ]);

            session()->flash('banner', [
                'type' => 'success',
                'message' => 'Feedback marked as unresolved.',
                'closable' => true,
            ]);

            $this->closeModal();
        }
    }

    public function saveNotes()
    {
        if ($this->selectedFeedback) {
            $this->selectedFeedback->update([
                'admin_notes' => $this->adminNotes,
            ]);

            session()->flash('banner', [
                'type' => 'success',
                'message' => 'Admin notes saved.',
                'closable' => true,
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedFeedback = null;
        $this->adminNotes = '';
    }

    public function createGitHubIssue()
    {
        if (! $this->selectedFeedback) {
            return;
        }

        $githubService = new GitHubService;

        if (! $githubService->isConfigured()) {
            session()->flash('banner', [
                'type' => 'error',
                'message' => 'GitHub integration is not configured.',
                'closable' => true,
            ]);

            return;
        }

        $issueData = $githubService->createIssue($this->selectedFeedback);

        if ($issueData) {
            session()->flash('banner', [
                'type' => 'success',
                'message' => 'GitHub issue created successfully: #'.$issueData['number'],
                'closable' => true,
            ]);
        } else {
            session()->flash('banner', [
                'type' => 'error',
                'message' => 'Failed to create GitHub issue. Check the logs for details.',
                'closable' => true,
            ]);
        }
    }

    public function getScreenshotUrl(?string $screenshotPath): ?string
    {
        if (! $screenshotPath) {
            return null;
        }

        return Storage::url($screenshotPath);
    }

    public function isGitHubConfigured(): bool
    {
        return (new GitHubService)->isConfigured();
    }

    public function render()
    {
        return view('livewire.admin.feedback')->layout('layouts.app');
    }
}
