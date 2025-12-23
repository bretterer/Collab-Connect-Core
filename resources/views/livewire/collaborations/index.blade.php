<div>
    <div class="mb-6">
        <flux:heading size="xl" class="text-gray-900 dark:text-white">Collaborations</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
            Manage your active and past collaborations
        </flux:text>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6">
        <flux:tabs wire:model="filter">
            <flux:tab name="active" wire:click="setFilter('active')">
                Active
                @if($this->counts['active'] > 0)
                    <flux:badge size="sm" color="green" class="ml-2">{{ $this->counts['active'] }}</flux:badge>
                @endif
            </flux:tab>
            <flux:tab name="completed" wire:click="setFilter('completed')">
                Completed
                @if($this->counts['completed'] > 0)
                    <flux:badge size="sm" color="blue" class="ml-2">{{ $this->counts['completed'] }}</flux:badge>
                @endif
            </flux:tab>
            <flux:tab name="cancelled" wire:click="setFilter('cancelled')">
                Cancelled
                @if($this->counts['cancelled'] > 0)
                    <flux:badge size="sm" color="zinc" class="ml-2">{{ $this->counts['cancelled'] }}</flux:badge>
                @endif
            </flux:tab>
        </flux:tabs>
    </div>

    <!-- Collaborations List -->
    @if($this->collaborations->isEmpty())
        <flux:card class="text-center py-12">
            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <flux:icon name="briefcase" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
            </div>
            <flux:heading size="sm" class="mb-2 text-gray-900 dark:text-white">No {{ $filter }} collaborations</flux:heading>
            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                @if($filter === 'active')
                    When you start working with {{ auth()->user()->isBusinessAccount() ? 'influencers' : 'businesses' }}, your collaborations will appear here.
                @else
                    Your {{ $filter }} collaborations will appear here.
                @endif
            </flux:text>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($this->collaborations as $collaboration)
                <flux:card wire:key="collaboration-{{ $collaboration->id }}" class="hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1">
                                <!-- Campaign/Partner Avatar -->
                                <div class="flex-shrink-0">
                                    @if(auth()->user()->isBusinessAccount())
                                        <flux:avatar
                                            name="{{ $collaboration->influencer?->name ?? 'Influencer' }}"
                                            size="lg"
                                        />
                                    @else
                                        <flux:avatar
                                            name="{{ $collaboration->business?->name ?? 'Business' }}"
                                            size="lg"
                                        />
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <flux:heading size="sm" class="text-gray-900 dark:text-white truncate">
                                            {{ $collaboration->campaign->title }}
                                        </flux:heading>
                                        <flux:badge size="sm" :color="$collaboration->status->color()">
                                            {{ $collaboration->status->label() }}
                                        </flux:badge>
                                    </div>

                                    <flux:text size="sm" class="text-gray-600 dark:text-gray-400 mb-2">
                                        @if(auth()->user()->isBusinessAccount())
                                            with {{ $collaboration->influencer?->name ?? 'Influencer' }}
                                        @else
                                            with {{ $collaboration->business?->name ?? 'Business' }}
                                        @endif
                                    </flux:text>

                                    <!-- Progress Bar -->
                                    @if($collaboration->deliverables->isNotEmpty())
                                        @php
                                            $approved = $collaboration->deliverables->where('status', \App\Enums\CollaborationDeliverableStatus::APPROVED)->count();
                                            $total = $collaboration->deliverables->count();
                                            $percentage = $total > 0 ? round(($approved / $total) * 100) : 0;
                                        @endphp
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                                <div
                                                    class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-500"
                                                    style="width: {{ $percentage }}%"
                                                ></div>
                                            </div>
                                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                {{ $approved }}/{{ $total }} deliverables
                                            </flux:text>
                                        </div>
                                    @endif

                                    <!-- Timestamps -->
                                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        @if($collaboration->started_at)
                                            <span class="flex items-center gap-1">
                                                <flux:icon name="play" class="w-3.5 h-3.5" />
                                                Started {{ $collaboration->started_at->diffForHumans() }}
                                            </span>
                                        @endif
                                        @if($collaboration->completed_at)
                                            <span class="flex items-center gap-1">
                                                <flux:icon name="check-circle" class="w-3.5 h-3.5 text-green-500" />
                                                Completed {{ $collaboration->completed_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <flux:button
                                    href="{{ route('collaborations.show', $collaboration) }}"
                                    variant="primary"
                                    size="sm"
                                    icon="arrow-right"
                                    icon-trailing
                                >
                                    View Dashboard
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
