<?php

namespace App\Livewire\Admin\LandingPages;

use App\Models\LandingPage;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class LandingPageIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $sortBy = 'updated_at';

    public string $sortDirection = 'desc';

    public ?int $deletingPageId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'updated_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete($id)
    {
        $this->deletingPageId = $id;
    }

    public function deletePage()
    {
        if ($this->deletingPageId) {
            $page = LandingPage::findOrFail($this->deletingPageId);
            $page->delete();

            $this->deletingPageId = null;

            Flux::toast(
                text: 'Landing page deleted successfully',
                variant: 'success'
            );
        }
    }

    public function cancelDelete()
    {
        $this->deletingPageId = null;
    }

    public function duplicatePage($id)
    {
        $page = LandingPage::findOrFail($id);

        $newPage = $page->replicate();
        $newPage->title = $page->title.' (Copy)';
        $newPage->slug = $page->slug.'-copy-'.time();
        $newPage->status = 'draft';
        $newPage->published_at = null;
        $newPage->created_by = auth()->id();
        $newPage->save();

        Flux::toast(
            text: 'Landing page duplicated successfully',
            variant: 'success'
        );

        return redirect()->route('admin.landing-pages.edit', $newPage);
    }

    public function toggleStatus($id)
    {
        $page = LandingPage::findOrFail($id);

        if ($page->isPublished()) {
            $page->unpublish();
            Flux::toast(text: 'Page unpublished', variant: 'success');
        } else {
            $page->publish();
            Flux::toast(text: 'Page published', variant: 'success');
        }
    }

    public function getStatusOptions()
    {
        return [
            '' => 'All Status',
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ];
    }

    public function render()
    {
        $pages = LandingPage::query()
            ->with(['creator', 'updater'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.landing-pages.landing-page-index', [
            'pages' => $pages,
        ]);
    }
}
