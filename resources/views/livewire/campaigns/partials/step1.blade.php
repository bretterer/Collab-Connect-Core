{{-- Step 1: Campaign Goal & Type --}}
<div class="space-y-8">
    <flux:heading size="lg">Campaign Goal & Type</flux:heading>

    {{-- Top Section: Project Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Project Name --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1.5 bg-blue-100 dark:bg-blue-500/20 rounded-lg">
                    <flux:icon.folder class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:label class="!mb-0">Project Name (Optional)</flux:label>
            </div>
            <flux:input
                type="text"
                wire:model="projectName"
                placeholder="e.g., Summer Coffee Campaign" />
        </div>

        {{-- Target Location --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1.5 bg-green-100 dark:bg-green-500/20 rounded-lg">
                    <flux:icon.map-pin class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <flux:label class="!mb-0">Target Zip Code</flux:label>
            </div>
            <flux:input
                type="text"
                wire:model="targetZipCode"
                placeholder="45066" />
            <flux:error name="targetZipCode" />
        </div>
    </div>

    <flux:separator />

    {{-- Campaign Goal --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <div class="p-1.5 bg-purple-100 dark:bg-purple-500/20 rounded-lg">
                <flux:icon.rocket-launch class="w-4 h-4 text-purple-600 dark:text-purple-400" />
            </div>
            <flux:label class="!mb-0">I'm a business and I want to...</flux:label>
        </div>
        <flux:input
            type="text"
            wire:model="campaignGoal"
            placeholder="e.g., increase brand awareness, promote new product launch, drive foot traffic" />
        <flux:error name="campaignGoal" />
    </div>

    <flux:separator />

    {{-- Campaign Types --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <div class="p-1.5 bg-amber-100 dark:bg-amber-500/20 rounded-lg">
                <flux:icon.tag class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            </div>
            <flux:label class="!mb-0">Campaign Type (Select all that apply)</flux:label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            @foreach($this->getCampaignTypeOptions() as $type)
                <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer border transition-colors
                    {{ collect($campaignType ?? [])->contains(function($item) use ($type) { return is_object($item) ? $item->value === $type['value'] : $item === $type['value']; })
                        ? 'border-amber-500 bg-amber-100 dark:bg-amber-500/20'
                        : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                    <flux:checkbox wire:model.live="campaignType" value="{{ $type['value'] }}" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-200">{{ $type['label'] }}</span>
                </label>
            @endforeach
        </div>
        <flux:error name="campaignType" />
    </div>
</div>
