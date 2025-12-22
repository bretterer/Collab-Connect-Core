<div>
@if($this->canShowSection)
<flux:card class="p-0 overflow-hidden">
    <flux:accordion transition>
        <flux:accordion.item>
            <flux:accordion.heading class="py-4 px-4">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <flux:icon name="user-plus" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="font-semibold text-gray-900 dark:text-white">Join Referral</span>
                    </div>
                    @if($this->hasEliteAccess)
                        <flux:switch wire:model.live="enabled" wire:click.stop />
                    @else
                        <x-upgrade-badge tier="{{ $this->requiredTierForAccess }}" size="xs" />
                    @endif
                </div>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div class="space-y-4 p-4 pt-0">
                    {{-- Info about referral program --}}
                    <flux:callout color="sky" icon="information-circle">
                        <flux:callout.heading>Earn Commissions</flux:callout.heading>
                        <flux:callout.text>
                            When visitors sign up using your referral link, you earn a commission on their subscription payments.
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Button Customization (Elite Feature) --}}
                    <x-tier-locked
                        :locked="!$this->hasEliteAccess"
                        :required-tier="$this->requiredTierForAccess"
                        title="Elite Feature"
                        description="Enable the Join CollabConnect button with the Elite plan."
                        overlay-style="blur"
                    >
                        <div class="space-y-4 p-4 -mx-4">
                            {{-- Button Text --}}
                            <flux:input
                                wire:model.live.debounce.500ms="text"
                                label="Button Text"
                                placeholder="Join CollabConnect"
                            />

                            {{-- Button Style --}}
                            <div>
                                <flux:label class="mb-3">Button Style</flux:label>
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach(['primary' => 'Solid', 'secondary' => 'Subtle', 'outline' => 'Outline'] as $value => $label)
                                        <button
                                            type="button"
                                            wire:click="$set('style', '{{ $value }}')"
                                            class="px-4 py-2 border rounded-lg text-sm transition-all {{ $style === $value ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                        >
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Button Color (only for Solid style) --}}
                            @if($style === 'primary')
                                <div>
                                    <flux:label class="mb-3">Button Color</flux:label>
                                    <div class="flex gap-2 flex-wrap">
                                        @foreach(['#000000', '#dc2626', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6', '#ec4899'] as $color)
                                            <button
                                                type="button"
                                                wire:click="$set('buttonColor', '{{ $color }}')"
                                                class="w-10 h-10 rounded-full border-2 transition-all {{ $buttonColor === $color ? 'border-gray-900 dark:border-white ring-2 ring-offset-2 ring-gray-400' : 'border-gray-200 dark:border-gray-600' }}"
                                                style="background-color: {{ $color }}"
                                            ></button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-tier-locked>

                    {{-- Link Info --}}
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <flux:icon name="arrow-top-right-on-square" class="inline w-4 h-4 mr-1" />
                        Links to your referral signup page (opens in new tab)
                    </div>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>
@endif
</div>
