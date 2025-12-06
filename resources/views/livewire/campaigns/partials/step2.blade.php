{{-- Step 2: Brand & Briefing --}}
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Brand & Briefing</flux:heading>

        {{-- Skip Step Button --}}
        @if($canSkipBrandStep && !empty($brandOverview))
            <flux:button variant="ghost" wire:click="skipStep" icon-trailing="forward">
                Skip (using saved defaults)
            </flux:button>
        @endif
    </div>

    {{-- Required Briefing Fields --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Left Column --}}
        <div class="space-y-6">
            {{-- Campaign Objective --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-1.5 bg-blue-100 dark:bg-blue-500/20 rounded-lg">
                        <flux:icon.flag class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <flux:label class="!mb-0">Campaign Objective</flux:label>
                </div>
                <flux:editor
                    wire:model="campaignObjective"
                    placeholder="What is the main objective for creators? e.g., Show how our product makes any moment better" />
                <flux:error name="campaignObjective" />
            </div>

            {{-- Key Insights --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-1.5 bg-green-100 dark:bg-green-500/20 rounded-lg">
                        <flux:icon.light-bulb class="w-4 h-4 text-green-600 dark:text-green-400" />
                    </div>
                    <flux:label class="!mb-0">Key Insights</flux:label>
                </div>
                <flux:editor
                    wire:model="keyInsights"
                    placeholder="What are the key insights about your target audience or market?" />
                <flux:error name="keyInsights" />
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Fan Motivator --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-1.5 bg-purple-100 dark:bg-purple-500/20 rounded-lg">
                        <flux:icon.heart class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <flux:label class="!mb-0">Fan Motivator</flux:label>
                </div>
                <flux:editor
                    wire:model="fanMotivator"
                    placeholder="What motivates your fans/customers to engage with your brand?" />
                <flux:error name="fanMotivator" />
            </div>

            {{-- Creative Connection --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-1.5 bg-amber-100 dark:bg-amber-500/20 rounded-lg">
                        <flux:icon.sparkles class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <flux:label class="!mb-0">Creative Connection (Optional)</flux:label>
                </div>
                <flux:editor
                    wire:model="creativeConnection"
                    placeholder="How should creators connect your brand to the audience?" />
            </div>
        </div>
    </div>

    <flux:separator />

    {{-- Optional Brand Details --}}
    <flux:accordion class="border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
        <flux:accordion.item>
            <flux:accordion.heading class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-zinc-200 dark:bg-zinc-600 rounded-lg">
                        <flux:icon.building-storefront class="w-4 h-4 text-zinc-600 dark:text-zinc-300" />
                    </div>
                    <div>
                        <span class="font-medium text-zinc-700 dark:text-zinc-300">Brand Details (Optional)</span>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            @if($hasAppliedDefaults)
                                Pre-filled from <a href="{{ route('business.settings', ['tab' => 'campaigns']) }}" class="text-blue-600 dark:text-blue-400 hover:underline" wire:navigate>Business Settings</a>
                            @else
                                Add your brand overview, story, and current marketing
                            @endif
                        </p>
                    </div>
                </div>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div class="p-4 space-y-4">
                    <div>
                        <flux:label class="text-sm">Brand Overview</flux:label>
                        <flux:editor
                            wire:model="brandOverview"
                            placeholder="Brief overview of your brand..." />
                    </div>
                    <div>
                        <flux:label class="text-sm">Brand Story</flux:label>
                        <flux:editor
                            wire:model="brandStory"
                            placeholder="Your brand's story..." />
                    </div>
                    <div>
                        <flux:label class="text-sm">Current Advertising</flux:label>
                        <flux:editor
                            wire:model="currentAdvertisingCampaign"
                            placeholder="Current campaigns..." />
                    </div>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>

    {{-- Content Guidelines --}}
    <flux:accordion class="border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
        <flux:accordion.item>
            <flux:accordion.heading class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-zinc-200 dark:bg-zinc-600 rounded-lg">
                        <flux:icon.clipboard-document-list class="w-4 h-4 text-zinc-600 dark:text-zinc-300" />
                    </div>
                    <div>
                        <span class="font-medium text-zinc-700 dark:text-zinc-300">Content Guidelines (Optional)</span>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Products to feature, restrictions, and additional notes</p>
                    </div>
                </div>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div class="p-4 space-y-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-cyan-500 rounded-full"></div>
                            <flux:label class="!mb-0 text-sm">Specific Products</flux:label>
                        </div>
                        <flux:editor
                            wire:model="specificProducts"
                            placeholder="Products to feature..." />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <flux:label class="!mb-0 text-sm">Posting Restrictions</flux:label>
                        </div>
                        <flux:editor
                            wire:model="postingRestrictions"
                            placeholder="Topics or content to avoid..." />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                            <flux:label class="!mb-0 text-sm">Additional Notes</flux:label>
                        </div>
                        <flux:editor
                            wire:model="additionalConsiderations"
                            placeholder="Any other considerations..." />
                    </div>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
