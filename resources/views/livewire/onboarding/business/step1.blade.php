<!-- Step 1: Business Information -->
<div>
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Business Information</h2>

    <div class="space-y-4">
        <!-- Business Name -->
        <flux:input
            type="text"
            wire:model="businessName"
            label="Business Name"
            placeholder="Enter your business name"
            required />

        <!-- Primary Industry -->
        <flux:field>
            <flux:label>Primary Industry</flux:label>
            <flux:select wire:model="industry"
                         variant="listbox"
                         placeholder="Select your primary industry"
                         required>
                @foreach ($nicheOptions as $niche)
                    <flux:select.option value="{{ $niche->value }}">{{ $niche->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="industry" />
        </flux:field>

        <!-- Websites -->
        <div>
            <flux:label>Business Website or Social Media (Optional)</flux:label>
            @foreach($websites as $index => $website)
                <div class="flex items-center space-x-2 mt-2">
                    <flux:input
                        type="url"
                        wire:model="websites.{{ $index }}"
                        placeholder="https://example.com"
                        class="flex-1" />
                    @if(count($websites) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeWebsite({{ $index }})">
                            Remove
                        </flux:button>
                    @endif
                </div>
            @endforeach
            <flux:button
                type="button"
                variant="ghost"
                size="sm"
                wire:click="addWebsite"
                class="mt-2">
                + Add Another Website
            </flux:button>
        </div>

        <!-- Primary Zip Code -->
        <flux:input
            type="text"
            wire:model="primaryZipCode"
            label="Primary Zip Code or Service Area"
            placeholder="Enter zip code"
            required />

        <!-- Location Count -->
        <flux:input
            type="number"
            wire:model.live="locationCount"
            label="How many locations does your business have?"
            min="1"
            required />

        @if($locationCount > 1)
            <div class="space-y-3">
                <flux:checkbox
                    wire:model="isFranchise"
                    label="This is a franchise" />

                @if($locationCount >= 30)
                    <flux:checkbox
                        wire:model="isNationalBrand"
                        label="This is a national brand (30+ locations)" />
                @endif
            </div>
        @endif
    </div>
</div>