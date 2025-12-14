<flux:card class="p-0 overflow-hidden">
    <flux:accordion transition>
        <flux:accordion.item>
            <flux:accordion.heading class="py-4 px-4">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <flux:icon name="user-circle" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="font-semibold text-gray-900 dark:text-white">Header</span>
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
                    description="Customize your header with the Elite plan."
                    overlay-style="blur"
                >
                    <div class="space-y-6 p-4 pt-0">
                        {{-- Profile Picture --}}
                        <div>
                            <flux:label class="mb-3">Profile Picture</flux:label>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                    @if(auth()->user()?->influencer?->getProfileImageUrl())
                                        <img src="{{ auth()->user()->influencer->getProfileImageUrl() }}" alt="Profile" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <flux:icon name="user" class="w-8 h-8 text-gray-400" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Shape --}}
                        <div>
                            <flux:label class="mb-3">Shape</flux:label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    wire:click="$set('profilePictureShape', 'round')"
                                    class="flex items-center gap-3 px-4 py-3 border rounded-lg transition-all {{ $profilePictureShape === 'round' ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                >
                                    <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                    <span>Round</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('profilePictureShape', 'square')"
                                    class="flex items-center gap-3 px-4 py-3 border rounded-lg transition-all {{ $profilePictureShape === 'square' ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                >
                                    <div class="w-8 h-8 rounded bg-gray-300 dark:bg-gray-600"></div>
                                    <span>Square</span>
                                </button>
                            </div>
                        </div>

                        {{-- Display Name --}}
                        <flux:input wire:model.live.debounce.500ms="displayName" label="Display Name" placeholder="Your name" />

                        {{-- Text Size --}}
                        <div>
                            <flux:label class="mb-3">Text Size</flux:label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['large' => 'Large', 'medium' => 'Medium', 'small' => 'Small'] as $value => $label)
                                    <button
                                        type="button"
                                        wire:click="$set('displayNameSize', '{{ $value }}')"
                                        class="px-4 py-2 border rounded-lg text-sm transition-all {{ $displayNameSize === $value ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                    >
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Location --}}
                        <flux:input wire:model.live.debounce.500ms="location" label="Location" placeholder="City, State" />

                        {{-- Bio --}}
                        <flux:textarea wire:model.live.debounce.500ms="bio" label="Bio" placeholder="Example: Fashion creator" rows="3" />

                        {{-- Header Format --}}
                        <div>
                            <flux:label class="mb-3">Header Format</flux:label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    wire:click="$set('headerFormat', 'vertical')"
                                    class="flex flex-col items-center gap-2 px-4 py-4 border rounded-lg transition-all {{ $headerFormat === 'vertical' ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                >
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                        <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                        <div class="flex gap-1">
                                            <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm">Vertical</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('headerFormat', 'card')"
                                    class="flex flex-col items-center gap-2 px-4 py-4 border rounded-lg transition-all {{ $headerFormat === 'card' ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                >
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                        <div class="flex flex-col gap-1">
                                            <div class="w-8 h-1 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                            <div class="flex gap-1">
                                                <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                                <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-sm">Card</span>
                                </button>
                            </div>
                        </div>

                        {{-- Other Options --}}
                        <div>
                            <flux:label class="mb-3">Other</flux:label>
                            <flux:switch wire:model.live="showShareButton" label="Show Share Button" description="Allow visitors to share your page." align="left" />
                        </div>
                    </div>
                </x-tier-locked>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>
