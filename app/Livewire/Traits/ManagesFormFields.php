<?php

namespace App\Livewire\Traits;

use App\Enums\FormFieldType;
use Flux\Flux;

trait ManagesFormFields
{
    public bool $showFieldSelector = false;

    public ?int $editingFieldIndex = null;

    public array $fieldData = [];

    public ?int $deletingFieldIndex = null;

    /**
     * Check if the email field exists in the form
     */
    private function hasEmailField(): bool
    {
        return collect($this->fields)->contains(function ($field) {
            return ($field['type'] ?? '') === FormFieldType::EMAIL->value;
        });
    }

    /**
     * Add a new field to the form
     */
    public function addField($fieldType): void
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

    /**
     * Edit a field
     */
    public function editField($fieldIndex): void
    {
        $field = $this->fields[$fieldIndex] ?? [];

        // Prevent editing of email field
        if (($field['type'] ?? '') === FormFieldType::EMAIL->value) {
            Flux::toast(text: 'Email field cannot be edited.', variant: 'error');

            return;
        }

        $this->editingFieldIndex = $fieldIndex;
        $this->fieldData = $this->fields[$fieldIndex] ?? [];
        Flux::modal(name: 'edit-field-modal')->show();
    }

    /**
     * Save field edits
     */
    public function saveFieldEdit(): void
    {
        if ($this->editingFieldIndex !== null) {
            $this->fields[$this->editingFieldIndex] = $this->fieldData;
            $this->editingFieldIndex = null;
            $this->fieldData = [];

            Flux::toast(text: 'Field updated', variant: 'success');
            Flux::modal(name: 'edit-field-modal')->close();
        }
    }

    /**
     * Cancel field edit
     */
    public function cancelFieldEdit(): void
    {
        $this->editingFieldIndex = null;
        $this->fieldData = [];
        Flux::modal(name: 'edit-field-modal')->close();
    }

    /**
     * Duplicate a field
     */
    public function duplicateField($fieldIndex): void
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

    /**
     * Delete a field
     */
    public function deleteField($fieldIndex): void
    {
        $this->deletingFieldIndex = $fieldIndex;
        Flux::modal(name: 'delete-field-modal')->show();
    }

    /**
     * Confirm field removal
     */
    public function confirmRemoveField(): void
    {
        if ($this->deletingFieldIndex !== null) {
            $field = $this->fields[$this->deletingFieldIndex];

            // Prevent deletion of email field
            if (($field['type'] ?? '') === FormFieldType::EMAIL->value) {
                Flux::toast(text: 'Email field cannot be removed from the form.', variant: 'error');
                Flux::modal(name: 'delete-field-modal')->close();
                $this->deletingFieldIndex = null;

                return;
            }

            unset($this->fields[$this->deletingFieldIndex]);
            $this->fields = array_values($this->fields);
            $this->deletingFieldIndex = null;

            Flux::toast(text: 'Field removed', variant: 'success');
            Flux::modal(name: 'delete-field-modal')->close();
        }
    }

    /**
     * Move field up
     */
    public function moveFieldUp($fieldIndex): void
    {
        if ($fieldIndex > 0) {
            $temp = $this->fields[$fieldIndex];
            $this->fields[$fieldIndex] = $this->fields[$fieldIndex - 1];
            $this->fields[$fieldIndex - 1] = $temp;
        }
    }

    /**
     * Move field down
     */
    public function moveFieldDown($fieldIndex): void
    {
        if ($fieldIndex < count($this->fields) - 1) {
            $temp = $this->fields[$fieldIndex];
            $this->fields[$fieldIndex] = $this->fields[$fieldIndex + 1];
            $this->fields[$fieldIndex + 1] = $temp;
        }
    }

    /**
     * Add option to field
     */
    public function addFieldOption(): void
    {
        if ($this->editingFieldIndex !== null && isset($this->fieldData['options'])) {
            $this->fieldData['options'][] = 'New Option';
        }
    }

    /**
     * Remove option from field
     */
    public function removeFieldOption($optionIndex): void
    {
        if ($this->editingFieldIndex !== null && isset($this->fieldData['options'][$optionIndex])) {
            unset($this->fieldData['options'][$optionIndex]);
            $this->fieldData['options'] = array_values($this->fieldData['options']);
        }
    }

    /**
     * Get default field settings based on field type
     */
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
}
