<!-- Step 5: Compensation & Timeline -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">5</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Compensation & Timeline
        </flux:heading>
    </div>

    <!-- Compensation Types -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Preferred Compensation Types
        </flux:heading>
        <flux:description>
            Select up to 3 compensation types you prefer. Being flexible can increase your opportunities.
        </flux:description>

        <flux:checkbox.group wire:model="compensationTypes" variant="buttons" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach(\App\Enums\CompensationType::cases() as $compensationType)
                <flux:checkbox
                    value="{{ $compensationType->value }}"
                    label="{{ $compensationType->label() }}"
                    description="{{ $compensationType->description() }}"
                    class="w-full"
                />
            @endforeach
        </flux:checkbox.group>
        <flux:error name="compensationTypes" />
        
        @if(count($compensationTypes) >= 3)
            <flux:description class="text-amber-600 dark:text-amber-400">
                You've selected the maximum of 3 compensation types. Uncheck one to select another.
            </flux:description>
        @endif
    </div>

    <!-- Lead Time -->
    <div class="space-y-4 mt-8">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Typical Lead Time
        </flux:heading>
        <flux:description>
            How many days do you typically need to prepare and create content for a campaign?
        </flux:description>

        <flux:field class="max-w-md">
            <flux:label>Days needed for project preparation</flux:label>
            <flux:input
                type="number"
                wire:model="typicalLeadTimeDays"
                placeholder="7"
                min="1"
                max="365"
            />
            <flux:error name="typicalLeadTimeDays" />
            <flux:description>
                This helps businesses plan their campaign timelines effectively
            </flux:description>
        </flux:field>
    </div>
</div>