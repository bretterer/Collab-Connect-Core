<div>
    <flux:card class="overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 dark:from-slate-950/50 dark:to-gray-950/50 p-4 border-b border-gray-200 dark:border-gray-700">
            <flux:heading size="base" class="text-gray-900 dark:text-white">Activity Timeline</flux:heading>
            <flux:text size="sm" class="text-gray-600 dark:text-gray-400">
                Recent activity on this collaboration
            </flux:text>
        </div>

        <div class="p-4">
            @if($this->activities->isEmpty())
                <div class="text-center py-6">
                    <flux:icon name="clock" class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                    <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                        No activity yet
                    </flux:text>
                </div>
            @else
                <div class="space-y-0">
                    @foreach($this->activities as $index => $activity)
                        <div wire:key="activity-{{ $activity->id }}" class="relative pl-6 pb-4 {{ !$loop->last ? 'border-l-2 border-gray-200 dark:border-gray-700' : '' }}">
                            <!-- Timeline dot -->
                            <div class="absolute left-0 -translate-x-1/2 w-3 h-3 rounded-full bg-{{ $activity->color }}-500 ring-4 ring-white dark:ring-gray-900"></div>

                            <div class="ml-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <flux:icon :name="$activity->icon" class="w-4 h-4 text-{{ $activity->color }}-500" />
                                    <flux:text size="sm" class="font-medium text-gray-900 dark:text-white">
                                        {{ $activity->description }}
                                    </flux:text>
                                </div>

                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    @if($activity->user)
                                        <span>by {{ $activity->user->name }}</span>
                                        <span>·</span>
                                    @endif
                                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                                </div>

                                @if($activity->metadata && isset($activity->metadata['notes']))
                                    <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-800/50 rounded text-sm text-gray-600 dark:text-gray-400">
                                        {{ $activity->metadata['notes'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($this->hasMore)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <flux:button wire:click="loadMore" variant="ghost" class="w-full" size="sm">
                            Load More
                        </flux:button>
                    </div>
                @endif
            @endif
        </div>
    </flux:card>
</div>
