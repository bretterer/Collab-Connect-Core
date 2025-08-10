<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    protected $fillable = [
        'business_user_id',
        'influencer_user_id',
    ];

    /**
     * Get the business user that owns the chat.
     */
    public function businessUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'business_user_id');
    }

    /**
     * Get the influencer user that owns the chat.
     */
    public function influencerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'influencer_user_id');
    }

    /**
     * Get all messages for the chat.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Get the latest message for the chat.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Check if the given user is a participant in this chat.
     */
    public function hasParticipant(User $user): bool
    {
        return $this->business_user_id === $user->id || $this->influencer_user_id === $user->id;
    }

    /**
     * Get the other participant in the chat (not the given user).
     */
    public function getOtherParticipant(User $user): ?User
    {
        if ($this->business_user_id === $user->id) {
            return $this->influencerUser;
        }

        if ($this->influencer_user_id === $user->id) {
            return $this->businessUser;
        }

        return null;
    }

    /**
     * Scope to get chats for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('business_user_id', $user->id)
            ->orWhere('influencer_user_id', $user->id);
    }

    /**
     * Find or create a chat between two users.
     */
    public static function findOrCreateBetweenUsers(User $businessUser, User $influencerUser): self
    {
        return self::firstOrCreate([
            'business_user_id' => $businessUser->id,
            'influencer_user_id' => $influencerUser->id,
        ]);
    }

    /**
     * Get the count of unread messages for a specific user in this chat.
     */
    public function getUnreadCountForUser(User $user): int
    {
        return $this->messages()->unreadFor($user)->count();
    }

    /**
     * Check if this chat has unread messages for a specific user.
     */
    public function hasUnreadMessagesFor(User $user): bool
    {
        return $this->getUnreadCountForUser($user) > 0;
    }
}
