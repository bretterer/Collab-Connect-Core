<?php

namespace App\Livewire\Admin\Forms;

use App\Enums\FormFieldType;
use App\Livewire\Traits\ManagesFormFields;
use App\Models\Form;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FormCreate extends Component
{
    use ManagesFormFields;

    public string $title = '';

    public string $internalTitle = '';

    public string $description = '';

    public string $status = 'draft';

    public array $fields = [];

    public function mount()
    {
        // Automatically add email field
        $this->fields[] = [
            'id' => uniqid('field-'),
            'type' => FormFieldType::EMAIL->value,
            'label' => 'Email Address',
            'name' => 'email',
            'required' => true,
            'placeholder' => 'Enter your email address',
            'options' => [],
            'settings' => $this->getDefaultFieldSettings(FormFieldType::EMAIL->value),
            'is_system_field' => true, // Mark as system field
        ];
    }

    protected function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'internalTitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published,archived'],
            'fields' => ['required', 'array', 'min:1'],
        ];
    }

    protected $messages = [
        'fields.required' => 'You must add at least one field to the form.',
        'fields.min' => 'You must add at least one field to the form.',
    ];

    public function save($publish = false)
    {
        $this->validate();

        // Ensure email field exists
        if (! $this->hasEmailField()) {
            $this->addError('fields', 'Form must contain an email field.');

            return;
        }

        $form = Form::create([
            'title' => $this->title,
            'internal_title' => $this->internalTitle ?: $this->title,
            'description' => $this->description,
            'fields' => $this->fields,
            'status' => $publish ? 'published' : $this->status,
            'published_at' => $publish ? now() : null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(
            text: $publish ? 'Form published successfully' : 'Form saved as draft',
            variant: 'success'
        );

        return redirect()->route('admin.marketing.forms.edit', $form);
    }

    public function render()
    {
        return view('livewire.admin.forms.form-create', [
            'fieldTypes' => FormFieldType::cases(),
        ]);
    }
}
