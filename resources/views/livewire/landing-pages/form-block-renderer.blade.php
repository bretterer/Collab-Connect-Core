<div>
    @if($form)
        @if($submitted)
            {{-- Success Message --}}
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-green-500 dark:text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-green-800 dark:text-green-200 text-lg">{{ $blockData['success_message'] ?? 'Thank you! Your submission has been received.' }}</p>
            </div>
        @else
            {{-- Form --}}
            <form wire:submit="submit" class="space-y-6">
                @foreach($form->fields as $field)
                    <div>
                        <label for="field-{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $field['label'] }}
                            @if($field['required'] ?? false)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>

                        @switch($field['type'])
                            @case('textarea')
                                <textarea
                                    id="field-{{ $field['name'] }}"
                                    wire:model="formData.{{ $field['name'] }}"
                                    rows="{{ $field['settings']['rows'] ?? 4 }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                ></textarea>
                                @break

                            @case('select')
                                <select
                                    id="field-{{ $field['name'] }}"
                                    wire:model="formData.{{ $field['name'] }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                >
                                    <option value="">-- Select --</option>
                                    @foreach($field['options'] ?? [] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                @break

                            @case('checkbox')
                                <div class="space-y-2">
                                    @foreach($field['options'] ?? [] as $option)
                                        <label class="flex items-center gap-2">
                                            <input
                                                type="checkbox"
                                                wire:model="formData.{{ $field['name'] }}"
                                                value="{{ $option }}"
                                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800"
                                            />
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @break

                            @case('radio')
                                <div class="space-y-2">
                                    @foreach($field['options'] ?? [] as $option)
                                        <label class="flex items-center gap-2">
                                            <input
                                                type="radio"
                                                wire:model="formData.{{ $field['name'] }}"
                                                value="{{ $option }}"
                                                class="border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800"
                                            />
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @break

                            @default
                                <input
                                    type="{{ $field['type'] }}"
                                    id="field-{{ $field['name'] }}"
                                    wire:model="formData.{{ $field['name'] }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                />
                        @endswitch

                        @error('formData.' . $field['name'])
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                {{-- Disclaimer Text --}}
                @if(!empty($blockData['disclaimer_text']))
                    <div class="text-sm" style="color: {{ $blockData['disclaimer_text_color'] ?? '#6B7280' }};">
                        {{ $blockData['disclaimer_text'] }}
                    </div>
                @endif

                {{-- Submit Button --}}
                @php
                    $widthClass = ($blockData['button_width'] ?? 'full') === 'full' ? 'w-full' : 'w-auto';
                    $sizeClasses = match($blockData['button_size'] ?? 'large') {
                        'small' => 'px-4 py-2 text-sm',
                        'medium' => 'px-6 py-3 text-base',
                        'large' => 'px-8 py-4 text-lg',
                        default => 'px-6 py-3 text-base',
                    };
                    $buttonBgColor = $blockData['button_bg_color'] ?? '#DFAD42';
                    $buttonTextColor = $blockData['button_text_color'] ?? '#000000';
                    $borderRadius = $blockData['border_radius'] ?? 8;
                    $buttonText = $blockData['button_text'] ?? 'Submit';
                @endphp

                <div>
                    <button
                        type="submit"
                        class="font-medium text-center transition-all duration-200 hover:opacity-90 border-2 border-transparent {{ $widthClass }} {{ $sizeClasses }}"
                        style="background-color: {{ $buttonBgColor }}; color: {{ $buttonTextColor }}; border-radius: {{ $borderRadius }}px;"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>{{ $buttonText }}</span>
                        <span wire:loading>Submitting...</span>
                    </button>
                </div>
            </form>
        @endif
    @else
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
            <p class="text-yellow-800 dark:text-yellow-200">Form not found or no longer available.</p>
        </div>
    @endif
</div>
