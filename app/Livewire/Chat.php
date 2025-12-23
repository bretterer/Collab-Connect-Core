<?php

namespace App\Livewire;

use App\Enums\ReactionType;
use App\Events\UserTyping;
use App\Models\Chat as ChatModel;
use App\Models\Message;
use App\Services\ChatService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class Chat extends BaseComponent
{
    public ?int $selectedChatId = null;

    public string $filter = 'active'; // 'active', 'archived', 'all'

    public string $searchQuery = '';

    #[Validate('required|string|max:2000')]
    public string $messageBody = '';

    public function mount(?int $chatId = null): void
    {
        if ($chatId) {
            $this->selectChat($chatId);
        }
    }

    #[Computed(persist: true, seconds: 300)]
    public function chats(): array
    {
        $user = $this->getAuthenticatedUser();

        return ChatService::getChatsForUser($user);
    }

    #[Computed]
    public function filteredActiveChats()
    {
        $chats = $this->chats['active'];

        if ($this->searchQuery) {
            $query = strtolower($this->searchQuery);
            $user = $this->getAuthenticatedUser();

            $chats = $chats->filter(function ($chat) use ($query, $user) {
                $displayName = strtolower($chat->getDisplayNameFor($user));
                $campaignName = strtolower($chat->getSubtitle());

                return str_contains($displayName, $query) || str_contains($campaignName, $query);
            });
        }

        return $chats;
    }

    #[Computed]
    public function filteredArchivedChats()
    {
        $chats = $this->chats['archived'];

        if ($this->searchQuery) {
            $query = strtolower($this->searchQuery);
            $user = $this->getAuthenticatedUser();

            $chats = $chats->filter(function ($chat) use ($query, $user) {
                $displayName = strtolower($chat->getDisplayNameFor($user));
                $campaignName = strtolower($chat->getSubtitle());

                return str_contains($displayName, $query) || str_contains($campaignName, $query);
            });
        }

        return $chats;
    }

    #[Computed]
    public function selectedChat(): ?ChatModel
    {
        if (! $this->selectedChatId) {
            return null;
        }

        $chat = ChatModel::with([
            'business',
            'influencer.user',
            'campaign',
        ])->find($this->selectedChatId);

        $user = $this->getAuthenticatedUser();

        if (! $chat || ! $chat->hasParticipant($user)) {
            $this->selectedChatId = null;

            return null;
        }

        return $chat;
    }

    #[Computed]
    public function chatMessages()
    {
        if (! $this->selectedChat) {
            return collect();
        }

        return ChatService::getMessages($this->selectedChat, 100);
    }

    #[Computed(persist: true, seconds: 60)]
    public function unreadCounts(): array
    {
        return ChatService::getUnreadCountsForUser($this->getAuthenticatedUser());
    }

    #[Computed]
    public function reactionTypes(): array
    {
        return ReactionType::toOptions();
    }

    public function selectChat(int $chatId): void
    {
        $user = $this->getAuthenticatedUser();
        $chat = ChatModel::find($chatId);

        if ($chat && $chat->hasParticipant($user)) {
            $this->selectedChatId = $chatId;

            // Mark messages as read
            ChatService::markAllAsRead($chat, $user);

            // Clear cached unread counts since we just marked messages as read
            unset($this->unreadCounts);

            $this->dispatch('chatSelected', chatId: $chatId);
        }
    }

    public function sendMessage(): void
    {
        if (! $this->selectedChat) {
            return;
        }

        $this->validate();

        $user = $this->getAuthenticatedUser();

        $message = ChatService::sendMessage(
            $this->selectedChat,
            $user,
            $this->messageBody
        );

        if (! $message) {
            $this->addError('messageBody', 'Unable to send message. The chat may be archived.');

            return;
        }

        $this->messageBody = '';

        // Clear cached chats since the latest message preview will change
        unset($this->chats);

        $this->dispatch('messageAdded');
    }

    public function toggleReaction(int $messageId, string $reactionType): void
    {
        $message = Message::find($messageId);

        if (! $message || ! $this->selectedChat) {
            return;
        }

        $type = ReactionType::tryFrom($reactionType);

        if (! $type) {
            return;
        }

        $user = $this->getAuthenticatedUser();

        ChatService::toggleReaction($message, $user, $type);
    }

    public function broadcastTyping(): void
    {
        if (! $this->selectedChat) {
            return;
        }

        broadcast(new UserTyping(
            $this->selectedChat,
            $this->getAuthenticatedUser(),
            true
        ))->toOthers();
    }

    public function broadcastStoppedTyping(): void
    {
        if (! $this->selectedChat) {
            return;
        }

        broadcast(new UserTyping(
            $this->selectedChat,
            $this->getAuthenticatedUser(),
            false
        ))->toOthers();
    }

    /**
     * Called from JavaScript when a new message is received via Echo.
     * Marks all messages as read if user is viewing the chat.
     */
    public function handleNewMessage(): void
    {
        // Clear cached data since a new message arrived
        unset($this->chats);
        unset($this->unreadCounts);

        if ($this->selectedChat) {
            ChatService::markAllAsRead($this->selectedChat, $this->getAuthenticatedUser());
        }
    }

    public function render()
    {
        return view('livewire.chat', [
            'currentUser' => $this->getAuthenticatedUser(),
        ]);
    }
}
