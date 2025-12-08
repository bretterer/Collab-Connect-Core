<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Events\AccountTypeSelected;
use App\Services\ProfileService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_type' => AccountType::class,
            'market_approved' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function currentBusiness(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'current_business');
    }

    public function isCurrentBusiness(Business $business): bool
    {
        return $this->current_business === $business->id;
    }

    public function setCurrentBusiness(Business $business): void
    {
        if ($this->isBusinessAccount()) {
            $this->current_business = $business->id;
            $this->save();

            // Clear the cached profile relationship since it depends on current_business
            unset($this->profile);
        }
    }

    public function businessInvites(): HasMany
    {
        return $this->hasMany(BusinessMemberInvite::class, 'email', 'email');
    }

    public function hasBusinessInvitePending(): bool
    {
        return $this->businessInvites()->whereNull('joined_at')->exists();
    }

    /**
     * Get the businesses this user belongs to
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_users')
            ->withPivot('role');
    }

    /**
     * Get the user's influencer profile
     */
    public function influencer(): HasOne
    {
        return $this->hasOne(Influencer::class);
    }

    public function profile(): HasOne
    {
        if ($this->isInfluencerAccount()) {
            return $this->hasOne(Influencer::class);
        }

        if ($this->isBusinessAccount()) {
            return $this->hasOne(Business::class, 'id', 'current_business');
        }

        throw new \Exception('User profile type not defined for this user.');
    }

    /**
     * Get the user's social media accounts
     */
    public function socialMediaAccounts(): HasMany
    {
        return $this->hasMany(SocialMediaAccount::class);
    }

    /**
     * Get the user's campaigns
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the user's campaign applications (as an influencer)
     */
    public function campaignApplications(): HasMany
    {
        return $this->hasMany(CampaignApplication::class);
    }

    /**
     * Check if the user has completed onboarding
     */
    public function hasCompletedOnboarding(): bool
    {
        return ProfileService::hasCompletedOnboarding($this);
    }

    /**
     * Check if the user needs to complete onboarding
     */
    public function needsOnboarding(): bool
    {
        return $this->account_type === AccountType::UNDEFINED || ! $this->hasCompletedOnboarding();
    }

    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->account_type === AccountType::ADMIN || $this->access_admin;
    }

    /**
     * Check if the user is a business account
     */
    public function isBusinessAccount(): bool
    {
        return $this->account_type === AccountType::BUSINESS;
    }

    /**
     * Check if the user is an influencer account
     */
    public function isInfluencerAccount(): bool
    {
        return $this->account_type === AccountType::INFLUENCER;
    }

    /**
     * Get the username for the user's profile URL
     * Returns the username if available, otherwise returns the user ID
     */
    public function username(): string
    {
        if ($this->isBusinessAccount()) {
            return empty($this->currentBusiness?->username) ? (string) $this->id : $this->currentBusiness->username;
        }

        if ($this->isInfluencerAccount()) {
            return empty($this->influencer?->username) ? (string) $this->id : $this->influencer->username;
        }

        return (string) $this->id;
    }

    /**
     * Get the user's postal code information
     */
    public function getPostalCodeInfo(): ?PostalCode
    {
        $zipCode = ProfileService::getUserPostalCodeInfo($this);

        if (! $zipCode) {
            return null;
        }

        return PostalCode::where('postal_code', $zipCode)
            ->where('country_code', 'US')
            ->first();
    }

    /**
     * Get coordinates for the user's location
     */
    public function getCoordinates(): ?array
    {
        $postalCode = $this->getPostalCodeInfo();

        return $postalCode?->coordinates;
    }

    /**
     * Get all chats for this user (both as business member and influencer)
     * This uses the Chat::forUser scope which handles the new business-influencer-campaign structure
     */
    public function chats()
    {
        return Chat::forUser($this);
    }

    /**
     * Get all messages sent by this user
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the user's referral enrollment
     */
    public function referralEnrollment(): HasOne
    {
        return $this->hasOne(ReferralEnrollment::class);
    }

    /**
     * Get users that this user has referred.
     */
    public function referredUsers(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_user_id');
    }

    /**
     * Get the user who referred this user.
     */
    public function referredBy(): HasOne
    {
        return $this->hasOne(Referral::class, 'referred_user_id');
    }

    /**
     * Get referral program stats for this user.
     */
    public function getReferralStats(): array
    {
        $enrollment = $this->referralEnrollment;

        if (! $enrollment) {
            return [
                'enrolled' => false,
                'active_count' => 0,
                'pending_count' => 0,
                'total_count' => 0,
                'pending_payout' => 0,
                'lifetime_earnings' => 0,
            ];
        }

        return [
            'enrolled' => true,
            'active_count' => $enrollment->getActiveReferralCount(),
            'pending_count' => $enrollment->referrals()->pending()->count(),
            'total_count' => $enrollment->referrals()->count(),
            'pending_payout' => $enrollment->getPendingPayout()?->amount ?? 0,
            'lifetime_earnings' => $enrollment->getLifetimeEarnings(),
        ];
    }

    /**
     * Cached unread message count for the current request
     */
    private ?int $cachedUnreadMessageCount = null;

    /**
     * Get the total count of unread messages across all chats for this user.
     * Result is cached for the duration of the request.
     */
    public function getUnreadMessageCount(): int
    {
        if ($this->cachedUnreadMessageCount === null) {
            $this->cachedUnreadMessageCount = Message::whereIn('chat_id', $this->chats()->pluck('id'))
                ->unreadFor($this)
                ->count();
        }

        return $this->cachedUnreadMessageCount;
    }

    /**
     * Check if the user has any unread messages.
     */
    public function hasUnreadMessages(): bool
    {
        return $this->getUnreadMessageCount() > 0;
    }

    /**
     * Clear the cached unread message count (call after marking messages as read)
     */
    public function clearUnreadMessageCountCache(): void
    {
        $this->cachedUnreadMessageCount = null;
    }

    /**
     * Get the user's market waitlist entry
     */
    public function marketWaitlist(): HasOne
    {
        return $this->hasOne(MarketWaitlist::class);
    }

    /**
     * Get the campaigns saved by this user
     */
    public function savedCampaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'saved_campaigns')
            ->withTimestamps();
    }

    /**
     * Get the campaigns hidden by this user
     */
    public function hiddenCampaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'hidden_campaigns')
            ->withTimestamps();
    }

    /**
     * Check if a campaign is saved by this user
     */
    public function hasSavedCampaign(Campaign $campaign): bool
    {
        return $this->savedCampaigns()->where('campaign_id', $campaign->id)->exists();
    }

    /**
     * Check if a campaign is hidden by this user
     */
    public function hasHiddenCampaign(Campaign $campaign): bool
    {
        return $this->hiddenCampaigns()->where('campaign_id', $campaign->id)->exists();
    }

    /**
     * Check if the user is on the market waitlist
     */
    public function isOnMarketWaitlist(): bool
    {
        return $this->marketWaitlist()->exists();
    }

    /**
     * Set the user's account type.
     */
    public function setAccountType(AccountType $accountType): void
    {
        $this->account_type = $accountType;
        $saved = $this->save();

        if (! $saved) {
            throw new \Exception('Failed to set account type for user.');
        }

        // Trigger any additional logic needed after setting the account type
        AccountTypeSelected::dispatch($this, $accountType);
    }
}
