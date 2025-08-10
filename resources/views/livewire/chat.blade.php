<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="chatInterface()">
        @if(session('message'))
            <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg h-[700px] flex">
            <!-- Chat List Sidebar -->
            <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Messages</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $chats->count() }} conversations</p>
                </div>

                <!-- Chat List -->
                <div class="flex-1 overflow-y-auto">
                    @forelse($chats as $chat)
                        @php
                            $otherUser = $chat->getOtherParticipant($currentUser);
                            $latestMessage = $chat->latestMessage;
                        @endphp

                        <div wire:click="selectChat({{ $chat->id }})"
                             class="p-4 border-b border-gray-100 dark:border-gray-700 cursor-pointer transition-colors
                                    {{ $selectedChatId === $chat->id ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <div class="flex items-center space-x-3">
                                <!-- Avatar -->
                                <div class="flex-shrink-0 relative">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ $otherUser ? $otherUser->initials() : 'U' }}
                                    </div>
                                    <!-- Online indicator -->
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 border-2 border-white dark:border-gray-800 rounded-full"
                                         :class="onlineUsers.find(u => u.id === {{ $otherUser?->id ?? 'null' }}) ? 'bg-green-400' : 'bg-gray-400'"></div>
                                </div>

                                <!-- Chat Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $otherUser?->name ?? 'Unknown User' }}
                                    </p>
                                    @if($latestMessage)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                            {{ Str::limit($latestMessage->body, 40) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            {{ $latestMessage->created_at->diffForHumans() }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-500">No messages yet</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">No conversations yet</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">Start a conversation to see it here</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div class="flex-1 flex flex-col">
                @if($selectedChat)
                    @php
                        $otherUser = $selectedChat->getOtherParticipant($currentUser);
                    @endphp

                    <!-- Chat Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ $otherUser ? $otherUser->initials() : 'U' }}
                                    </div>
                                    <!-- Online indicator -->
                                    <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 border-2 border-white dark:border-gray-800 rounded-full"
                                         :class="onlineUsers.find(u => u.id === {{ $otherUser?->id ?? 'null' }}) ? 'bg-green-400' : 'bg-gray-400'"></div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $otherUser?->name ?? 'Unknown User' }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <span x-show="onlineUsers.find(u => u.id === {{ $otherUser?->id ?? 'null' }})">Online</span>
                                        <span x-show="!onlineUsers.find(u => u.id === {{ $otherUser?->id ?? 'null' }})">Offline</span>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Online users count -->
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="onlineUsers.length"></span> online
                            </div>
                        </div>
                    </div>

                    <!-- Messages Container -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4"
                         id="messages-container"
                         x-ref="messagesContainer">
                        @forelse($messages as $message)
                            <div class="flex {{ $message->user_id === $currentUser->id ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-sm lg:max-w-md">
                                    <div class="flex items-end space-x-2 {{ $message->user_id === $currentUser->id ? 'flex-row-reverse space-x-reverse' : '' }}">
                                        <!-- Avatar -->
                                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                            {{ $message->user->initials() }}
                                        </div>

                                        <!-- Message Bubble -->
                                        <div class="px-4 py-2 rounded-lg {{ $message->user_id === $currentUser->id
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }}">
                                            <p class="text-sm">{{ $message->body }}</p>
                                            <p class="text-xs mt-1 opacity-70">
                                                {{ $message->created_at->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">No messages yet</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">Send a message to start the conversation</p>
                                </div>
                            </div>
                        @endforelse
                        
                        <!-- Typing Indicator -->
                        <div x-show="typingUsers.length > 0" class="flex justify-start">
                            <div class="max-w-sm lg:max-w-md">
                                <div class="flex items-end space-x-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                        <span x-text="typingUsers[0]?.initials || typingUsers[0]?.name?.charAt(0) || '?'"></span>
                                    </div>
                                    <div class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700">
                                        <div class="flex space-x-1">
                                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-pulse"></div>
                                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-8">
                                    <span x-text="typingUsers[0]?.name"></span> is typing...
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <form wire:submit="sendMessage" class="flex space-x-4">
                            <div class="flex-1">
                                <textarea
                                    wire:model="messageBody"
                                    x-ref="messageInput"
                                    placeholder="Type your message..."
                                    rows="1"
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none"
                                    x-data="{
                                        resize() {
                                            $el.style.height = 'auto';
                                            $el.style.height = Math.min($el.scrollHeight, 120) + 'px';
                                        }
                                    }"
                                    x-init="resize()"
                                    @input="resize(); onTyping()"
                                    @keydown.enter.prevent="if (!$event.shiftKey) { onStoppedTyping(); $wire.sendMessage(); resize(); }"
                                    @blur="onStoppedTyping()"
                                ></textarea>
                                @error('messageBody')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!$wire.messageBody.trim()"
                            >
                                Send
                            </button>
                        </form>

                    </div>
                @else
                    <!-- No Chat Selected -->
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Select a conversation</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Choose a chat from the sidebar to start messaging</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <script>
            function chatInterface() {
                return {
                    currentChannel: null,
                    currentUser: {!! json_encode($currentUser) !!},
                    selectedChatId: {{ $selectedChatId ?? 'null' }},
                    onlineUsers: [],
                    typingUsers: [],
                    typingTimeout: null,

                    init() {
                        this.scrollToBottom();

                        // If a chat is already selected (from mount), subscribe to it
                        if (this.selectedChatId) {
                            this.subscribeToChat(this.selectedChatId);
                        }

                        // Listen for chat selection
                        Livewire.on('chatSelected', (data) => {
                            this.selectedChatId = data.chatId;
                            this.subscribeToChat(data.chatId);
                            setTimeout(() => this.scrollToBottom(), 100);
                        });

                        // Listen for new messages sent by current user
                        Livewire.on('messageAdded', () => {
                            setTimeout(() => this.scrollToBottom(), 100);
                        });

                    },

                    subscribeToChat(chatId) {
                        // Leave previous channel if exists
                        if (this.currentChannel) {
                            window.Echo.leave(this.currentChannel);
                        }

                        // Subscribe to the presence channel for this chat
                        const channelName = `chat.${chatId}`;
                        this.currentChannel = channelName;

                        try {
                            window.Echo.join(channelName)
                                .here((users) => {
                                    this.onlineUsers = users;
                                })
                                .joining((user) => {
                                    this.onlineUsers.push(user);
                                })
                                .leaving((user) => {
                                    this.onlineUsers = this.onlineUsers.filter(u => u.id !== user.id);
                                    this.typingUsers = this.typingUsers.filter(u => u.id !== user.id);
                                })
                                .listen('MessageSent', (e) => {
                                    let messageData = e.message;
                                    if (typeof e.message === 'string') {
                                        try {
                                            messageData = JSON.parse(e.message);
                                        } catch (parseError) {
                                            return;
                                        }
                                    }
                                    this.handleNewMessage(messageData);
                                })
                                .listenForWhisper('typing', (e) => {
                                    this.handleTyping(e);
                                })
                                .listenForWhisper('stopped-typing', (e) => {
                                    this.handleStoppedTyping(e);
                                });

                        } catch (error) {
                            console.error('Error subscribing to chat channel:', error);
                        }
                    },

                    handleNewMessage(messageData) {
                        // Don't add message if it's from the current user (already added by Livewire)
                        if (messageData.user_id === this.currentUser.id) {
                            return;
                        }

                        // Remove typing indicator for the sender
                        this.typingUsers = this.typingUsers.filter(u => u.id !== messageData.user_id);

                        // Refresh the Livewire component to show the new message
                        this.$wire.$refresh();

                        // Scroll to bottom after DOM update
                        setTimeout(() => this.scrollToBottom(), 200);
                    },

                    handleTyping(e) {
                        if (e.user.id !== this.currentUser.id) {
                            // Add user to typing list if not already there
                            if (!this.typingUsers.find(u => u.id === e.user.id)) {
                                this.typingUsers.push(e.user);
                            }
                        }
                    },

                    handleStoppedTyping(e) {
                        this.typingUsers = this.typingUsers.filter(u => u.id !== e.user.id);
                    },

                    onTyping() {
                        if (!this.currentChannel) return;

                        // Send typing whisper
                        window.Echo.join(this.currentChannel)
                            .whisper('typing', {
                                user: this.currentUser
                            });

                        // Clear existing timeout
                        if (this.typingTimeout) {
                            clearTimeout(this.typingTimeout);
                        }

                        // Set timeout to stop typing after 3 seconds
                        this.typingTimeout = setTimeout(() => {
                            this.onStoppedTyping();
                        }, 3000);
                    },

                    onStoppedTyping() {
                        if (!this.currentChannel) return;

                        window.Echo.join(this.currentChannel)
                            .whisper('stopped-typing', {
                                user: this.currentUser
                            });

                        if (this.typingTimeout) {
                            clearTimeout(this.typingTimeout);
                            this.typingTimeout = null;
                        }
                    },

                    scrollToBottom() {
                        const container = document.getElementById('messages-container');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                            
                            // Force scroll if needed
                            setTimeout(() => {
                                container.scrollTop = container.scrollHeight;
                            }, 50);
                        }
                    }
                }
            }
        </script>
    </div>
</div>
