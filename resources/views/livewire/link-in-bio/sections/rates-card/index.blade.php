<flux:card class="p-0 overflow-hidden">
    <flux:accordion transition>
        <flux:accordion.item>
            <flux:accordion.heading class="py-4 px-4">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <flux:icon name="currency-dollar" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="font-semibold text-gray-900 dark:text-white">Rates Card</span>
                    </div>
                    @if($this->hasCustomizationAccess)
                        <flux:switch wire:model.live="enabled" wire:click.stop />
                    @else
                        <x-upgrade-badge tier="{{ $this->requiredTierForCustomization }}" size="xs" />
                    @endif
                </div>
            </flux:accordion.heading>
            <flux:accordion.content>
                <x-tier-locked
                    :locked="!$this->hasCustomizationAccess"
                    :required-tier="$this->requiredTierForCustomization"
                    title="Elite Feature"
                    description="Customize your rates card with the Elite plan."
                    overlay-style="blur"
                >
                    <div class="space-y-4 p-4 pt-0">
                        {{-- Rates List --}}
                        <div class="space-y-2">
                            @foreach($items as $index => $rate)
                                <div class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg" wire:key="rate-{{ $index }}">
                                    <span class="flex-1 truncate">{{ $rate['platform'] ?: 'Untitled' }} - {{ $rate['rate'] ?: '$0' }}</span>
                                    <flux:switch wire:model.live="items.{{ $index }}.enabled" />
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                        <flux:menu>
                                            <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                            <flux:menu.item icon="trash" variant="danger" wire:click="removeRate({{ $index }})">Delete</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            @endforeach
                        </div>

                        <flux:button variant="primary" class="w-full" wire:click="addRate">Add Rate</flux:button>

                        {{-- Settings --}}
                        <flux:separator />

                        <flux:input wire:model.live="title" label="Title" placeholder="My Rates" />
                        <flux:input wire:model.live="subtitle" label="Subtitle" placeholder="Check out my rates below" />

                        {{-- Size --}}
                        <div>
                            <flux:label class="mb-3">Size</flux:label>
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
                    </div>
                </x-tier-locked>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>
