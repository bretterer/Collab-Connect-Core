{{-- Step 3: Deliverables & Settings --}}
<div class="space-y-8">
    <flux:heading size="lg">Deliverables & Settings</flux:heading>

    {{-- Top Row: Three Sections --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Platforms --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1.5 bg-blue-100 dark:bg-blue-500/20 rounded-lg">
                    <flux:icon.device-phone-mobile class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:label class="!mb-0">Platforms</flux:label>
            </div>
            <div class="space-y-1">
                @foreach($this->getTargetPlatformOptions() as $platform)
                    <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 {{ in_array($platform['value'], $targetPlatforms) ? 'bg-blue-100 dark:bg-blue-500/20' : '' }}">
                        <flux:checkbox wire:model.live="targetPlatforms" value="{{ $platform['value'] }}" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-200">{{ $platform['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Deliverables --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1.5 bg-green-100 dark:bg-green-500/20 rounded-lg">
                    <flux:icon.photo class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <flux:label class="!mb-0">Deliverables</flux:label>
            </div>
            <div class="space-y-1 max-h-48 overflow-y-auto">
                @foreach($this->getDeliverableTypeOptions() as $deliverable)
                    <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 {{ in_array($deliverable['value'], $deliverables) ? 'bg-green-100 dark:bg-green-500/20' : '' }}">
                        <flux:checkbox wire:model.live="deliverables" value="{{ $deliverable['value'] }}" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-200">{{ $deliverable['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Metrics --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1.5 bg-purple-100 dark:bg-purple-500/20 rounded-lg">
                    <flux:icon.chart-bar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                </div>
                <flux:label class="!mb-0">Success Metrics</flux:label>
            </div>
            <div class="space-y-1">
                @foreach($this->getSuccessMetricOptions() as $metric)
                    <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 {{ in_array($metric['value'], $successMetrics) ? 'bg-purple-100 dark:bg-purple-500/20' : '' }}">
                        <flux:checkbox wire:model.live="successMetrics" value="{{ $metric['value'] }}" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-200">{{ $metric['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <flux:separator />

    {{-- Bottom Row: Two Sections --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Compensation --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="p-1.5 bg-amber-100 dark:bg-amber-500/20 rounded-lg">
                    <flux:icon.currency-dollar class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                </div>
                <flux:label class="!mb-0">Compensation</flux:label>
            </div>

            <div class="space-y-4">
                {{-- Compensation Type as icon buttons --}}
                <div class="grid grid-cols-4 gap-2">
                    @php
                        $compIcons = [
                            'monetary' => 'banknotes',
                            'free_product' => 'gift',
                            'discount' => 'receipt-percent',
                            'gift_card' => 'credit-card',
                            'experience' => 'sparkles',
                            'other' => 'ellipsis-horizontal',
                        ];
                    @endphp
                    @foreach($this->getCompensationTypeOptions() as $option)
                        <button type="button"
                                wire:click="$set('compensationType', '{{ $option['value'] }}')"
                                class="flex flex-col items-center gap-1 p-2 rounded-lg border transition-colors {{ $compensationType === $option['value'] ? 'border-amber-500 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300' : 'border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700' }}">
                            <flux:icon :name="$compIcons[$option['value']] ?? 'question-mark-circle'" class="w-5 h-5" />
                            <span class="text-xs truncate w-full text-center">{{ Str::limit($option['label'], 8) }}</span>
                        </button>
                    @endforeach
                </div>
                <flux:error name="compensationType" />

                <flux:field>
                    <flux:label>Details</flux:label>
                    <flux:textarea wire:model="compensationDescription" rows="2" placeholder="Describe your offer..." />
                    <flux:error name="compensationDescription" />
                </flux:field>

                <div class="grid grid-cols-2 gap-3">
                    <flux:field>
                        <flux:label># Influencers</flux:label>
                        <flux:input type="number" wire:model="influencerCount" min="1" max="50" />
                        <flux:error name="influencerCount" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Exclusivity (days)</flux:label>
                        <flux:input type="number" wire:model="exclusivityPeriod" min="0" placeholder="0" />
                    </flux:field>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="p-1.5 bg-rose-100 dark:bg-rose-500/20 rounded-lg">
                    <flux:icon.calendar class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                </div>
                <flux:label class="!mb-0">Timeline</flux:label>
            </div>

            <div class="space-y-4">
                {{-- Visual Mini Timeline --}}
                <div class="flex items-center justify-between px-3 py-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></div>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Deadline</span>
                        <span class="text-xs font-medium text-zinc-700 dark:text-zinc-200">{{ $applicationDeadline ? \Carbon\Carbon::parse($applicationDeadline)->format('n/j') : '—' }}</span>
                    </div>
                    <div class="flex-1 h-px bg-zinc-300 dark:bg-zinc-600 mx-3"></div>
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Start</span>
                        <span class="text-xs font-medium text-zinc-700 dark:text-zinc-200">{{ $campaignStartDate ? \Carbon\Carbon::parse($campaignStartDate)->format('n/j') : '—' }}</span>
                    </div>
                    <div class="flex-1 h-px bg-zinc-300 dark:bg-zinc-600 mx-3"></div>
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 bg-red-500 rounded-full"></div>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">End</span>
                        <span class="text-xs font-medium text-zinc-700 dark:text-zinc-200">{{ $campaignCompletionDate ? \Carbon\Carbon::parse($campaignCompletionDate)->format('n/j') : '—' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <flux:field>
                        <flux:label>Apply By</flux:label>
                        <flux:input type="date" wire:model.live="applicationDeadline" />
                        <flux:error name="applicationDeadline" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Start</flux:label>
                        <flux:input type="date" wire:model.live="campaignStartDate" />
                        <flux:error name="campaignStartDate" />
                    </flux:field>
                    <flux:field>
                        <flux:label>End</flux:label>
                        <flux:input type="date" wire:model.live="campaignCompletionDate" />
                        <flux:error name="campaignCompletionDate" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Additional Notes (Optional)</flux:label>
                    <flux:textarea wire:model="additionalRequirements" rows="2" placeholder="Extra requirements or guidelines..." />
                </flux:field>
            </div>
        </div>
    </div>
</div>
