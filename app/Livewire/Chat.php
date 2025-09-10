<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Chat as ChatModel;
use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class Chat extends BaseComponent
{
    public ?int $selectedChatId = null;

    #[Validate('required|string|max:1000')]
    public string $messageBody = '';

    public function mount($chatId = null)
    {
        if ($chatId) {
            $this->selectChat((int) $chatId);
        }
    }

    public function render()
    {
        $user = $this->getAuthenticatedUser();

        // Get all chats for the current user with latest messages
        $chats = ChatModel::forUser($user)
            ->with(['businessUser', 'influencerUser', 'latestMessage'])
            ->orderByDesc('updated_at')
            ->get();

        // Get messages for the selected chat
        $messages = collect();
        $selectedChat = null;

        if ($this->selectedChatId) {
            $selectedChat = ChatModel::find($this->selectedChatId);

            // Ensure user has access to this chat
            if ($selectedChat && $selectedChat->hasParticipant($user)) {
                $messages = $selectedChat->messages()
                    ->with('user')
                    ->orderBy('created_at')
                    ->get();

                // Mark unread messages as read by current user
                $this->markMessagesAsRead($selectedChat, $user);
            } else {
                // Reset selected chat if user doesn't have access
                $this->selectedChatId = null;
                $selectedChat = null;
            }
        }

        return view('livewire.chat', [
            'chats' => $chats,
            'messages' => $messages,
            'selectedChat' => $selectedChat,
            'currentUser' => $user,
        ]);
    }

    public function selectChat(int $chatId)
    {
        $user = $this->getAuthenticatedUser();
        $chat = ChatModel::find($chatId);

        // Verify user has access to this chat
        if ($chat && $chat->hasParticipant($user)) {
            $this->selectedChatId = $chatId;
            $this->dispatch('chatSelected', chatId: $chatId);
        }
    }

    public function sendMessage()
    {
        if (! $this->selectedChatId) {
            return;
        }

        $this->validate();

        $user = $this->getAuthenticatedUser();
        $chat = ChatModel::find($this->selectedChatId);

        // Verify user has access to this chat
        if (! $chat || ! $chat->hasParticipant($user)) {
            $this->flashError('You do not have access to this chat.');

            return;
        }

        // Create the message
        $message = Message::create([
            'chat_id' => $this->selectedChatId,
            'user_id' => $user->id,
            'body' => trim($this->messageBody),
        ]);

        // Broadcast the message
        MessageSent::dispatch($message);

        // Update chat timestamp
        $chat->touch();

        // Clear message input
        $this->messageBody = '';

        // Dispatch event to scroll to bottom
        $this->dispatch('messageAdded');
    }

    /**
     * Mark all unread messages in a chat as read by the current user.
     */
    private function markMessagesAsRead(ChatModel $chat, User $user): void
    {
        $unreadMessages = $chat->messages()
            ->unreadFor($user)
            ->get();

        foreach ($unreadMessages as $message) {
            $message->markAsReadBy($user);
        }
    }
}
