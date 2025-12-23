<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" level="1" class="text-gray-900 dark:text-white">
                        {{ $collaboration->campaign->project_name }}
                    </flux:heading>
                    <flux:badge :variant="$collaboration->status->color()">
                        {{ $collaboration->status->label() }}
                    </flux:badge>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <flux:icon name="calendar" class="w-4 h-4" />
                        Started {{ $collaboration->created_at->format('M j, Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <flux:icon name="user" class="w-4 h-4" />
                        @if($this->isBusiness)
                            Working with {{ $collaboration->influencer?->influencer?->display_name ?? 'Influencer' }}
                        @else
                            Working with {{ $collaboration->business?->name ?? 'Business' }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <flux:button href="{{ route('campaigns.show', $collaboration->campaign) }}" variant="ghost" icon="arrow-left">
                    View Campaign
                </flux:button>
                @if($collaboration->status->value === 'COMPLETED')
                    <flux:button href="{{ route('collaborations.review', $collaboration) }}" variant="primary" icon="star">
                        Leave Review
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
                <flux:text size="sm" class="text-gray-600 dark:text-gray-400">
                    Progress: {{ $this->progressStats['approved'] }} of {{ $this->progressStats['total'] }} deliverables approved
                </flux:text>
                <flux:text size="sm" class="font-medium text-gray-900 dark:text-white">
                    {{ $this->progressStats['percentage'] }}%
                </flux:text>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                <div
                    class="bg-gradient-to-r from-blue-500 to-green-500 h-2.5 rounded-full transition-all duration-500"
                    style="width: {{ $this->progressStats['percentage'] }}%"
                ></div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Content (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Overview Section -->
            <livewire:collaborations.overview :collaboration="$collaboration" wire:key="overview-{{ $collaboration->id }}" />

            <!-- Deliverables Section -->
            <livewire:collaborations.deliverables-list :collaboration="$collaboration" wire:key="deliverables-{{ $collaboration->id }}" />
        </div>

        <!-- Right Column - Sidebar (1/3) -->
        <div class="space-y-6">
            <!-- Embedded Chat -->
            <livewire:collaborations.embedded-chat :collaboration="$collaboration" wire:key="chat-{{ $collaboration->id }}" />

            <!-- Activity Timeline -->
            <livewire:collaborations.timeline :collaboration="$collaboration" wire:key="timeline-{{ $collaboration->id }}" />
        </div>
    </div>

    <!-- Submission Modal -->
    <livewire:collaborations.deliverable-submission-modal :collaboration="$collaboration" wire:key="submission-modal-{{ $collaboration->id }}" />
</div>
