<?php

namespace App\Livewire\Admin\Forms;

use App\Enums\FormFieldType;
use App\Models\Form;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FormCreate extends Component
{
    public string $title = '';

    public string $internalTitle = '';

    public string $description = '';

    public string $status = 'draft';

    public array $fields = [];

    public bool $showFieldSelector = false;

    public ?int $editingFieldIndex = null;

    public array $fieldData = [];

    public ?int $deletingFieldIndex = null;

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

    // Field Management
    public function addField($fieldType)
    {
        $field = [
            'id' => uniqid('field-'),
            'type' => $fieldType,
            'label' => FormFieldType::from($fieldType)->label(),
            'name' => 'field_'.count($this->fields),
            'required' => false,
            'placeholder' => '',
            'options' => FormFieldType::from($fieldType)->hasOptions() ? ['Option 1'] : [],
            'settings' => $this->getDefaultFieldSettings($fieldType),
        ];

        $this->fields[] = $field;
        $this->showFieldSelector = false;

        Flux::toast(text: 'Field added', variant: 'success');
    }

    public function editField($fieldIndex)
    {
        $this->editingFieldIndex = $fieldIndex;
        $this->fieldData = $this->fields[$fieldIndex] ?? [];
        Flux::modal(name: 'edit-field-modal')->show();
    }

    public function saveFieldEdit()
    {
        if ($this->editingFieldIndex !== null) {
            $this->fields[$this->editingFieldIndex] = $this->fieldData;
            $this->editingFieldIndex = null;
            $this->fieldData = [];

            Flux::toast(text: 'Field updated', variant: 'success');
            Flux::modal(name: 'edit-field-modal')->close();
        }
    }

    public function cancelFieldEdit()
    {
        $this->editingFieldIndex = null;
        $this->fieldData = [];
        Flux::modal(name: 'edit-field-modal')->close();
    }

    public function duplicateField($fieldIndex)
    {
        $originalField = $this->fields[$fieldIndex];
        $duplicatedField = [
            'id' => uniqid('field-'),
            'type' => $originalField['type'],
            'label' => $originalField['label'].' (Copy)',
            'name' => $originalField['name'].'_copy',
            'required' => $originalField['required'],
            'placeholder' => $originalField['placeholder'],
            'options' => $originalField['options'],
            'settings' => $originalField['settings'],
        ];

        array_splice($this->fields, $fieldIndex + 1, 0, [$duplicatedField]);

        Flux::toast(text: 'Field duplicated', variant: 'success');
    }

    public function deleteField($fieldIndex)
    {
        $this->deletingFieldIndex = $fieldIndex;
        Flux::modal(name: 'delete-field-modal')->show();
    }

    public function confirmRemoveField()
    {
        if ($this->deletingFieldIndex !== null) {
            unset($this->fields[$this->deletingFieldIndex]);
            $this->fields = array_values($this->fields);
            $this->deletingFieldIndex = null;

            Flux::toast(text: 'Field removed', variant: 'success');
            Flux::modal(name: 'delete-field-modal')->close();
        }
    }

    public function moveFieldUp($fieldIndex)
    {
        if ($fieldIndex > 0) {
            $temp = $this->fields[$fieldIndex];
            $this->fields[$fieldIndex] = $this->fields[$fieldIndex - 1];
            $this->fields[$fieldIndex - 1] = $temp;
        }
    }

    public function moveFieldDown($fieldIndex)
    {
        if ($fieldIndex < count($this->fields) - 1) {
            $temp = $this->fields[$fieldIndex];
            $this->fields[$fieldIndex] = $this->fields[$fieldIndex + 1];
            $this->fields[$fieldIndex + 1] = $temp;
        }
    }

    public function addFieldOption()
    {
        if ($this->editingFieldIndex !== null && isset($this->fieldData['options'])) {
            $this->fieldData['options'][] = 'New Option';
        }
    }

    public function removeFieldOption($optionIndex)
    {
        if ($this->editingFieldIndex !== null && isset($this->fieldData['options'][$optionIndex])) {
            unset($this->fieldData['options'][$optionIndex]);
            $this->fieldData['options'] = array_values($this->fieldData['options']);
        }
    }

    public function save($publish = false)
    {
        $this->validate();

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

    private function getDefaultFieldSettings($fieldType): array
    {
        return match ($fieldType) {
            FormFieldType::TEXT->value, FormFieldType::EMAIL->value, FormFieldType::PHONE->value, FormFieldType::URL->value => [
                'min_length' => null,
                'max_length' => null,
                'pattern' => '',
            ],
            FormFieldType::TEXTAREA->value => [
                'min_length' => null,
                'max_length' => null,
                'rows' => 4,
            ],
            FormFieldType::NUMBER->value => [
                'min' => null,
                'max' => null,
                'step' => 1,
            ],
            FormFieldType::DATE->value => [
                'min_date' => null,
                'max_date' => null,
            ],
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.admin.forms.form-create', [
            'fieldTypes' => FormFieldType::cases(),
        ]);
    }
}
