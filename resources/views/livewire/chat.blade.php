<div>
    @if(!auth()->user()->profile->subscribed('default'))
        <livewire:components.subscription-prompt
            variant="green"
            heading="Subscribe to Message"
            description="Subscribe to unlock direct messaging and start communicating with your matches."
            :features="[
                'Unlimited messaging',
                'Real-time notifications',
                'Message reactions',
                'Message history'
            ]"
        />
    @else
    <livewire:components.beta-notification />

    <div class="flex gap-6" x-data="chatApp()" x-init="init()">
        {{-- Sidebar --}}
        <div class="w-80 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Sidebar Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <flux:heading size="lg">Messages</flux:heading>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->filteredActiveChats->count() }} active, {{ $this->filteredArchivedChats->count() }} archived
                    </flux:text>

                    <div class="mt-3">
                        <flux:input
                            wire:model.live.debounce.300ms="searchQuery"
                            placeholder="Search conversations..."
                            icon="magnifying-glass"
                            size="sm"
                        />
                    </div>
                </div>

                {{-- Chat List --}}
                <div class="max-h-[calc(100vh-20rem)] overflow-y-auto">
                    @if($this->filteredActiveChats->isNotEmpty())
                        <div class="px-4 py-2">
                            <flux:text class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Active Chats
                            </flux:text>
                        </div>

                        @foreach($this->filteredActiveChats as $chat)
                            @php
                                $displayName = $chat->getDisplayNameFor($currentUser);
                                $subtitle = $chat->getSubtitle();
                                $latestMessage = $chat->latestMessage;
                                $unreadCount = $this->unreadCounts[$chat->id] ?? 0;
                                $isSelected = $selectedChatId === $chat->id;
                            @endphp

                            <div
                                wire:key="chat-{{ $chat->id }}"
                                wire:click="selectChat({{ $chat->id }})"
                                class="mx-2 mb-1 p-3 rounded-lg cursor-pointer transition-all duration-150
                                    {{ $isSelected
                                        ? 'bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800'
                                        : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 border border-transparent' }}"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="relative flex-shrink-0">
                                        <flux:avatar size="sm" name="{{ $displayName }}" />
                                        @if($unreadCount > 0)
                                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-xs text-white font-bold">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-medium text-gray-900 dark:text-white truncate {{ $unreadCount > 0 ? 'font-semibold' : '' }}">
                                                {{ $displayName }}
                                            </span>
                                            @if($latestMessage)
                                                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                                    {{ $latestMessage->created_at->shortAbsoluteDiffForHumans() }}
                                                </span>
                                            @endif
                                        </div>

                                        <span class="text-xs text-blue-600 dark:text-blue-400 truncate block">
                                            {{ $subtitle }}
                                        </span>

                                        @if($latestMessage)
                                            <span class="text-sm text-gray-600 dark:text-gray-400 truncate block mt-1 {{ $unreadCount > 0 ? 'font-medium' : '' }}">
                                                @if($latestMessage->is_system_message)
                                                    <span class="italic">{{ Str::limit($latestMessage->body, 40) }}</span>
                                                @else
                                                    <span class="font-medium">{{ $latestMessage->user_id === $currentUser->id ? 'You' : $latestMessage->user->first_name }}:</span>
                                                    {{ Str::limit($latestMessage->body, 30) }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400 italic mt-1 block">No messages yet</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($this->filteredArchivedChats->isNotEmpty())
                        <div class="px-4 py-2 mt-2" x-data="{ showArchived: false }">
                            <button
                                @click="showArchived = !showArchived"
                                class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-300 transition-colors w-full"
                            >
                                <flux:icon.archive-box class="w-4 h-4" />
                                <span>Archived ({{ $this->filteredArchivedChats->count() }})</span>
                                <flux:icon.chevron-down class="w-4 h-4 ml-auto transition-transform duration-200" x-bind:class="{ 'rotate-180': showArchived }" />
                            </button>

                            <div x-show="showArchived" x-collapse class="mt-2">
                                @foreach($this->filteredArchivedChats as $chat)
                                    @php
                                        $displayName = $chat->getDisplayNameFor($currentUser);
                                        $subtitle = $chat->getSubtitle();
                                        $isSelected = $selectedChatId === $chat->id;
                                    @endphp

                                    <div
                                        wire:key="archived-chat-{{ $chat->id }}"
                                        wire:click="selectChat({{ $chat->id }})"
                                        class="mb-1 p-3 rounded-lg cursor-pointer transition-all duration-150 opacity-60
                                            {{ $isSelected
                                                ? 'bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600'
                                                : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 border border-transparent' }}"
                                    >
                                        <div class="flex items-start gap-3">
                                            <flux:avatar size="sm" name="{{ $displayName }}" class="grayscale" />
                                            <div class="flex-1 min-w-0">
                                                <span class="font-medium text-gray-700 dark:text-gray-300 truncate block">{{ $displayName }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 truncate block">{{ $subtitle }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($this->filteredActiveChats->isEmpty() && $this->filteredArchivedChats->isEmpty())
                        <div class="p-8 text-center">
                            <flux:icon.chat-bubble-left-right class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto" />
                            <flux:heading size="sm" class="mt-4">No conversations</flux:heading>
                            <flux:text class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                @if($searchQuery)
                                    No chats match your search
                                @else
                                    When you're accepted into campaigns, your chats will appear here
                                @endif
                            </flux:text>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Chat Area --}}
        <div class="flex-1 min-w-0">
            {{-- Loading Skeleton --}}
            <flux:skeleton.group animate="shimmer" wire:loading.delay wire:target="selectChat" class="w-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col h-[calc(100vh-12rem)]">
                {{-- Skeleton Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <flux:skeleton class="size-10 rounded-full" />
                        <div class="flex-1">
                            <flux:skeleton.line class="w-32" />
                            <flux:skeleton.line class="w-48 mt-1" />
                        </div>
                    </div>
                </div>

                {{-- Skeleton Messages --}}
                <div class="flex-1 overflow-hidden p-6 space-y-4">
                    {{-- System message skeleton --}}
                    <div class="flex justify-center">
                        <flux:skeleton.line class="w-64" />
                    </div>

                    {{-- Incoming message skeleton --}}
                    <div class="flex justify-start gap-2">
                        <flux:skeleton class="size-8 rounded-full flex-shrink-0" />
                        <div>
                            <flux:skeleton.line class="w-24 mb-1" />
                            <flux:skeleton class="h-16 w-64 rounded-2xl rounded-bl-md" />
                        </div>
                    </div>

                    {{-- Outgoing message skeleton --}}
                    <div class="flex justify-end">
                        <flux:skeleton class="h-12 w-48 rounded-2xl rounded-br-md" />
                    </div>

                    {{-- Incoming message skeleton --}}
                    <div class="flex justify-start gap-2">
                        <flux:skeleton class="size-8 rounded-full flex-shrink-0" />
                        <div>
                            <flux:skeleton.line class="w-20 mb-1" />
                            <flux:skeleton class="h-10 w-56 rounded-2xl rounded-bl-md" />
                        </div>
                    </div>

                    {{-- Outgoing message skeleton --}}
                    <div class="flex justify-end">
                        <flux:skeleton class="h-20 w-72 rounded-2xl rounded-br-md" />
                    </div>
                </div>

                {{-- Skeleton Input --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-end gap-3">
                        <flux:skeleton class="flex-1 h-10 rounded-xl" />
                        <flux:skeleton class="size-10 rounded-lg" />
                    </div>
                </div>
            </flux:skeleton.group>

            <div wire:loading.remove wire:target="selectChat">
            @if($this->selectedChat)
                @php
                    $chat = $this->selectedChat;
                    $displayName = $chat->getDisplayNameFor($currentUser);
                    $isArchived = $chat->isArchived();
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col h-[calc(100vh-12rem)]">
                    {{-- Chat Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <flux:avatar size="sm" name="{{ $displayName }}" />
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $displayName }}</span>
                                        @if($isArchived)
                                            <flux:badge size="sm" color="zinc">Archived</flux:badge>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $chat->getSubtitle() }}</span>
                                </div>
                            </div>

                            <div class="text-sm text-gray-500 dark:text-gray-400" x-show="onlineUsers.length > 0">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    <span x-text="onlineUsers.length + ' online'"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($isArchived)
                        <div class="px-4 py-2 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/50">
                            <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400">
                                <flux:icon.archive-box class="w-4 h-4" />
                                <span class="text-sm">This chat is archived. You can view messages but cannot send new ones.</span>
                            </div>
                        </div>
                    @endif

                    {{-- Messages --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-container" x-ref="messagesContainer">
                        @forelse($this->chatMessages as $message)
                            @php
                                $isOwn = $message->user_id === $currentUser->id;
                                $isSystem = $message->is_system_message;
                                $senderRole = $message->getSenderRole();
                                $reactions = $message->getReactionCounts();
                            @endphp

                            <div wire:key="message-{{ $message->id }}">
                                @if($isSystem)
                                    <div class="flex justify-center my-2">
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 text-xs text-gray-500 dark:text-gray-400">
                                            @if($message->system_message_type)
                                                <flux:icon :name="$message->system_message_type->icon()" class="w-3.5 h-3.5" />
                                            @endif
                                            <span>{{ $message->body }}</span>
                                            <span class="text-gray-400 dark:text-gray-500">·</span>
                                            <span class="text-gray-400 dark:text-gray-500">{{ $message->created_at->format('M j, g:i A') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} group">
                                        <div class="flex gap-2 max-w-[70%] {{ $isOwn ? 'flex-row-reverse' : '' }}">
                                            @if(!$isOwn)
                                                <flux:avatar size="xs" name="{{ $message->user->name }}" class="flex-shrink-0 mt-0.5" />
                                            @endif

                                            <div class="flex flex-col {{ $isOwn ? 'items-end' : 'items-start' }}">
                                                @if(!$isOwn)
                                                    <div class="flex items-center gap-1.5 mb-1">
                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $message->user->first_name }}</span>
                                                        @if($senderRole === 'business')
                                                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300">Business</span>
                                                        @elseif($senderRole === 'influencer')
                                                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300">Influencer</span>
                                                        @endif
                                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $message->created_at->format('g:i A') }}</span>
                                                    </div>
                                                @endif

                                                <div class="relative" x-data="{ showReactions: false }">
                                                    <div class="px-4 py-2 rounded-2xl {{ $isOwn
                                                        ? 'bg-blue-600 text-white rounded-br-md'
                                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-bl-md' }}">
                                                        <p class="text-sm whitespace-pre-wrap break-words">{{ $message->body }}</p>
                                                    </div>

                                                    @if(!$isArchived)
                                                        <button
                                                            @click="showReactions = !showReactions"
                                                            class="absolute {{ $isOwn ? '-left-7' : '-right-7' }} top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600"
                                                        >
                                                            <flux:icon.face-smile class="w-4 h-4 text-gray-400" />
                                                        </button>

                                                        <div
                                                            x-show="showReactions"
                                                            @click.outside="showReactions = false"
                                                            x-transition
                                                            class="absolute {{ $isOwn ? 'right-0' : 'left-0' }} -top-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 px-1 py-1 flex gap-0.5 z-10"
                                                        >
                                                            @foreach($this->reactionTypes as $reaction)
                                                                <button
                                                                    wire:click="toggleReaction({{ $message->id }}, '{{ $reaction['value'] }}')"
                                                                    @click="showReactions = false"
                                                                    class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors text-base"
                                                                    title="{{ $reaction['label'] }}"
                                                                >
                                                                    {{ $reaction['emoji'] }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if(!empty($reactions))
                                                        <div class="flex flex-wrap gap-1 mt-1 {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                                                            @foreach($reactions as $type => $count)
                                                                @php
                                                                    $reactionType = \App\Enums\ReactionType::tryFrom($type);
                                                                    $hasReacted = $message->hasReactionFrom($currentUser, $reactionType);
                                                                @endphp
                                                                @if($reactionType)
                                                                    <button
                                                                        wire:click="toggleReaction({{ $message->id }}, '{{ $type }}')"
                                                                        class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-xs transition-colors
                                                                            {{ $hasReacted
                                                                                ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300'
                                                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                                                                        @if($isArchived) disabled @endif
                                                                    >
                                                                        <span>{{ $reactionType->emoji() }}</span>
                                                                        <span class="font-medium">{{ $count }}</span>
                                                                    </button>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>

                                                @if($isOwn)
                                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 mr-1">{{ $message->created_at->format('g:i A') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <flux:icon.chat-bubble-left-right class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto" />
                                    <p class="font-medium text-gray-700 dark:text-gray-300 mt-4">Start the conversation</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Send a message to begin collaborating</p>
                                </div>
                            </div>
                        @endforelse

                        <div x-show="typingUsers.length > 0" x-transition class="flex justify-start">
                            <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-2xl rounded-bl-md">
                                <div class="flex gap-1">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="typingUsers.map(u => u.name).join(', ')"></span>
                                    <span x-text="typingUsers.length === 1 ? ' is typing...' : ' are typing...'"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Message Input --}}
                    @if(!$isArchived)
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            <form wire:submit="sendMessage" class="flex items-end gap-3">
                                <div class="flex-1">
                                    <textarea
                                        wire:model="messageBody"
                                        placeholder="Type a message..."
                                        rows="1"
                                        class="block w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none text-sm py-2.5 px-4"
                                        x-data="{
                                            resize() {
                                                $el.style.height = 'auto';
                                                $el.style.height = Math.min($el.scrollHeight, 120) + 'px';
                                            }
                                        }"
                                        x-init="resize()"
                                        @input="resize(); onTyping()"
                                        @keydown.enter.prevent="if (!$event.shiftKey) { onStoppedTyping(); $wire.sendMessage(); $el.style.height = 'auto'; }"
                                        @blur="onStoppedTyping()"
                                    ></textarea>
                                    @error('messageBody')
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <flux:button type="submit" variant="primary" icon="paper-airplane" />
                            </form>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 h-[calc(100vh-12rem)] flex items-center justify-center">
                    <div class="text-center">
                        <flux:icon.chat-bubble-left-right class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto" />
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mt-4">Select a conversation</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose a chat from the sidebar to start messaging</p>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>

    <script>
        function chatApp() {
            return {
                currentChannelName: null,
                currentChannelInstance: null,
                currentUser: @json($currentUser),
                selectedChatId: @json($selectedChatId),
                onlineUsers: [],
                typingUsers: [],
                typingTimeout: null,

                init() {
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });

                    if (this.selectedChatId) {
                        this.subscribeToChat(this.selectedChatId);
                    }

                    Livewire.on('chatSelected', (data) => {
                        this.selectedChatId = data.chatId;
                        this.subscribeToChat(data.chatId);
                        this.typingUsers = [];
                        this.$nextTick(() => this.scrollToBottom());
                    });

                    Livewire.on('messageAdded', () => {
                        this.$nextTick(() => this.scrollToBottom());
                    });

                    Livewire.hook('morph.updated', ({ el }) => {
                        if (el.id === 'messages-container' || el.closest('#messages-container')) {
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    });
                },

                subscribeToChat(chatId) {
                    if (this.currentChannelName) {
                        window.Echo.leave(this.currentChannelName);
                        this.currentChannelInstance = null;
                    }

                    const channelName = `chat.${chatId}`;
                    this.currentChannelName = channelName;

                    try {
                        this.currentChannelInstance = window.Echo.join(channelName)
                            .here((users) => {
                                this.onlineUsers = users;
                            })
                            .joining((user) => {
                                if (!this.onlineUsers.find(u => u.id === user.id)) {
                                    this.onlineUsers.push(user);
                                }
                            })
                            .leaving((user) => {
                                this.onlineUsers = this.onlineUsers.filter(u => u.id !== user.id);
                                this.typingUsers = this.typingUsers.filter(u => u.id !== user.id);
                            })
                            .listen('.message.sent', (e) => {
                                if (e.message && e.message.user_id !== this.currentUser.id) {
                                    this.typingUsers = this.typingUsers.filter(u => u.id !== e.message.user_id);
                                    this.$wire.$refresh().then(() => {
                                        this.$wire.handleNewMessage();
                                        this.$nextTick(() => this.scrollToBottom());
                                    });
                                }
                            })
                            .listen('.reaction.toggled', () => {
                                this.$wire.$refresh();
                            })
                            .listen('.user.typing', (e) => {
                                if (e.user_id !== this.currentUser.id) {
                                    if (e.is_typing) {
                                        if (!this.typingUsers.find(u => u.id === e.user_id)) {
                                            this.typingUsers.push({ id: e.user_id, name: e.user_name });
                                        }
                                    } else {
                                        this.typingUsers = this.typingUsers.filter(u => u.id !== e.user_id);
                                    }
                                }
                            })
                            .listenForWhisper('typing', (e) => {
                                if (e.user && e.user.id !== this.currentUser.id) {
                                    if (!this.typingUsers.find(u => u.id === e.user.id)) {
                                        this.typingUsers.push(e.user);
                                    }
                                }
                            })
                            .listenForWhisper('stopped-typing', (e) => {
                                if (e.user) {
                                    this.typingUsers = this.typingUsers.filter(u => u.id !== e.user.id);
                                }
                            });
                    } catch (error) {
                        console.error('Error subscribing to chat channel:', error);
                    }
                },

                onTyping() {
                    if (!this.currentChannelInstance) return;

                    this.currentChannelInstance.whisper('typing', { user: this.currentUser });

                    if (this.typingTimeout) {
                        clearTimeout(this.typingTimeout);
                    }

                    this.typingTimeout = setTimeout(() => {
                        this.onStoppedTyping();
                    }, 3000);
                },

                onStoppedTyping() {
                    if (!this.currentChannelInstance) return;

                    this.currentChannelInstance.whisper('stopped-typing', { user: this.currentUser });

                    if (this.typingTimeout) {
                        clearTimeout(this.typingTimeout);
                        this.typingTimeout = null;
                    }
                },

                scrollToBottom() {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        requestAnimationFrame(() => {
                            container.scrollTop = container.scrollHeight;
                        });
                    }
                }
            }
        }
    </script>
    @endif
</div>
