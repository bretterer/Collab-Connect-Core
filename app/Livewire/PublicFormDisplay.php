<?php

namespace App\Livewire;

use App\Models\Form;
use App\Models\FormSubmission;
use Livewire\Component;

class PublicFormDisplay extends Component
{
    public int $formId;

    public array $formData = [];

    public bool $submitted = false;

    public function mount(int $formId)
    {
        $this->formId = $formId;
    }

    public function submit()
    {
        $form = Form::findOrFail($this->formId);

        // Build validation rules from form fields
        $rules = [];
        foreach ($form->fields as $field) {
            $fieldRules = [];
            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            }

            if ($field['type'] === 'email') {
                $fieldRules[] = 'email';
            }

            if (! empty($fieldRules)) {
                $rules["formData.{$field['name']}"] = $fieldRules;
            }
        }

        $this->validate($rules);

        // Create form submission
        FormSubmission::create([
            'form_id' => $form->id,
            'data' => $this->formData,
            'submitted_at' => now(),
        ]);

        $this->submitted = true;
        $this->formData = [];
    }

    public function render()
    {
        $form = Form::findOrFail($this->formId);

        return view('livewire.public-form-display', [
            'form' => $form,
        ]);
    }
}
