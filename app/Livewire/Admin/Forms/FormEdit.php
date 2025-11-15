<?php

namespace App\Livewire\Admin\Forms;

use App\Enums\FormFieldType;
use App\Livewire\Traits\ManagesFormFields;
use App\Models\Form;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FormEdit extends Component
{
    use ManagesFormFields;

    public Form $form;

    public string $title = '';

    public string $internalTitle = '';

    public string $description = '';

    public string $status = 'draft';

    public array $fields = [];

    public function mount(Form $form)
    {
        $this->form = $form;
        $this->title = $form->title;
        $this->internalTitle = $form->internal_title ?? '';
        $this->description = $form->description ?? '';
        $this->status = $form->status;
        $this->fields = $form->fields ?? [];

        // Ensure email field exists - add if missing (for legacy forms)
        if (! $this->hasEmailField()) {
            array_unshift($this->fields, [
                'id' => uniqid('field-'),
                'type' => FormFieldType::EMAIL->value,
                'label' => 'Email Address',
                'name' => 'email',
                'required' => true,
                'placeholder' => 'Enter your email address',
                'options' => [],
                'settings' => $this->getDefaultFieldSettings(FormFieldType::EMAIL->value),
                'is_system_field' => true,
            ]);
        }
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

        $this->form->update([
            'title' => $this->title,
            'internal_title' => $this->internalTitle ?: $this->title,
            'description' => $this->description,
            'fields' => $this->fields,
            'status' => $publish ? 'published' : $this->status,
            'published_at' => $publish && ! $this->form->published_at ? now() : $this->form->published_at,
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(
            text: $publish ? 'Form published successfully' : 'Form updated successfully',
            variant: 'success'
        );
    }

    public function render()
    {
        return view('livewire.admin.forms.form-edit', [
            'fieldTypes' => FormFieldType::cases(),
        ]);
    }
}
