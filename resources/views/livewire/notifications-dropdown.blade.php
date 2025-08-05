<div class="relative" x-data="{ open: false }">
    <button class="relative rounded-full p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:hover:text-gray-300"
            @click="open = !open">
        <span class="sr-only">View notifications</span>
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <!-- Notification badge -->
        @if($unreadCount > 0)
            <span class="absolute right-0 top-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white dark:ring-gray-800"></span>
        @endif
    </button>

    <div class="absolute right-0 z-50 mt-2 w-80 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800"
         x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false">

        <div class="border-b border-gray-200 px-4 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-200">
            <div class="flex items-center justify-between">
                <span class="font-medium">Notifications</span>
                @if($unreadCount > 0)
                    <span class="text-xs text-gray-500">{{ $unreadCount }} new</span>
                @endif
            </div>
        </div>

        <div class="max-h-64 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700 {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="mt-2 h-2 w-2 rounded-full {{ !$notification->is_read ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium">{{ $notification->title }}</p>
                                @if(!$notification->is_read)
                                    <button wire:click="markAsRead({{ $notification->id }})" class="text-xs text-blue-600 hover:text-blue-500">
                                        Mark as read
                                    </button>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            @if($notification->action_url)
                                <a href="{{ $notification->action_url }}" class="text-xs text-blue-600 hover:text-blue-500 mt-1 block">
                                    View details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                    No notifications
                </div>
            @endforelse
        </div>

        @if(count($notifications) > 0)
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-2">
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-500">
                        Mark all as read
                    </button>
                @endif
                <a href="#" class="text-xs text-blue-600 hover:text-blue-500 dark:text-blue-400 ml-4">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>