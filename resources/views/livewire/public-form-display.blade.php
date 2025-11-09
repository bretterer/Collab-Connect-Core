<div>
    @if($submitted)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 mx-auto text-green-600 dark:text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-2">
                {{ $form->settings['success_message'] ?? 'Thank you for your submission!' }}
            </h3>
            <p class="text-green-700 dark:text-green-300 text-sm">
                We'll get back to you soon.
            </p>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-4">
            @foreach($form->fields as $field)
                <div>
                    <flux:label>
                        {{ $field['label'] }}
                        @if($field['required'] ?? false)
                            <span class="text-red-500">*</span>
                        @endif
                    </flux:label>

                    @if($field['type'] === 'text' || $field['type'] === 'email')
                        <flux:input
                            type="{{ $field['type'] }}"
                            wire:model="formData.{{ $field['name'] }}"
                            placeholder="{{ $field['placeholder'] ?? '' }}"
                        />
                    @elseif($field['type'] === 'textarea')
                        <flux:textarea
                            wire:model="formData.{{ $field['name'] }}"
                            placeholder="{{ $field['placeholder'] ?? '' }}"
                            rows="4"
                        />
                    @elseif($field['type'] === 'select')
                        <flux:select wire:model="formData.{{ $field['name'] }}">
                            <option value="">Select an option</option>
                            @if(isset($field['options']))
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            @endif
                        </flux:select>
                    @elseif($field['type'] === 'checkbox')
                        <flux:checkbox wire:model="formData.{{ $field['name'] }}">
                            {{ $field['label'] }}
                        </flux:checkbox>
                    @endif

                    @error("formData.{$field['name']}")
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>
            @endforeach

            <div class="pt-4">
                <flux:button type="submit" class="w-full">
                    {{ $form->settings['submit_button_text'] ?? 'Submit' }}
                </flux:button>
            </div>
        </form>
    @endif
</div>
