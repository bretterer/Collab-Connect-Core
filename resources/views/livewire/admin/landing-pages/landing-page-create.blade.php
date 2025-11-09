<div class="h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
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
        </div>

        <!-- Center - Preview -->
        <div class="flex-1 bg-gray-100 dark:bg-gray-950 overflow-auto">
            <div class="h-full">
                @if(count($sections) > 0)
                    <div class="bg-white dark:bg-gray-900">
                        @foreach($sections as $section)
                            @php
                                $settings = $section['settings'];
                                $bgType = $settings['background_type'] ?? 'color';
                                $bgColor = $settings['background_color'] ?? '#ffffff';
                                $paddingTop = $settings['padding_top'] ?? 0;
                                $paddingBottom = $settings['padding_bottom'] ?? 0;
                                $maxWidth = $settings['max_width'] ?? 'full';
                            @endphp

                            <section
                                style="background-color: {{ $bgColor }}; padding-top: {{ $paddingTop }}px; padding-bottom: {{ $paddingBottom }}px;"
                                class="relative group"
                            >
                                <!-- Section overlay when selected -->
                                @if($selectedSectionId === $section['id'])
                                    <div class="absolute inset-0 border-2 border-blue-500 pointer-events-none"></div>
                                @endif

                                <div class="{{ $maxWidth === 'container' ? 'max-w-7xl mx-auto px-4' : 'w-full' }}">
                                    @foreach($section['blocks'] as $block)
                                        @include('landing-pages.blocks.' . $block['type'], ['data' => $block['data']])
                                    @endforeach
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
</div>
