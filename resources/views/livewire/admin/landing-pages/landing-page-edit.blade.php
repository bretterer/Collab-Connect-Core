<div class="h-svh flex flex-col bg-gray-50 dark:bg-gray-900">
    <!-- Top Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button variant="ghost" size="sm" :href="route('admin.marketing.landing-pages.index')" wire:navigate icon="arrow-left">
                    Back
                </flux:button>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $title ?: 'Untitled Page' }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ count($sections) }} sections
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="outline" wire:click="save(false)" size="sm">
                    Save Draft
                </flux:button>
                <flux:button wire:click="save(true)" size="sm">
                    Publish
                </flux:button>
            </div>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        <!-- Left Sidebar - Sections List -->
        <div class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
            <!-- Page Settings -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="space-y-3">
                    <div>
                        <flux:label class="text-xs">Page Title</flux:label>
                        <flux:input wire:model.blur="title" placeholder="Enter title" class="text-sm" />
                    </div>
                    <div>
                        <flux:label class="text-xs">URL Slug</flux:label>
                        <flux:input wire:model="slug" placeholder="url-slug" class="text-sm">
                            <x-slot:prefix>
                                <span class="text-xs text-gray-500">/l/</span>
                            </x-slot:prefix>
                        </flux:input>
                    </div>
                </div>
            </div>

            <!-- Sections List -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Sections</h3>
                    <flux:button size="xs" variant="ghost" wire:click="addSection" icon="plus"></flux:button>
                </div>

                @if(count($sections) > 0)
                    <div class="space-y-2">
                        @foreach($sections as $sectionIndex => $section)
                            <div
                                class="group border rounded-lg p-3 cursor-pointer transition-all {{ $selectedSectionId === $section['id'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}"
                                wire:click="selectSection('{{ $section['id'] }}')"
                                wire:key="{{ $section['id'] }}"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <input
                                            type="text"
                                            value="{{ $section['name'] }}"
                                            wire:change="renameSection('{{ $section['id'] }}', $event.target.value)"
                                            class="w-full text-sm font-medium bg-transparent border-none p-0 focus:ring-0 text-gray-900 dark:text-white"
                                            placeholder="Section name"
                                        />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ count($section['blocks']) }} blocks
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="flex flex-col gap-1">
                                            <button
                                                wire:click.stop="moveSectionUp({{ $sectionIndex }})"
                                                @if($sectionIndex === 0) disabled @endif
                                                class="text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            </button>
                                            <button
                                                wire:click.stop="moveSectionDown({{ $sectionIndex }})"
                                                @if($sectionIndex === count($sections) - 1) disabled @endif
                                                class="text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <button
                                            wire:click.stop="editSectionSettings('{{ $section['id'] }}')"
                                            class="text-gray-400 hover:text-blue-600"
                                            title="Section settings"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </button>
                                        <button
                                            wire:click.stop="deleteSection('{{ $section['id'] }}')"
                                            class="text-gray-400 hover:text-red-600"
                                            title="Delete section"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Blocks in this section -->
                                @if($selectedSectionId === $section['id'] && count($section['blocks']) > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 space-y-1">
                                        @foreach($section['blocks'] as $blockIndex => $block)
                                            @php
                                                $blockType = App\Enums\LandingPageBlockType::from($block['type']);
                                            @endphp
                                            <div class="flex items-center justify-between text-xs py-1 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700" wire:key="{{ $block['id'] }}">
                                                <span class="text-gray-700 dark:text-gray-300">{{ $blockType->label() }}</span>
                                                <div class="flex gap-1">
                                                    <flux:button
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="pencil"
                                                        wire:click.stop="editBlock('{{ $section['id'] }}', {{ $blockIndex }})"
                                                    />
                                                    <flux:button
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="document-duplicate"
                                                        wire:click.stop="duplicateBlock('{{ $section['id'] }}', {{ $blockIndex }})"
                                                        title="Duplicate block"
                                                    />
                                                    <flux:button
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="trash"
                                                        wire:click.stop="deleteBlock('{{ $section['id'] }}', {{ $blockIndex }})"
                                                    />
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Add Block Button -->
                                @if($selectedSectionId === $section['id'])
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <flux:button
                                            size="xs"
                                            variant="outline"
                                            icon="plus"
                                            wire:click.stop="$set('showBlockSelector', true); $set('selectedSectionId', '{{ $section['id'] }}')"
                                            class="w-full"
                                        >
                                            Add Block
                                        </flux:button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">No sections yet</p>
                        <flux:button size="sm" wire:click="addSection" icon="plus">
                            Add Section
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Two Step Optin Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Two Step Optin</h3>
                    <flux:switch wire:model.live="twoStepOptinEnabled" size="sm" />
                </div>

                @if($twoStepOptinEnabled)
                    <div class="space-y-2 mt-3">
                        @if(count($twoStepOptinBlocks) > 0)
                            @foreach($twoStepOptinBlocks as $blockIndex => $block)
                                @php
                                    $blockType = App\Enums\LandingPageBlockType::from($block['type']);
                                @endphp
                                <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                            {{ $blockType->label() }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="pencil"
                                            wire:click="editTwoStepOptinBlock({{ $blockIndex }})"
                                        />
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="document-duplicate"
                                            wire:click="duplicateTwoStepOptinBlock({{ $blockIndex }})"
                                        />
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            wire:click="deleteTwoStepOptinBlock({{ $blockIndex }})"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <flux:button
                            size="xs"
                            variant="outline"
                            icon="plus"
                            wire:click="$set('editingTwoStepOptin', 'selector')"
                            class="w-full"
                        >
                            Add Block
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Exit Popup Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Exit Popup</h3>
                    <flux:switch wire:model.live="exitPopupEnabled" size="sm" />
                </div>

                @if($exitPopupEnabled)
                    <div class="space-y-2 mt-3">
                        @if(count($exitPopupBlocks) > 0)
                            @foreach($exitPopupBlocks as $blockIndex => $block)
                                @php
                                    $blockType = App\Enums\LandingPageBlockType::from($block['type']);
                                @endphp
                                <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                            {{ $blockType->label() }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="pencil"
                                            wire:click="editExitPopupBlock({{ $blockIndex }})"
                                        />
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="document-duplicate"
                                            wire:click="duplicateExitPopupBlock({{ $blockIndex }})"
                                        />
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="trash"
                                            wire:click="deleteExitPopupBlock({{ $blockIndex }})"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <flux:button
                            size="xs"
                            variant="outline"
                            icon="plus"
                            wire:click="$set('editingExitPopup', 'selector')"
                            class="w-full"
                        >
                            Add Block
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Center - Preview -->
        <div class="flex-1 bg-gray-100 dark:bg-gray-950 overflow-auto">
            <div class="h-full">
                @if(count($sections) > 0)
                    <div class="bg-white dark:bg-gray-900">
                        @foreach($sections as $section)
                            @php
                                $settings = $section['settings'];

                                // Background settings
                                $bgColor = $settings['background_color'] ?? '#ffffff';
                                $bgImage = $settings['background_image'] ?? '';
                                $bgPosition = $settings['background_position'] ?? 'center';
                                $bgFixed = $settings['background_fixed'] ?? false;

                                // Desktop layout
                                $desktopHide = $settings['desktop_hide'] ?? false;
                                $desktopPaddingTop = $settings['desktop_padding_top'] ?? 64;
                                $desktopPaddingBottom = $settings['desktop_padding_bottom'] ?? 64;
                                $desktopPaddingLeft = $settings['desktop_padding_left'] ?? 16;
                                $desktopPaddingRight = $settings['desktop_padding_right'] ?? 16;
                                $desktopVerticalAlign = $settings['desktop_vertical_align'] ?? 'top';
                                $desktopHorizontalAlign = $settings['desktop_horizontal_align'] ?? 'left';

                                // Mobile layout
                                $mobileHide = $settings['mobile_hide'] ?? false;
                                $mobilePaddingTop = $settings['mobile_padding_top'] ?? 48;
                                $mobilePaddingBottom = $settings['mobile_padding_bottom'] ?? 48;
                                $mobilePaddingLeft = $settings['mobile_padding_left'] ?? 16;
                                $mobilePaddingRight = $settings['mobile_padding_right'] ?? 16;

                                // Build background styles
                                $backgroundStyles = "background-color: {$bgColor};";
                                if ($bgImage) {
                                    $bgAttachment = $bgFixed ? 'fixed' : 'scroll';
                                    $bgPositionMap = [
                                        'top' => 'center top',
                                        'center' => 'center center',
                                        'bottom' => 'center bottom',
                                    ];
                                    $bgPositionValue = $bgPositionMap[$bgPosition] ?? 'center center';
                                    $backgroundStyles .= " background-image: url('{$bgImage}'); background-size: cover; background-position: {$bgPositionValue}; background-attachment: {$bgAttachment};";
                                }

                                // Build responsive classes
                                $visibilityClasses = '';
                                if ($desktopHide && $mobileHide) {
                                    $visibilityClasses = 'hidden';
                                } elseif ($desktopHide) {
                                    $visibilityClasses = 'hidden md:block';
                                } elseif ($mobileHide) {
                                    $visibilityClasses = 'block md:hidden';
                                }

                                // Build flex alignment classes
                                $verticalAlignMap = [
                                    'top' => 'items-start',
                                    'center' => 'items-center',
                                    'bottom' => 'items-end',
                                ];
                                $horizontalAlignMap = [
                                    'left' => 'justify-start',
                                    'center' => 'justify-center',
                                    'right' => 'justify-end',
                                    'space-around' => 'justify-around',
                                    'space-between' => 'justify-between',
                                ];
                                $flexClasses = ($verticalAlignMap[$desktopVerticalAlign] ?? 'items-start') . ' ' . ($horizontalAlignMap[$desktopHorizontalAlign] ?? 'justify-start');
                            @endphp

                            <section
                                style="{{ $backgroundStyles }} padding: {{ $mobilePaddingTop }}px {{ $mobilePaddingRight }}px {{ $mobilePaddingBottom }}px {{ $mobilePaddingLeft }}px; @media (min-width: 768px) { padding: {{ $desktopPaddingTop }}px {{ $desktopPaddingRight }}px {{ $desktopPaddingBottom }}px {{ $desktopPaddingLeft }}px; }"
                                class="relative group {{ $visibilityClasses }}"
                            >
                                <!-- Section overlay when selected -->
                                @if($selectedSectionId === $section['id'])
                                    <div class="absolute inset-0 border-2 border-blue-500 pointer-events-none"></div>
                                @endif

                                <div class="flex {{ $flexClasses }} min-h-full">
                                    <div class="w-full">
                                        @foreach($section['blocks'] as $block)
                                            @include('landing-pages.blocks.' . $block['type'], ['data' => $block['data']])
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Start Building</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Add your first section to get started</p>
                            <flux:button wire:click="addSection" icon="plus">
                                Add Section
                            </flux:button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Panel - Block/Section Editor -->
        @if($editingBlockIndex !== null && $selectedSectionId)
            @php
                $editingSection = collect($sections)->firstWhere('id', $selectedSectionId);
                $editingBlock = $editingSection['blocks'][$editingBlockIndex] ?? null;
            @endphp

            @if($editingBlock)
                @php
                    $blockType = App\Enums\LandingPageBlockType::from($editingBlock['type']);
                @endphp
                <div class="w-96 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Edit {{ $blockType->label() }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Customize block settings</p>
                            </div>
                            <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelBlockEdit" />
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4">
                        @include('livewire.admin.landing-pages.blocks.' . $editingBlock['type'])
                    </div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <flux:button variant="ghost" wire:click="cancelBlockEdit" size="sm">
                            Cancel
                        </flux:button>
                        <flux:button wire:click="saveBlockEdit" size="sm">
                            Save Changes
                        </flux:button>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Block Selector Modal -->
    <flux:modal :open="$showBlockSelector" wire:model="showBlockSelector" class="max-w-4xl">
        <flux:heading>Add a Block</flux:heading>
        <flux:subheading>Choose a block type to add to this section</flux:subheading>

        <div class="mt-6 grid grid-cols-3 gap-4">
            @foreach(App\Enums\LandingPageBlockType::cases() as $blockType)
                <button
                    wire:click="addBlockToSection('{{ $selectedSectionId }}', '{{ $blockType->value }}')"
                    class="flex flex-col items-start p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-left group"
                >
                    <div class="w-8 h-8 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $blockType->label() }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $blockType->description() }}</p>
                </button>
            @endforeach
        </div>
    </flux:modal>

    <!-- Delete Block Modal -->
    <flux:modal :open="$deletingBlockIndex !== null && $selectedSectionId !== null" class="max-w-md">
        <div class="space-y-6">
            <div>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <flux:heading size="lg" class="text-center">Delete Block?</flux:heading>
                <flux:subheading class="text-center mt-2">
                    This action cannot be undone. The block and all its content will be permanently removed.
                </flux:subheading>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button variant="ghost" wire:click="cancelDeleteBlock">
                    Cancel
                </flux:button>
                <flux:button variant="danger" wire:click="removeBlock">
                    Delete Block
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Delete Section Modal -->
    <flux:modal name="delete-section-modal" class="max-w-md">
        <div class="space-y-6">
            <div>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <flux:heading size="lg" class="text-center">Delete Section?</flux:heading>
                <flux:subheading class="text-center mt-2">
                    This action cannot be undone. The section and all its blocks will be permanently removed.
                </flux:subheading>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button variant="ghost" x-on:click="$flux.modal('delete-section-modal').close()">
                    Cancel
                </flux:button>
                <flux:button variant="danger" wire:click="confirmRemoveSection">
                    Delete Section
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Delete Block Modal -->
    <flux:modal name="delete-block-modal" class="max-w-md">
        <div class="space-y-6">
            <div>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <flux:heading size="lg" class="text-center">Delete Block?</flux:heading>
                <flux:subheading class="text-center mt-2">
                    This action cannot be undone. The block will be permanently removed from this section.
                </flux:subheading>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button variant="ghost" x-on:click="$flux.modal('delete-block-modal').close()">
                    Cancel
                </flux:button>
                <flux:button variant="danger" wire:click="confirmRemoveBlock">
                    Delete Block
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Two Step Optin Block Selector Modal -->
    <flux:modal :open="$editingTwoStepOptin === 'selector'" wire:model="editingTwoStepOptin" class="max-w-4xl">
        <flux:heading>Add Block to Two Step Optin</flux:heading>
        <flux:subheading>Choose a block type to add to the two step optin modal</flux:subheading>

        <div class="mt-6 grid grid-cols-3 gap-4">
            @foreach(App\Enums\LandingPageBlockType::cases() as $blockType)
                <button
                    wire:click="addTwoStepOptinBlock('{{ $blockType->value }}'); $set('editingTwoStepOptin', false)"
                    class="flex flex-col items-start p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-left group"
                >
                    <div class="w-8 h-8 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $blockType->label() }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $blockType->description() }}</p>
                </button>
            @endforeach
        </div>
    </flux:modal>

    <!-- Exit Popup Block Selector Modal -->
    <flux:modal :open="$editingExitPopup === 'selector'" wire:model="editingExitPopup" class="max-w-4xl">
        <flux:heading>Add Block to Exit Popup</flux:heading>
        <flux:subheading>Choose a block type to add to the exit popup</flux:subheading>

        <div class="mt-6 grid grid-cols-3 gap-4">
            @foreach(App\Enums\LandingPageBlockType::cases() as $blockType)
                <button
                    wire:click="addExitPopupBlock('{{ $blockType->value }}'); $set('editingExitPopup', false)"
                    class="flex flex-col items-start p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-left group"
                >
                    <div class="w-8 h-8 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $blockType->label() }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $blockType->description() }}</p>
                </button>
            @endforeach
        </div>
    </flux:modal>

    <!-- Two Step Optin Block Editor -->
    @if($editingTwoStepOptin === true && $editingTwoStepOptinBlockIndex !== null)
        @php
            $editingBlock = $twoStepOptinBlocks[$editingTwoStepOptinBlockIndex] ?? null;
        @endphp

        @if($editingBlock)
            @php
                $blockType = App\Enums\LandingPageBlockType::from($editingBlock['type']);
            @endphp
            <flux:modal :open="true" wire:model="editingTwoStepOptin" class="max-w-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <flux:heading>Edit {{ $blockType->label() }}</flux:heading>
                        <flux:subheading>Customize block settings for two step optin</flux:subheading>
                    </div>
                </div>

                <div class="space-y-4">
                    @include('livewire.admin.landing-pages.partials.block-editors.' . $blockType->value, ['data' => 'twoStepOptinBlockData'])
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <flux:button variant="ghost" wire:click="cancelTwoStepOptinBlockEdit">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="saveTwoStepOptinBlock">
                        Save Changes
                    </flux:button>
                </div>
            </flux:modal>
        @endif
    @endif

    <!-- Exit Popup Block Editor -->
    @if($editingExitPopup === true && $editingExitPopupBlockIndex !== null)
        @php
            $editingBlock = $exitPopupBlocks[$editingExitPopupBlockIndex] ?? null;
        @endphp

        @if($editingBlock)
            @php
                $blockType = App\Enums\LandingPageBlockType::from($editingBlock['type']);
            @endphp
            <flux:modal :open="true" wire:model="editingExitPopup" class="max-w-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <flux:heading>Edit {{ $blockType->label() }}</flux:heading>
                        <flux:subheading>Customize block settings for exit popup</flux:subheading>
                    </div>
                </div>

                <div class="space-y-4">
                    @include('livewire.admin.landing-pages.partials.block-editors.' . $blockType->value, ['data' => 'exitPopupBlockData'])
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <flux:button variant="ghost" wire:click="cancelExitPopupBlockEdit">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="saveExitPopupBlock">
                        Save Changes
                    </flux:button>
                </div>
            </flux:modal>
        @endif
    @endif

    <!-- Section Settings Modal -->
    <flux:modal name="section-settings" class="max-w-3xl" variant="flyout">
        <flux:heading>Section Settings</flux:heading>
        <flux:subheading>Customize section layout and appearance</flux:subheading>

        <div class="mt-6 space-y-6">
            <!-- Background Settings -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Background</h3>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Background Color</flux:label>
                        <flux:input type="color" wire:model="sectionSettings.background_color" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Background Image URL</flux:label>
                        <flux:input wire:model="sectionSettings.background_image" placeholder="https://example.com/image.jpg" />
                        <flux:description>Leave empty for solid color background</flux:description>
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Image Position</flux:label>
                            <flux:select wire:model="sectionSettings.background_position">
                                <option value="top">Top</option>
                                <option value="center">Center</option>
                                <option value="bottom">Bottom</option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Fixed Background</flux:label>
                            <flux:switch wire:model="sectionSettings.background_fixed" />
                            <flux:description>Parallax effect</flux:description>
                        </flux:field>
                    </div>
                </div>
            </div>

            <!-- Desktop Layout Settings -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Desktop Layout</h3>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Hide on Desktop</flux:label>
                        <flux:switch wire:model="sectionSettings.desktop_hide" />
                    </flux:field>

                    <div>
                        <flux:label class="mb-2">Desktop Padding</flux:label>
                        <div class="grid grid-cols-4 gap-2">
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.desktop_padding_top" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">T</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.desktop_padding_right" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">R</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.desktop_padding_bottom" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">B</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.desktop_padding_left" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">L</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Vertical Alignment</flux:label>
                            <flux:select wire:model="sectionSettings.desktop_vertical_align">
                                <option value="top">Top</option>
                                <option value="center">Center</option>
                                <option value="bottom">Bottom</option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Horizontal Alignment</flux:label>
                            <flux:select wire:model="sectionSettings.desktop_horizontal_align">
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                                <option value="space-around">Space Around</option>
                                <option value="space-between">Space Between</option>
                            </flux:select>
                        </flux:field>
                    </div>
                </div>
            </div>

            <!-- Mobile Layout Settings -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Mobile Layout</h3>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Hide on Mobile</flux:label>
                        <flux:switch wire:model="sectionSettings.mobile_hide" />
                    </flux:field>

                    <div>
                        <flux:label class="mb-2">Mobile Padding</flux:label>
                        <div class="grid grid-cols-4 gap-2">
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.mobile_padding_top" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">T</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.mobile_padding_right" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">R</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.mobile_padding_bottom" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">B</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                            <flux:field>
                                <flux:input type="number" wire:model="sectionSettings.mobile_padding_left" min="0">
                                    <x-slot:suffix>
                                        <span class="text-xs">L</span>
                                    </x-slot:suffix>
                                </flux:input>
                            </flux:field>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex gap-3 justify-end">
            <flux:button variant="ghost" wire:click="cancelSectionSettings">
                Cancel
            </flux:button>
            <flux:button wire:click="saveSectionSettings">
                Save Settings
            </flux:button>
        </div>
    </flux:modal>
</div>
