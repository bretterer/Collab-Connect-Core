<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FormIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $sortBy = 'updated_at';

    public string $sortDirection = 'desc';

    public ?int $deletingFormId = null;

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
        $this->deletingFormId = $id;
    }

    public function deleteForm()
    {
        if ($this->deletingFormId) {
            $form = Form::findOrFail($this->deletingFormId);
            $form->delete();

            $this->deletingFormId = null;

            Flux::toast(
                text: 'Form deleted successfully',
                variant: 'success'
            );
        }
    }

    public function cancelDelete()
    {
        $this->deletingFormId = null;
    }

    public function duplicateForm($id)
    {
        $form = Form::findOrFail($id);

        $newForm = $form->replicate();
        $newForm->title = $form->title.' (Copy)';
        $newForm->internal_title = $form->internal_title.' (Copy)';
        $newForm->status = 'draft';
        $newForm->published_at = null;
        $newForm->created_by = auth()->id();
        $newForm->save();

        Flux::toast(
            text: 'Form duplicated successfully',
            variant: 'success'
        );

        return redirect()->route('admin.marketing.forms.edit', $newForm);
    }

    public function toggleStatus($id)
    {
        $form = Form::findOrFail($id);

        if ($form->isPublished()) {
            $form->unpublish();
            Flux::toast(text: 'Form unpublished', variant: 'success');
        } else {
            $form->publish();
            Flux::toast(text: 'Form published', variant: 'success');
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
        $forms = Form::query()
            ->with(['creator', 'updater'])
            ->withCount('submissions')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('internal_title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.forms.form-index', [
            'forms' => $forms,
        ]);
    }
}
