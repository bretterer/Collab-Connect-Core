<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'body',
        'read_at',
        'read_by_user_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

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
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that read the message.
     */
    public function readByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by_user_id');
    }

    /**
     * Scope to get messages for a specific chat.
     */
    public function scopeForChat($query, Chat $chat)
    {
        return $query->where('chat_id', $chat->id);
    }

    /**
     * Check if the message belongs to the given user.
     */
    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if the message has been read.
     */
    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }

    /**
     * Check if the message has been read by a specific user.
     */
    public function isReadBy(User $user): bool
    {
        return $this->isRead() && $this->read_by_user_id === $user->id;
    }

    /**
     * Mark the message as read by a specific user.
     */
    public function markAsReadBy(User $user): void
    {
        if (! $this->belongsToUser($user) && ! $this->isRead()) {
            $this->update([
                'read_at' => now(),
                'read_by_user_id' => $user->id,
            ]);
        }
    }

    /**
     * Scope to get unread messages for a specific user.
     */
    public function scopeUnreadFor($query, User $user)
    {
        return $query->where('user_id', '!=', $user->id)
            ->whereNull('read_at');
    }

    /**
     * Scope to get read messages.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
