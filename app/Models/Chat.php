<?php

namespace App\Models;

use App\Enums\ChatStatus;
use App\Enums\SystemMessageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    protected $fillable = [
        'business_id',
        'influencer_id',
        'campaign_id',
        'status',
        'archived_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ChatStatus::class,
            'archived_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

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
     * Get all messages for the chat.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Get the latest message for the chat.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Check if the given user is a participant in this chat.
     */
    public function hasParticipant(User $user): bool
    {
        return $this->isInfluencer($user) || $this->isBusinessMember($user);
    }

    /**
     * Check if the user is a business member in this chat.
     */
    public function isBusinessMember(User $user): bool
    {
        if (! $this->business) {
            return false;
        }

        // Use eager-loaded users if available (avoids N+1 exists() queries)
        if ($this->business->relationLoaded('users')) {
            return $this->business->users->contains('id', $user->id);
        }

        return $this->business->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if the user is the influencer in this chat.
     */
    public function isInfluencer(User $user): bool
    {
        return $this->influencer && $this->influencer->user_id === $user->id;
    }

    /**
     * Get the role of a user in this chat.
     */
    public function getUserRole(User $user): ?string
    {
        if ($this->isInfluencer($user)) {
            return 'influencer';
        }

        if ($this->isBusinessMember($user)) {
            return 'business';
        }

        return null;
    }

    /**
     * Check if the chat is active and can receive messages.
     */
    public function isActive(): bool
    {
        return $this->status === ChatStatus::Active;
    }

    /**
     * Check if the chat is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === ChatStatus::Archived;
    }

    /**
     * Check if a user can send messages to this chat.
     */
    public function canSendMessage(User $user): bool
    {
        return $this->isActive() && $this->hasParticipant($user);
    }

    /**
     * Archive the chat.
     */
    public function archive(): void
    {
        $this->update([
            'status' => ChatStatus::Archived,
            'archived_at' => now(),
        ]);

        // Send system message about archiving
        $this->sendSystemMessage(SystemMessageType::ChatArchived);
    }

    /**
     * Send a system message to this chat.
     */
    public function sendSystemMessage(SystemMessageType $type, ?string $customBody = null): Message
    {
        return $this->messages()->create([
            'user_id' => null,
            'body' => $customBody ?? $type->message(),
            'is_system_message' => true,
            'system_message_type' => $type->value,
        ]);
    }

    /**
     * Update the last activity timestamp.
     */
    public function touchActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Get the count of unread messages for a specific user in this chat.
     */
    public function getUnreadCountForUser(User $user): int
    {
        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();
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
            return $this->business->name ?? 'Business';
        }

        return $this->influencer->user->name ?? 'Influencer';
    }

    /**
     * Get the avatar URL for the chat from the perspective of the given user.
     */
    public function getAvatarFor(User $user): ?string
    {
        if ($this->isInfluencer($user)) {
            return $this->business->logo_url ?? null;
        }

        return $this->influencer->user->avatar_url ?? null;
    }

    /**
     * Get a subtitle for the chat (campaign name).
     */
    public function getSubtitle(): string
    {
        return $this->campaign->project_name ?? $this->campaign->campaign_goal ?? 'Campaign';
    }

    /**
     * Scope to get chats for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->whereHas('influencer', function ($influencerQuery) use ($user) {
                $influencerQuery->where('user_id', $user->id);
            })
                ->orWhereHas('business.users', function ($businessQuery) use ($user) {
                    $businessQuery->where('users.id', $user->id);
                });
        });
    }

    /**
     * Scope to get active chats.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ChatStatus::Active);
    }

    /**
     * Scope to get archived chats.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', ChatStatus::Archived);
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
     * Scope to order by most recent activity.
     */
    public function scopeOrderByActivity($query)
    {
        return $query->orderByDesc('last_activity_at');
    }

    /**
     * Find or create a chat between a business and influencer for a specific campaign.
     */
    public static function findOrCreateForCampaign(
        Business $business,
        Influencer $influencer,
        Campaign $campaign
    ): self {
        $chat = self::firstOrCreate([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ], [
            'status' => ChatStatus::Active,
            'last_activity_at' => now(),
        ]);

        // If it's a new chat, send the welcome system message
        if ($chat->wasRecentlyCreated) {
            $chat->sendSystemMessage(SystemMessageType::InfluencerAccepted);
        }

        return $chat;
    }

    /**
     * Get all participants of this chat (business users + influencer user).
     *
     * @return \Illuminate\Support\Collection<User>
     */
    public function getParticipants(): \Illuminate\Support\Collection
    {
        $participants = collect();

        // Add business users
        if ($this->business) {
            $participants = $participants->merge($this->business->users);
        }

        // Add influencer user
        if ($this->influencer && $this->influencer->user) {
            $participants->push($this->influencer->user);
        }

        return $participants->unique('id');
    }

    /**
     * Get the other participants (excluding the given user).
     *
     * @return \Illuminate\Support\Collection<User>
     */
    public function getOtherParticipants(User $user): \Illuminate\Support\Collection
    {
        return $this->getParticipants()->reject(fn (User $participant) => $participant->id === $user->id);
    }
}
