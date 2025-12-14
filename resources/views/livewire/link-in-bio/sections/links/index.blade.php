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
                    <div class="space-y-2">
                        @foreach($items as $index => $link)
                            <div
                                class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800"
                                wire:key="link-{{ $index }}"
                            >
                                <flux:icon name="bars-3" class="w-5 h-5 text-gray-400 cursor-grab" />
                                <span class="flex-1 truncate">{{ $link['title'] ?: 'Untitled Link' }}</span>
                                <flux:switch wire:model.live="items.{{ $index }}.enabled" />
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:click="editLink({{ $index }})">Edit</flux:menu.item>
                                        <flux:menu.item icon="trash" variant="danger" wire:click="removeLink({{ $index }})">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        @endforeach
                    </div>

                    <flux:button variant="primary" class="w-full" wire:click="addLink">Add Link</flux:button>

                    {{-- Settings --}}
                    <flux:separator />

                    <flux:input wire:model.live="title" label="Title" placeholder="My Links" />
                    <flux:input wire:model.live="subtitle" label="Subtitle" placeholder="Check out my links below" />

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
                    <div>
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
                    <div class="space-y-3">
                        <flux:switch wire:model.live="shadow" label="Link Shadow" align="left" />
                        <flux:separator variant="subtle" />
                        <flux:switch wire:model.live="outline" label="Link Outline" align="left" />
                    </div>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>

</flux:card>
