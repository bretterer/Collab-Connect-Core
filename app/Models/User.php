<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AccountType;
use App\Services\ProfileService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
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

    /**
     * Get the user's business profile
     */
    public function businessProfile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /**
     * Get the user's influencer profile
     */
    public function influencerProfile(): HasOne
    {
        return $this->hasOne(InfluencerProfile::class);
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
     * Get the user's notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
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
        return $this->account_type === AccountType::ADMIN;
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
     * Get chats where this user is the business user
     */
    public function businessChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'business_user_id');
    }

    /**
     * Get chats where this user is the influencer user
     */
    public function influencerChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'influencer_user_id');
    }

    /**
     * Get all chats for this user (both as business and influencer)
     */
    public function chats()
    {
        return Chat::where('business_user_id', $this->id)
            ->orWhere('influencer_user_id', $this->id);
    }

    /**
     * Get all messages sent by this user
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the total count of unread messages across all chats for this user.
     */
    public function getUnreadMessageCount(): int
    {
        return Message::whereIn('chat_id', $this->chats()->pluck('id'))
            ->unreadFor($this)
            ->count();
    }

    /**
     * Check if the user has any unread messages.
     */
    public function hasUnreadMessages(): bool
    {
        return $this->getUnreadMessageCount() > 0;
    }
}
