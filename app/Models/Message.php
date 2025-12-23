<?php

namespace App\Models;

use App\Enums\ReactionType;
use App\Enums\SystemMessageType;
use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'body',
        'is_system_message',
        'system_message_type',
    ];

    protected function casts(): array
    {
        return [
            'is_system_message' => 'boolean',
            'system_message_type' => SystemMessageType::class,
        ];
    }

    protected $dispatchesEvents = [
        'created' => MessageSent::class,
    ];

    /**
     * Get the chat that owns the message.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the user that sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the read receipts for this message.
     */
    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Get the reactions for this message.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    /**
     * Check if this is a system message.
     */
    public function isSystemMessage(): bool
    {
        return $this->is_system_message;
    }

    /**
     * Check if the message belongs to the given user.
     */
    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if the message has been read by a specific user.
     */
    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }

    /**
     * Mark the message as read by a specific user.
     */
    public function markAsReadBy(User $user): ?MessageRead
    {
        // Don't mark your own messages as read
        if ($this->belongsToUser($user)) {
            return null;
        }

        // Don't duplicate read receipts
        if ($this->isReadBy($user)) {
            return null;
        }

        return $this->reads()->create([
            'user_id' => $user->id,
            'read_at' => now(),
        ]);
    }

    /**
     * Get the sender's display name.
     */
    public function getSenderName(): string
    {
        if ($this->is_system_message) {
            return 'System';
        }

        return $this->user?->name ?? 'Unknown';
    }

    /**
     * Get the sender's role in the chat context.
     */
    public function getSenderRole(): ?string
    {
        if ($this->is_system_message || ! $this->user || ! $this->chat) {
            return null;
        }

        return $this->chat->getUserRole($this->user);
    }

    /**
     * Toggle a reaction for a user.
     */
    public function toggleReaction(User $user, ReactionType $type): bool
    {
        $existingReaction = $this->reactions()
            ->where('user_id', $user->id)
            ->where('reaction_type', $type->value)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();

            return false; // Reaction removed
        }

        $this->reactions()->create([
            'user_id' => $user->id,
            'reaction_type' => $type->value,
        ]);

        return true; // Reaction added
    }

    /**
     * Check if a user has reacted with a specific type.
     */
    public function hasReactionFrom(User $user, ReactionType $type): bool
    {
        return $this->reactions()
            ->where('user_id', $user->id)
            ->where('reaction_type', $type->value)
            ->exists();
    }

    /**
     * Get reaction counts grouped by type.
     *
     * @return array<string, int>
     */
    public function getReactionCounts(): array
    {
        // Use already loaded reactions if available and not empty (avoids N+1 queries)
        // Only use collection if it has data - empty might mean stale data
        if ($this->relationLoaded('reactions') && $this->reactions->isNotEmpty()) {
            return $this->reactions
                ->groupBy('reaction_type')
                ->map(fn ($group) => $group->count())
                ->toArray();
        }

        // Query fresh data if not loaded or collection is empty
        return $this->reactions()
            ->selectRaw('reaction_type, COUNT(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();
    }

    /**
     * Get users who reacted with a specific type.
     *
     * @return \Illuminate\Support\Collection<User>
     */
    public function getReactors(ReactionType $type): \Illuminate\Support\Collection
    {
        return User::whereIn('id',
            $this->reactions()
                ->where('reaction_type', $type->value)
                ->pluck('user_id')
        )->get();
    }

    /**
     * Scope to get messages for a specific chat.
     */
    public function scopeForChat($query, Chat $chat)
    {
        return $query->where('chat_id', $chat->id);
    }

    /**
     * Scope to get unread messages for a specific user.
     */
    public function scopeUnreadFor($query, User $user)
    {
        return $query->where('user_id', '!=', $user->id)
            ->where('is_system_message', false)
            ->whereDoesntHave('reads', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
    }

    /**
     * Scope to get messages older than a specified time that haven't been read.
     */
    public function scopeUnreadOlderThan($query, int $hours)
    {
        return $query->where('created_at', '<', now()->subHours($hours))
            ->where('is_system_message', false)
            ->whereDoesntHave('reads');
    }

    /**
     * Scope to only get user messages (not system messages).
     */
    public function scopeUserMessages($query)
    {
        return $query->where('is_system_message', false);
    }

    /**
     * Scope to only get system messages.
     */
    public function scopeSystemMessages($query)
    {
        return $query->where('is_system_message', true);
    }
}
