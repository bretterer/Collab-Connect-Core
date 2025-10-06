<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'business_id',
        'influencer_id',
        'campaign_id',
    ];

    /**
     * Get the business that owns the chat.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the influencer that owns the chat.
     */
    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }

    /**
     * Get the campaign this chat is associated with.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get all business users who have access to this chat.
     */
    public function businessUsers(): BelongsToMany
    {
        return $this->business->users();
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
     * Business users must be part of the business, influencers must own the influencer profile.
     */
    public function hasParticipant(User $user): bool
    {
        // Check if user is the influencer
        if ($this->influencer && $this->influencer->user_id === $user->id) {
            return true;
        }

        // Check if user is part of the business
        if ($this->business) {
            return $this->business->users()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Check if the user is a business member in this chat.
     */
    public function isBusinessMember(User $user): bool
    {
        if ($this->business) {
            return $this->business->users()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Check if the user is the influencer in this chat.
     */
    public function isInfluencer(User $user): bool
    {
        return $this->influencer && $this->influencer->user_id === $user->id;
    }

    /**
     * Scope to get chats for a specific user.
     * Returns chats where user is either part of the business or is the influencer.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            // Chats where user is the influencer
            $q->whereHas('influencer', function ($influencerQuery) use ($user) {
                $influencerQuery->where('user_id', $user->id);
            })
            // OR chats where user is part of the business
                ->orWhereHas('business.users', function ($businessQuery) use ($user) {
                    $businessQuery->where('users.id', $user->id);
                });
        });
    }

    /**
     * Scope to get chats for a specific business.
     */
    public function scopeForBusiness($query, Business $business)
    {
        return $query->where('business_id', $business->id);
    }

    /**
     * Scope to get chats for a specific campaign.
     */
    public function scopeForCampaign($query, Campaign $campaign)
    {
        return $query->where('campaign_id', $campaign->id);
    }

    /**
     * Find or create a chat between a business and influencer for a specific campaign.
     */
    public static function findOrCreateForCampaign(
        Business $business,
        Influencer $influencer,
        Campaign $campaign
    ): self {
        return self::firstOrCreate([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
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

    /**
     * Get a display name for the chat from the perspective of the given user.
     */
    public function getDisplayNameFor(User $user): string
    {
        if ($this->isInfluencer($user)) {
            // Influencer sees the business name
            return $this->business->name ?? 'Business';
        }

        // Business users see the influencer name
        return $this->influencer->user->name ?? 'Influencer';
    }
}
