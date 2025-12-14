<div
    x-data="{ sortableWidth: 0 }"
    x-init="
        $nextTick(() => {
            const container = $el.querySelector('[wire\\:sortable]');
            if (container) {
                // Set initial width
                sortableWidth = container.offsetWidth;
                document.documentElement.style.setProperty('--sortable-width', sortableWidth + 'px');

                // Update on resize
                new ResizeObserver(() => {
                    sortableWidth = container.offsetWidth;
                    document.documentElement.style.setProperty('--sortable-width', sortableWidth + 'px');
                }).observe(container);
            }
        });
    "
>
    <style>
        .draggable-mirror {
            width: var(--sortable-width) !important;
        }
        .draggable-source--is-dragging {
            opacity: 0.4;
        }
    </style>

    <flux:card class="p-0 overflow-hidden">
    <flux:accordion transition>
        <flux:accordion.item>
            <flux:accordion.heading class="py-4 px-4">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <flux:icon name="link" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="font-semibold text-gray-900 dark:text-white">Links</span>
                    </div>
                    <flux:switch wire:model.live="enabled" wire:click.stop />
                </div>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div class="space-y-4 p-4 pt-0">
                    {{-- Links List --}}
                    <div class="space-y-2" wire:sortable="sortItems">
                        @foreach($items as $index => $link)
                            <div
                                class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800"
                                wire:key="link-{{ $index }}"
                                wire:sortable.item="{{ $index }}"
                            >
                                <div wire:sortable.handle class="cursor-grab active:cursor-grabbing">
                                    <flux:icon name="bars-3" class="w-5 h-5 text-gray-400" />
                                </div>
                                <span class="flex-1 truncate">{{ $link['title'] ?: 'Untitled Link' }}</span>
                                <div class="flex items-center gap-2">
                                    <flux:switch wire:model.live="items.{{ $index }}.enabled" />
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                        <flux:menu>
                                            <flux:menu.item icon="pencil" wire:click="editLink({{ $index }})">Edit</flux:menu.item>
                                            <flux:menu.item icon="trash" variant="danger" wire:click="removeLink({{ $index }})">Delete</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Link Limit Indicator --}}
                    @if($this->linkLimit < PHP_INT_MAX)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ count($items) }} / {{ $this->linkLimit }} links used
                            </span>
                            @if(!$this->canAddMoreLinks)
                                <x-upgrade-badge tier="elite" size="xs" />
                            @endif
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div
                                class="h-1.5 rounded-full transition-all duration-300 {{ count($items) >= $this->linkLimit ? 'bg-amber-500' : 'bg-purple-600' }}"
                                style="width: {{ min(100, (count($items) / $this->linkLimit) * 100) }}%"
                            ></div>
                        </div>
                    @endif

                    @if($this->canAddMoreLinks)
                        <flux:button variant="primary" class="w-full" wire:click="addLink">
                            Add Link
                        </flux:button>
                    @else
                        <flux:button variant="primary" class="w-full" disabled icon="lock-closed">
                            Upgrade to Add More Links
                        </flux:button>
                    @endif

                    {{-- Settings --}}
                    <flux:separator />

                    <flux:input wire:model.live="title" label="Title" placeholder="My Links" />
                    <flux:input wire:model.live="subtitle" label="Subtitle" placeholder="Check out my links below" />

                    {{-- Customization Options - Locked for Professional tier --}}
                    <x-tier-locked
                        :locked="!$this->hasCustomizationAccess"
                        :required-tier="$this->requiredTierForCustomization"
                        :current-tier="$this->currentTier"
                        title="Elite Feature"
                        description="Customize your link layout and style with the Elite plan."
                        overlay-style="blur"
                    >
                        {{-- Layout --}}
                        <div>
                            <flux:label class="mb-3">Layout</flux:label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    wire:click="$set('layout', 'classic')"
                                    class="flex items-center gap-3 px-4 py-3 border rounded-lg transition-all {{ $layout === 'classic' ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                >
                                    <flux:icon name="bars-3" class="w-5 h-5" />
                                    <span>Classic</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('layout', 'grid')"
                                    class="flex items-center gap-3 px-4 py-3 border rounded-lg transition-all {{ $layout === 'grid' ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                >
                                    <flux:icon name="squares-2x2" class="w-5 h-5" />
                                    <span>Grid</span>
                                </button>
                            </div>
                        </div>

                        {{-- Block Size --}}
                        <div class="mt-4">
                            <flux:label class="mb-3">Block Size</flux:label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['large' => 'Large', 'medium' => 'Medium', 'small' => 'Small'] as $value => $label)
                                    <button
                                        type="button"
                                        wire:click="$set('size', '{{ $value }}')"
                                        class="px-4 py-2 border rounded-lg text-sm transition-all {{ $size === $value ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                    >
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Link Options --}}
                        <div class="space-y-3 mt-4">
                            <flux:switch wire:model.live="shadow" label="Link Shadow" align="left" />
                            <flux:separator variant="subtle" />
                            <flux:switch wire:model.live="outline" label="Link Outline" align="left" />
                        </div>
                    </x-tier-locked>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
    </flux:card>

    {{-- Edit Link Modal --}}
    <flux:modal name="edit-link-modal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading>Edit Link</flux:heading>
                <flux:text class="mt-2">Update your link details below.</flux:text>
            </div>

            <div class="space-y-4">
                <flux:input
                    wire:model="linkForm.title"
                    label="Title"
                    placeholder="My Link"
                />

                <flux:input
                    wire:model="linkForm.url"
                    label="URL"
                    type="url"
                    placeholder="https://example.com"
                />

                <flux:select wire:model="linkForm.icon" label="Icon" placeholder="Select an icon...">
                    <flux:select.option value="">Default Link</flux:select.option>
                    <flux:select.option value="instagram">Instagram</flux:select.option>
                    <flux:select.option value="tiktok">TikTok</flux:select.option>
                    <flux:select.option value="youtube">YouTube</flux:select.option>
                    <flux:select.option value="twitter">X (Twitter)</flux:select.option>
                    <flux:select.option value="facebook">Facebook</flux:select.option>
                </flux:select>
            </div>

            <div class="flex gap-2">
                <flux:button variant="ghost" class="flex-1" wire:click="cancelLinkEdit">
                    Cancel
                </flux:button>
                <flux:button variant="primary" class="flex-1" wire:click="saveLinkEdit">
                    Save
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
