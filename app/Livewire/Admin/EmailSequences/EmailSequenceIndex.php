<?php

namespace App\Livewire\Admin\EmailSequences;

use App\Models\EmailSequence;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EmailSequenceIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function deleteSequence(EmailSequence $sequence): void
    {
        $sequence->delete();

        Flux::toast(text: 'Email sequence deleted', variant: 'success');
    }

    public function duplicateSequence(EmailSequence $sequence)
    {
        $newSequence = $sequence->replicate();
        $newSequence->name = $sequence->name.' (Copy)';
        $newSequence->created_by = auth()->id();
        $newSequence->updated_by = auth()->id();
        $newSequence->save();

        // Duplicate all emails
        foreach ($sequence->emails as $email) {
            $newEmail = $email->replicate();
            $newEmail->email_sequence_id = $newSequence->id;
            $newEmail->save();
        }

        Flux::toast(text: 'Email sequence duplicated', variant: 'success');

        return redirect()->route('admin.marketing.email-sequences.edit', $newSequence);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $sequences = EmailSequence::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->withCount(['emails', 'activeSubscribers'])
            ->latest()
            ->paginate(15);

        return view('livewire.admin.email-sequences.email-sequence-index', [
            'sequences' => $sequences,
        ]);
    }
}
