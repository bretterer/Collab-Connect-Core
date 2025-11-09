<?php

namespace App\Livewire\Admin\Forms;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FormSubmissions extends Component
{
    use WithPagination;

    public Form $form;

    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(Form $form)
    {
        $this->form = $form;
    }

    public function updatingSearch()
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

    public function export()
    {
        $submissions = $this->form->submissions()->get();

        if ($submissions->isEmpty()) {
            \Flux\Flux::toast(
                text: 'No submissions to export',
                variant: 'warning'
            );

            return;
        }

        // Get all unique field names from form
        $headers = ['ID', 'Email', 'Submitted At'];
        foreach ($this->form->fields as $field) {
            $headers[] = $field['label'];
        }

        // Create CSV content
        $csv = [];
        $csv[] = $headers;

        foreach ($submissions as $submission) {
            $row = [
                $submission->id,
                $submission->email ?? '',
                $submission->created_at->format('Y-m-d H:i:s'),
            ];

            foreach ($this->form->fields as $field) {
                $value = $submission->data[$field['name']] ?? '';
                // Handle arrays (for checkboxes, multi-select)
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $row[] = $value;
            }

            $csv[] = $row;
        }

        // Generate CSV file
        $filename = 'form-'.$this->form->id.'-submissions-'.now()->format('Y-m-d').'.csv';

        $handle = fopen('php://temp', 'r+');
        foreach ($csv as $line) {
            fputcsv($handle, $line);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        $submissions = FormSubmission::query()
            ->where('form_id', $this->form->id)
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('email', 'like', '%'.$this->search.'%')
                        ->orWhere('data', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(25);

        return view('livewire.admin.forms.form-submissions', [
            'submissions' => $submissions,
        ]);
    }
}
