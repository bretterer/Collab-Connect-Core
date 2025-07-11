<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AccountType;
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
     * Check if the user has completed onboarding
     */
    public function hasCompletedOnboarding(): bool
    {
        if ($this->account_type === AccountType::BUSINESS) {
            return $this->businessProfile?->onboarding_completed ?? false;
        }

        if ($this->account_type === AccountType::INFLUENCER) {
            return $this->influencerProfile?->onboarding_completed ?? false;
        }

        return false;
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
}
