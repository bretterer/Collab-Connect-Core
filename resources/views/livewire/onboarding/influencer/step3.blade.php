<!-- Step 3: Content & Business Preferences -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">3</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Content & Business Preferences
        </flux:heading>
    </div>

    <!-- Content Types -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            What type of content do you create?
        </flux:heading>
        <flux:description>
            Select up to 3 content types that best describe your expertise. This helps businesses find the right match.
        </flux:description>

        <flux:checkbox.group wire:model="contentTypes" variant="buttons" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach(\App\Enums\BusinessIndustry::cases() as $industry)
                <flux:checkbox
                    value="{{ $industry->value }}"
                    label="{{ $industry->label() }}"
                    class="w-full"
                />
            @endforeach
        </flux:checkbox.group>
        <flux:error name="contentTypes" />
        
        @if(count($contentTypes) >= 3)
            <flux:description class="text-amber-600 dark:text-amber-400">
                You've selected the maximum of 3 content types. Uncheck one to select another.
            </flux:description>
        @endif
    </div>

    <!-- Business Types -->
    <div class="space-y-4 mt-8">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            What type of companies do you want to work with?
        </flux:heading>
        <flux:description>
            Select up to 2 business types you're most interested in collaborating with.
        </flux:description>

        <flux:checkbox.group wire:model="preferredBusinessTypes" variant="buttons" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach(\App\Enums\BusinessType::cases() as $businessType)
                <flux:checkbox
                    value="{{ $businessType->value }}"
                    label="{{ $businessType->label() }}"
                    description="{{ $businessType->description() }}"
                    class="w-full"
                />
            @endforeach
        </flux:checkbox.group>
        <flux:error name="preferredBusinessTypes" />
        
        @if(count($preferredBusinessTypes) >= 2)
            <flux:description class="text-amber-600 dark:text-amber-400">
                You've selected the maximum of 2 business types. Uncheck one to select another.
            </flux:description>
        @endif
    </div>
</div>