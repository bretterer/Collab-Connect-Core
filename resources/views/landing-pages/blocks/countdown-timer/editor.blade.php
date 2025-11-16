<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        {{-- Target Date/Time --}}
        <flux:field>
            <flux:label>Target Date & Time</flux:label>
            <flux:description>The date and time to countdown to (uses browser's timezone)</flux:description>
            <flux:input
                type="datetime-local"
                wire:model="{{ $propertyPrefix }}.target_datetime"
                placeholder="Select date and time"
            />
        </flux:field>

        {{-- Number Color --}}
        <flux:field>
            <flux:label>Number Color</flux:label>
            <flux:description>The color of the countdown numbers</flux:description>
            <flux:input type="color" wire:model="{{ $propertyPrefix }}.number_color" />
        </flux:field>

        {{-- Label Color --}}
        <flux:field>
            <flux:label>Label Color</flux:label>
            <flux:description>The color of the labels (Days, Hours, Minutes, Seconds)</flux:description>
            <flux:input type="color" wire:model="{{ $propertyPrefix }}.label_color" />
        </flux:field>

        {{-- Background Color --}}
        <flux:field>
            <flux:label>Background Color</flux:label>
            <flux:description>The background color of the countdown timer</flux:description>
            <flux:input type="color" wire:model="{{ $propertyPrefix }}.background_color" />
        </flux:field>

        {{-- Custom Labels --}}
        <div class="grid grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Days Label</flux:label>
                <flux:input wire:model="{{ $propertyPrefix }}.label_days" placeholder="Days" />
            </flux:field>

            <flux:field>
                <flux:label>Hours Label</flux:label>
                <flux:input wire:model="{{ $propertyPrefix }}.label_hours" placeholder="Hours" />
            </flux:field>

            <flux:field>
                <flux:label>Minutes Label</flux:label>
                <flux:input wire:model="{{ $propertyPrefix }}.label_minutes" placeholder="Minutes" />
            </flux:field>

            <flux:field>
                <flux:label>Seconds Label</flux:label>
                <flux:input wire:model="{{ $propertyPrefix }}.label_seconds" placeholder="Seconds" />
            </flux:field>
        </div>

        {{-- Remove on Completion --}}
        <flux:field>
            <flux:label>
                <flux:checkbox wire:model="{{ $propertyPrefix }}.remove_on_completion" />
                Remove on Completion
            </flux:label>
            <flux:description>Hide the countdown when it reaches zero. If unchecked, it will count up from zero.</flux:description>
        </flux:field>
    </div>
</x-landing-page-block.editor>
