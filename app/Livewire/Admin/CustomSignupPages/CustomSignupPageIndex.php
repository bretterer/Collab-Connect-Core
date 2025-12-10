<?php

namespace App\Livewire\Admin\CustomSignupPages;

use App\Models\CustomSignupPage;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CustomSignupPageIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $accountTypeFilter = '';

    public string $sortBy = 'updated_at';

    public string $sortDirection = 'desc';

    public ?int $deletingPageId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'accountTypeFilter' => ['except' => ''],
        'sortBy' => ['except' => 'updated_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAccountTypeFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingPageId = $id;
    }

    public function deletePage(): void
    {
        if ($this->deletingPageId) {
            $page = CustomSignupPage::findOrFail($this->deletingPageId);
            $page->delete();

            $this->deletingPageId = null;

            Flux::toast(
                text: 'Custom signup page deleted successfully',
                variant: 'success'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->deletingPageId = null;
    }

    public function duplicatePage(int $id): mixed
    {
        $page = CustomSignupPage::findOrFail($id);

        $newPage = $page->replicate();
        $newPage->name = $page->name.' (Copy)';
        $newPage->slug = $page->slug.'-copy-'.time();
        $newPage->is_active = false;
        $newPage->published_at = null;
        $newPage->created_by = auth()->id();
        $newPage->save();

        Flux::toast(
            text: 'Custom signup page duplicated successfully',
            variant: 'success'
        );

        return redirect()->route('admin.custom-signup-pages.edit', $newPage);
    }

    public function toggleStatus(int $id): void
    {
        $page = CustomSignupPage::findOrFail($id);

        if ($page->isPublished()) {
            $page->unpublish();
            Flux::toast(text: 'Page unpublished', variant: 'success');
        } else {
            $page->publish();
            Flux::toast(text: 'Page published', variant: 'success');
        }
    }

    public function getStatusOptions(): array
    {
        return [
            '' => 'All Status',
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    public function getAccountTypeOptions(): array
    {
        return [
            '' => 'All Account Types',
            'influencer' => 'Influencer',
            'business' => 'Business',
        ];
    }

    public function render()
    {
        $pages = CustomSignupPage::query()
            ->with(['creator', 'updater'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%')
                        ->orWhere('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } else {
                    $query->where('is_active', false);
                }
            })
            ->when($this->accountTypeFilter, function (Builder $query) {
                $query->where('account_type', $this->accountTypeFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.custom-signup-pages.custom-signup-page-index', [
            'pages' => $pages,
        ]);
    }
}
