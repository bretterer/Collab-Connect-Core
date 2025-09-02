<div>
    <!-- Notification Button -->
    <button wire:click="openModal" class="relative -mr-2.5 p-2.5 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-2 right-2.5 block h-2 w-2 rounded-full bg-red-400"></span>
        @endif
    </button>

    <!-- Notification Flyout -->
    <flux:modal name="notifications" variant="flyout" position="right" wire:model="showModal" class="bg-gray-50! dark:bg-gray-900! md:w-128">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <flux:heading size="lg" class="font-semibold">Notifications</flux:heading>

            @if($unreadCount > 0)
                <flux:button wire:click="markAllAsRead" variant="subtle" size="sm" class="text-sm font-medium">
                    Mark all read
                </flux:button>
            @endif
        </div>

        <div class="overflow-y-auto">
            @if(count($notifications) > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($notifications as $notification)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150 @if(!$notification->read_at) bg-blue-50/50 dark:bg-blue-950/30 border-l-4 border-blue-500 dark:border-blue-400 @endif">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    @if(!$notification->read_at)
                                        <div class="h-2.5 w-2.5 bg-blue-500 dark:bg-blue-400 rounded-full ring-2 ring-blue-500/20 dark:ring-blue-400/20"></div>
                                    @else
                                        <div class="h-2.5 w-2.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white leading-5">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 leading-5">
                                        {{ $notification->data['message'] ?? 'No message available' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="flex-shrink-0 flex flex-col space-y-2 items-end">
                                    @if($notification->data['action_url'] ?? null)
                                        <a href="{{ $notification->data['action_url'] }}" target="_blank" rel="noopener noreferrer">
                                            <flux:button
                                                variant="subtle"
                                                size="sm"
                                                class="text-xs px-3 py-1.5 bg-green-100 hover:bg-green-200 dark:bg-green-900/40 dark:hover:bg-green-800/50 text-green-700 dark:text-green-300 font-medium rounded-md transition-colors"
                                            >
                                                View
                                            </flux:button>
                                        </a>
                                    @endif
                                    @if(!$notification->read_at)
                                    <flux:button
                                        wire:click="markAsRead('{{ $notification->id }}')"
                                        variant="subtle"
                                        size="sm"
                                        class="text-xs px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/40 dark:hover:bg-blue-800/50 text-blue-700 dark:text-blue-300 font-medium rounded-md transition-colors"
                                    >
                                        Mark read
                                    </flux:button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="text-gray-400 dark:text-gray-500 mb-4">
                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <flux:heading size="base" class="text-gray-600 dark:text-gray-400 font-semibold mb-2">All caught up!</flux:heading>
                    <flux:text class="text-gray-500 dark:text-gray-500 text-sm">
                        No new notifications to show
                    </flux:text>
                </div>
            @endif
        </div>
    </flux:modal>
</div>