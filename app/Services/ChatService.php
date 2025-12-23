<?php

namespace App\Services;

use App\Enums\ReactionType;
use App\Enums\SystemMessageType;
use App\Events\MessagesRead;
use App\Events\ReactionToggled;
use App\Models\Business;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

class ChatService
{
    /**
     * Create or retrieve a chat for a campaign-influencer pair.
     */
    public static function findOrCreateForCampaign(
        Business $business,
        Influencer $influencer,
        Campaign $campaign
    ): Chat {
        return Chat::findOrCreateForCampaign($business, $influencer, $campaign);
    }

    /**
     * Send a message to a chat.
     */
    public static function sendMessage(Chat $chat, User $user, string $body): ?Message
    {
        if (! $chat->canSendMessage($user)) {
            return null;
        }

        $message = $chat->messages()->create([
            'user_id' => $user->id,
            'body' => trim($body),
            'is_system_message' => false,
        ]);

        $chat->touchActivity();

        return $message;
    }

    /**
     * Send a system message to a chat.
     */
    public static function sendSystemMessage(Chat $chat, SystemMessageType $type, ?string $customBody = null): Message
    {
        $message = $chat->sendSystemMessage($type, $customBody);
        $chat->touchActivity();

        return $message;
    }

    /**
     * Mark messages as read by a user.
     *
     * @param  Collection<Message>|array<Message>  $messages
     */
    public static function markMessagesAsRead(Chat $chat, User $user, Collection|array $messages): void
    {
        $messages = collect($messages);
        $readMessageIds = [];

        foreach ($messages as $message) {
            $read = $message->markAsReadBy($user);
            if ($read) {
                $readMessageIds[] = $message->id;
            }
        }

        if (count($readMessageIds) > 0) {
            broadcast(new MessagesRead($chat, $user, $readMessageIds))->toOthers();
        }
    }

    /**
     * Mark all unread messages in a chat as read for a user (optimized bulk operation).
     */
    public static function markAllAsRead(Chat $chat, User $user): void
    {
        // Get IDs of unread messages (excluding user's own messages)
        $unreadMessageIds = $chat->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->id))
            ->pluck('id')
            ->toArray();

        if (empty($unreadMessageIds)) {
            return;
        }

        // Bulk insert read records
        $now = now();
        $readRecords = array_map(fn ($messageId) => [
            'message_id' => $messageId,
            'user_id' => $user->id,
            'read_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], $unreadMessageIds);

        \App\Models\MessageRead::insert($readRecords);

        broadcast(new MessagesRead($chat, $user, $unreadMessageIds))->toOthers();
    }

    /**
     * Toggle a reaction on a message.
     */
    public static function toggleReaction(Message $message, User $user, ReactionType $type): bool
    {
        $added = $message->toggleReaction($user, $type);

        broadcast(new ReactionToggled($message, $user, $type, $added))->toOthers();

        return $added;
    }

    /**
     * Archive a chat.
     */
    public static function archiveChat(Chat $chat): void
    {
        $chat->archive();
    }

    /**
     * Archive all chats for a campaign.
     */
    public static function archiveCampaignChats(Campaign $campaign): void
    {
        $campaign->load('business');

        $chats = Chat::forCampaign($campaign)
            ->active()
            ->get();

        foreach ($chats as $chat) {
            self::archiveChat($chat);
        }
    }

    /**
     * Get all chats for a user, organized by status.
     *
     * @return array{active: Collection<Chat>, archived: Collection<Chat>}
     */
    public static function getChatsForUser(User $user): array
    {
        $chats = Chat::forUser($user)
            ->with([
                'business',
                'influencer.user',
                'campaign',
                'latestMessage.user',
            ])
            ->orderByActivity()
            ->get();

        return [
            'active' => $chats->filter(fn (Chat $chat) => $chat->isActive()),
            'archived' => $chats->filter(fn (Chat $chat) => $chat->isArchived()),
        ];
    }

    /**
     * Get unread counts for all chats for a user (optimized single query).
     *
     * @return array<int, int> Chat ID => unread count
     */
    public static function getUnreadCountsForUser(User $user): array
    {
        $chatIds = Chat::forUser($user)->pluck('id');

        if ($chatIds->isEmpty()) {
            return [];
        }

        // Single query to get unread counts for all chats
        $counts = Message::whereIn('chat_id', $chatIds)
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->id))
            ->selectRaw('chat_id, COUNT(*) as unread_count')
            ->groupBy('chat_id')
            ->pluck('unread_count', 'chat_id')
            ->toArray();

        return $counts;
    }

    /**
     * Get total unread count across all chats for a user.
     */
    public static function getTotalUnreadCount(User $user): int
    {
        return array_sum(self::getUnreadCountsForUser($user));
    }

    /**
     * Get messages for a chat with pagination support.
     * Returns messages in chronological order (oldest first, newest last).
     *
     * @return Collection<Message>
     */
    public static function getMessages(Chat $chat, int $limit = 50, ?int $beforeId = null): Collection
    {
        $query = $chat->messages()
            ->with([
                'user',
                'reactions',
                'chat.influencer',
                'chat.business.users',
            ]);

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        // Order by oldest first so newest appears at bottom
        return $query->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->take($limit)
            ->get();
    }

    /**
     * Search messages within a chat.
     *
     * @return Collection<Message>
     */
    public static function searchMessages(Chat $chat, string $query): Collection
    {
        return $chat->messages()
            ->with(['user'])
            ->where('body', 'like', '%'.$query.'%')
            ->orderByDesc('created_at')
            ->take(50)
            ->get();
    }

    /**
     * Get messages that have been unread for more than the specified hours.
     *
     * @return Collection<Message>
     */
    public static function getStaleUnreadMessages(int $hours = 4): Collection
    {
        return Message::query()
            ->where('is_system_message', false)
            ->where('created_at', '<', now()->subHours($hours))
            ->whereDoesntHave('reads')
            ->with(['chat.business', 'chat.influencer.user', 'user'])
            ->get();
    }

    /**
     * Check if a user has access to a chat.
     */
    public static function userCanAccessChat(User $user, Chat $chat): bool
    {
        return $chat->hasParticipant($user);
    }

    /**
     * Check if a user can send messages to a chat.
     */
    public static function userCanSendMessage(User $user, Chat $chat): bool
    {
        return $chat->canSendMessage($user);
    }
}
