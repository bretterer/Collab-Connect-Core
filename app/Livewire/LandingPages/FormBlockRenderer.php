<?php

namespace App\Livewire\LandingPages;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\LandingPage;
use App\Services\EmailSequenceService;
use Livewire\Component;

class FormBlockRenderer extends Component
{
    public ?Form $form = null;

    public array $blockData = [];

    public array $formData = [];

    public bool $submitted = false;

    public function mount($formId, $blockData = [])
    {
        $this->form = Form::find($formId);
        $this->blockData = $blockData;

        // Initialize form data array with empty values
        if ($this->form) {
            foreach ($this->form->fields as $field) {
                $this->formData[$field['name']] = '';
            }
        }
    }

    public function submit()
    {
        if (! $this->form) {
            return;
        }

        // Build validation rules dynamically based on form fields
        $rules = [];
        foreach ($this->form->fields as $field) {
            $fieldRules = [];

            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific validation
            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    if (isset($field['settings']['min'])) {
                        $fieldRules[] = 'min:'.$field['settings']['min'];
                    }
                    if (isset($field['settings']['max'])) {
                        $fieldRules[] = 'max:'.$field['settings']['max'];
                    }
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            // Add length validation for text fields
            if (in_array($field['type'], ['text', 'textarea', 'email', 'phone', 'url'])) {
                if (isset($field['settings']['max_length'])) {
                    $fieldRules[] = 'max:'.$field['settings']['max_length'];
                }
                if (isset($field['settings']['min_length'])) {
                    $fieldRules[] = 'min:'.$field['settings']['min_length'];
                }
            }

            $rules['formData.'.$field['name']] = $fieldRules;
        }

        // Validate the form
        $this->validate($rules);

        // Create the form submission
        FormSubmission::create([
            'form_id' => $this->form->id,
            'data' => $this->formData,
            'email' => $this->formData['email'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
        ]);

        // Trigger email sequences
        if (isset($this->formData['email'])) {
            app(EmailSequenceService::class)->handleFormSubmission(
                formId: $this->form->id,
                data: $this->formData
            );
        }

        // Fire Livewire event if enabled
        if ($this->blockData['fire_event'] ?? true) {
            $this->dispatch('form-submitted', formId: $this->form->id, data: $this->formData);
        }

        // Handle thank you action
        $thankYouAction = $this->blockData['thank_you_action'] ?? 'message';

        if ($thankYouAction === 'landing_page') {
            $landingPageId = $this->blockData['thank_you_landing_page_id'] ?? null;
            if ($landingPageId) {
                $landingPage = LandingPage::find($landingPageId);
                if ($landingPage) {
                    return redirect()->to('/l/'.$landingPage->slug);
                }
            }
        } elseif ($thankYouAction === 'url') {
            $url = $this->blockData['thank_you_url'] ?? null;
            if ($url) {
                return redirect()->to($url);
            }
        }

        // Default to showing success message
        $this->submitted = true;

        // Reset form data
        foreach ($this->form->fields as $field) {
            $this->formData[$field['name']] = '';
        }
    }

    public function render()
    {
        return view('livewire.landing-pages.form-block-renderer');
    }
}
