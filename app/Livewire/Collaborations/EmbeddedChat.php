<?php

namespace App\Livewire\Collaborations;

use App\Livewire\BaseComponent;
use App\Models\Chat;
use App\Models\Collaboration;
use App\Services\ChatService;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EmbeddedChat extends BaseComponent
{
    public Collaboration $collaboration;

    public ?Chat $chat = null;

    public string $messageBody = '';

    public function mount(Collaboration $collaboration): void
    {
        $this->collaboration = $collaboration;
        $this->loadOrCreateChat();
    }

    protected function loadOrCreateChat(): void
    {
        $business = $this->collaboration->business;
        $influencer = $this->collaboration->influencer?->influencer;
        $campaign = $this->collaboration->campaign;

        if (! $business || ! $influencer || ! $campaign) {
            return;
        }

        $this->chat = Chat::findOrCreateForCampaign($business, $influencer, $campaign);
    }

    public function getMessagesProperty(): Collection
    {
        if (! $this->chat) {
            return collect();
        }

        return ChatService::getMessages($this->chat, 50);
    }

    public function sendMessage(): void
    {
        if (! $this->chat) {
            Flux::toast('Chat not available.');

            return;
        }

        if (empty(trim($this->messageBody))) {
            return;
        }

        $user = $this->getAuthenticatedUser();

        if (! $this->chat->canSendMessage($user)) {
            Flux::toast('You cannot send messages to this chat.');

            return;
        }

        ChatService::sendMessage($this->chat, $user, $this->messageBody);

        $this->messageBody = '';

        $this->dispatch('scroll-chat');
    }

    public function markAsRead(): void
    {
        if ($this->chat) {
            ChatService::markAllAsRead($this->chat, $this->getAuthenticatedUser());
        }
    }

    public function getUnreadCountProperty(): int
    {
        if (! $this->chat) {
            return 0;
        }

        return $this->chat->getUnreadCountForUser($this->getAuthenticatedUser());
    }

    public function getListeners(): array
    {
        if (! $this->chat) {
            return [];
        }

        return [
            "echo-presence:chat.{$this->chat->id},.message.sent" => '$refresh',
        ];
    }

    public function render(): View
    {
        return view('livewire.collaborations.embedded-chat');
    }
}
