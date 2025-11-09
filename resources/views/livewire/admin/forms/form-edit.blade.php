<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Form</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                {{ $form->title }}
            </p>
        </div>
        <div class="flex gap-3">
            <flux:button variant="ghost" :href="route('admin.marketing.forms.submissions', $form)" wire:navigate icon="clipboard-document-list">
                View Submissions ({{ $form->submissions->count() }})
            </flux:button>
            <flux:button variant="ghost" :href="route('admin.marketing.forms.index')" wire:navigate>
                Cancel
            </flux:button>
            <flux:button wire:click="save(false)" icon="check">
                Save Changes
            </flux:button>
            @if($form->isDraft())
                <flux:button wire:click="save(true)" variant="primary" icon="arrow-up-tray">
                    Publish
                </flux:button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <!-- Form Details -->
            <flux:card>
                <div class="flex flex-col gap-6">
                    <flux:field>
                        <flux:label>Title *</flux:label>
                        <flux:input wire:model.live="title" placeholder="e.g., Contact Form, Newsletter Signup" />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Internal Title</flux:label>
                        <flux:description>Optional title for internal reference only</flux:description>
                        <flux:input wire:model="internalTitle" placeholder="e.g., Fall 2025 Lead Gen Form" />
                        <flux:error name="internalTitle" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Description</flux:label>
                        <flux:description>Optional description for this form</flux:description>
                        <flux:textarea wire:model="description" rows="3" placeholder="Describe the purpose of this form..." />
                        <flux:error name="description" />
                    </flux:field>
                </div>
            </flux:card>

            <!-- Form Fields -->
            <flux:card>
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Form Fields</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Add and configure fields for your form</p>
                    </div>
                    <flux:button wire:click="$set('showFieldSelector', true)" icon="plus" size="sm">
                        Add Field
                    </flux:button>
                </div>

                @if(count($fields) > 0)
                    <div class="flex flex-col gap-4">
                        @foreach($fields as $index => $field)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50" wire:key="{{ $field['id'] ?? $index }}">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">

                                        <flux:icon name="{{ App\Enums\FormFieldType::from($field['type'] ?? 'text')->icon() }}" class="w-5 h-5 text-gray-500 flex-shrink-0" />
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $field['label'] ?? 'Untitled Field' }}</span>
                                                @if($field['required'] ?? false)
                                                    <flux:badge color="red" size="sm">Required</flux:badge>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                {{ App\Enums\FormFieldType::from($field['type'] ?? 'text')->label() }}
                                                @if(!empty($field['placeholder'] ?? ''))
                                                    <span class="text-gray-400">•</span> {{ $field['placeholder'] }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        @if($index > 0)
                                            <flux:button wire:click="moveFieldUp({{ $index }})" icon="arrow-up" variant="ghost" size="sm" />
                                        @endif
                                        @if($index < count($fields) - 1)
                                            <flux:button wire:click="moveFieldDown({{ $index }})" icon="arrow-down" variant="ghost" size="sm" />
                                        @endif
                                        <flux:button wire:click="editField({{ $index }})" icon="pencil" variant="ghost" size="sm" />
                                        <flux:button wire:click="duplicateField({{ $index }})" icon="document-duplicate" variant="ghost" size="sm" />
                                        <flux:button wire:click="deleteField({{ $index }})" icon="trash" variant="ghost" size="sm" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                        <flux:icon.clipboard-document-list class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No fields yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding your first field.</p>
                        <div class="mt-6">
                            <flux:button wire:click="$set('showFieldSelector', true)" icon="plus">
                                Add Your First Field
                            </flux:button>
                        </div>
                    </div>
                @endif
                <flux:error name="fields" />
            </flux:card>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            <flux:card>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Form Settings</h3>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </flux:select>
                </flux:field>
            </flux:card>

            <flux:card>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Form Stats</h3>
                <div class="flex flex-col gap-4">
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $form->submissions->count() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Submissions</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Created</div>
                        <div class="text-sm text-gray-900 dark:text-white">{{ $form->created_at->format('M d, Y') }}</div>
                    </div>
                    @if($form->published_at)
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Published</div>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $form->published_at->format('M d, Y') }}</div>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>

    <!-- Field Selector Modal -->
    <flux:modal :open="$showFieldSelector" wire:model="showFieldSelector" class="max-w-3xl">
        <flux:heading>Add Field</flux:heading>
        <flux:subheading>Select a field type to add to your form</flux:subheading>

        <div class="grid grid-cols-2 gap-4 mt-6">
            @foreach($fieldTypes as $fieldType)
                <button
                    wire:click="addField('{{ $fieldType->value }}')"
                    class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-blue-500 dark:hover:border-blue-500 transition">
                    <flux:icon name="{{ $fieldType->icon() }}" class="w-6 h-6 text-gray-500" />
                    <div class="text-left">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $fieldType->label() }}</div>
                    </div>
                </button>
            @endforeach
        </div>
    </flux:modal>

    <!-- Field Edit Modal -->
    <flux:modal name="edit-field-modal" class="max-w-2xl">
        @if($editingFieldIndex !== null && isset($fieldData['type']))
            <flux:heading>Edit Field</flux:heading>

            <div class="flex flex-col gap-4 mt-6">
                <flux:field>
                    <flux:label>Field Label *</flux:label>
                    <flux:input wire:model="fieldData.label" />
                </flux:field>

                <flux:field>
                    <flux:label>Field Name *</flux:label>
                    <flux:description>Used to identify this field in submissions</flux:description>
                    <flux:input wire:model="fieldData.name" />
                </flux:field>

                <flux:field>
                    <flux:label>Placeholder</flux:label>
                    <flux:input wire:model="fieldData.placeholder" />
                </flux:field>

                <div class="flex items-center gap-2 mt-4">
                    <flux:checkbox wire:model="fieldData.required" />
                    <flux:label>Required Field</flux:label>
                </div>

                @if(App\Enums\FormFieldType::from($fieldData['type'])->hasOptions())
                    <div>
                        <flux:label>Options</flux:label>
                        <div class="flex flex-col gap-2 mt-2">
                            @foreach($fieldData['options'] ?? [] as $optionIndex => $option)
                                <div class="flex items-center gap-2">
                                    <flux:input wire:model="fieldData.options.{{ $optionIndex }}" class="flex-1" />
                                    <flux:button
                                        wire:click="removeFieldOption({{ $optionIndex }})"
                                        icon="trash"
                                        variant="ghost"
                                        size="sm"
                                    />
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <flux:button wire:click="addFieldOption" icon="plus" size="sm">
                                Add Option
                            </flux:button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="cancelFieldEdit">Cancel</flux:button>
                <flux:button wire:click="saveFieldEdit">Save Changes</flux:button>
            </div>
        @endif
    </flux:modal>

    <!-- Delete Field Confirmation Modal -->
    <flux:modal name="delete-field-modal" class="max-w-md">
        <div class="space-y-6">
            <div>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <flux:icon.trash class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <flux:heading class="text-center">Delete Field</flux:heading>
                <flux:subheading class="text-center mt-2">
                    Are you sure you want to delete this field? This action cannot be undone.
                </flux:subheading>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click="$set('deletingFieldIndex', null)">Cancel</flux:button>
                <flux:button variant="danger" wire:click="confirmRemoveField">Delete Field</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
