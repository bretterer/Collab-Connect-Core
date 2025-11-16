<?php

namespace App\Livewire\Admin\EmailSequences;

use App\Models\EmailSequence;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmailSequenceCreate extends Component
{
    public string $name = '';

    public string $description = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save()
    {
        $this->validate();

        $emailSequence = EmailSequence::create([
            'name' => $this->name,
            'description' => $this->description,
            'subscribe_triggers' => [],
            'unsubscribe_triggers' => [],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(text: 'Email sequence created successfully', variant: 'success');

        return redirect()->route('admin.marketing.email-sequences.edit', $emailSequence);
    }

    public function render()
    {
        return view('livewire.admin.email-sequences.email-sequence-create');
    }
}
