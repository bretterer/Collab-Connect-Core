<!-- Step 1: Profile Information -->
<div>
    <h2 class="mb-6 text-xl font-semibold text-gray-900 dark:text-white">Profile Information</h2>

    <div class="space-y-4">
        <!-- Creator Name -->
        <flux:field>
            <flux:label>Full Name or Preferred Creator Name</flux:label>
            <flux:input type="text"
                    wire:model="creatorName"
                    placeholder="Enter your name or creator name"
                    required />
            <flux:error name="creatorName" />
        </flux:field>

        <!-- Primary Niche -->
        <flux:field>
            <flux:label>Primary Content Niche/Interest</flux:label>
            <flux:select wire:model="primaryNiche"
                         variant="listbox"
                         placeholder="Select your primary niche"
                         required>
                @foreach ($nicheOptions as $niche)
                    <flux:select.option value="{{ $niche->value }}">{{ $niche->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="primaryNiche" />
        </flux:field>

        <!-- Primary Zip Code -->
        <flux:field>
            <flux:label>Primary Zip Code</flux:label>
            <flux:input type="text"
                        wire:model="primaryZipCode"
                        placeholder="45066"
                        required />
            <flux:error name="primaryZipCode" />
        </flux:field>
    </div>
</div>