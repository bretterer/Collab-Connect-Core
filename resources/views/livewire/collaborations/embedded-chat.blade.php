<div>
    <flux:card class="overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/50 dark:to-emerald-950/50 p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="text-gray-900 dark:text-white">Messages</flux:heading>
                    @if($this->unreadCount > 0)
                        <flux:text size="sm" class="text-green-600 dark:text-green-400">
                            {{ $this->unreadCount }} unread message{{ $this->unreadCount > 1 ? 's' : '' }}
                        </flux:text>
                    @else
                        <flux:text size="sm" class="text-gray-600 dark:text-gray-400">
                            Chat with your collaborator
                        </flux:text>
                    @endif
                </div>
                @if($chat)
                    <flux:button
                        href="{{ route('chat.show', $chat->id) }}"
                        variant="ghost"
                        size="sm"
                        icon="arrow-top-right-on-square"
                    >
                        Open Full Chat
                    </flux:button>
                @endif
            </div>
        </div>

        @if(!$chat)
            <div class="p-6 text-center">
                <flux:icon name="chat-bubble-left-right" class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                    Chat will be available once both parties are confirmed.
                </flux:text>
            </div>
        @else
            <!-- Messages Area -->
            <div
                id="embedded-chat-messages"
                class="h-72 overflow-y-auto p-4 space-y-3"
                x-data
                x-init="
                    $nextTick(() => $el.scrollTop = $el.scrollHeight);
                    Livewire.hook('morph.updated', ({ el }) => {
                        if (el.id === 'embedded-chat-messages') {
                            $nextTick(() => el.scrollTop = el.scrollHeight);
                        }
                    });
                "
                x-on:scroll-chat.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
                wire:poll.30s="$refresh"
            >
                @forelse($this->messages as $message)
                    @php
                        $isOwn = $message->user_id === auth()->id();
                        $isSystem = $message->is_system_message ?? false;
                    @endphp

                    <div wire:key="embedded-message-{{ $message->id }}">
                        @if($isSystem)
                            <div class="flex justify-center my-2">
                                <div class="px-3 py-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-full">
                                    {{ $message->body }}
                                </div>
                            </div>
                        @else
                            <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                                <div class="flex gap-2 max-w-[85%] {{ $isOwn ? 'flex-row-reverse' : '' }}">
                                    @if(!$isOwn)
                                        <flux:avatar size="xs" name="{{ $message->user->name }}" class="flex-shrink-0" />
                                    @endif

                                    <div class="flex flex-col {{ $isOwn ? 'items-end' : 'items-start' }}">
                                        @if(!$isOwn)
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">
                                                {{ $message->user->first_name }}
                                            </span>
                                        @endif

                                        <div class="px-3 py-2 rounded-2xl {{ $isOwn
                                            ? 'bg-blue-600 text-white rounded-br-md'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-bl-md' }}">
                                            <p class="text-sm break-words">{{ $message->body }}</p>
                                        </div>

                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ $message->created_at->format('g:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <flux:icon name="chat-bubble-left-right" class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto" />
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400 mt-2">
                                Start a conversation
                            </flux:text>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Message Input or Completed State -->
            @if($collaboration->status->value === 'completed')
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="text-center">
                        <flux:icon name="check-circle" class="w-8 h-8 text-green-500 mx-auto mb-2" />
                        <flux:text size="sm" class="text-gray-600 dark:text-gray-400 mb-3">
                            This collaboration has been completed.
                        </flux:text>
                        <flux:button
                            href="{{ route('collaborations.review', $collaboration) }}"
                            variant="primary"
                            size="sm"
                            icon="star"
                        >
                            Leave a Review
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <form
                        wire:submit="sendMessage"
                        x-on:submit="$nextTick(() => $dispatch('scroll-chat'))"
                        class="flex items-center gap-2"
                    >
                        <div class="flex-1">
                            <input
                                type="text"
                                wire:model="messageBody"
                                placeholder="Type a message..."
                                class="block w-full h-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm px-4"
                                @keydown.enter.prevent="$wire.sendMessage(); $dispatch('scroll-chat');"
                            />
                        </div>
                        <flux:button type="submit" variant="primary" size="sm" icon="paper-airplane" />
                    </form>
                </div>
            @endif
        @endif
    </flux:card>
</div>
