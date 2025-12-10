<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Livewire\Component;

class InfluencerCard extends Component
{
    public User $user;

    public bool $isPromoted = false;

    public bool $isVerified = false;

    public bool $showFavorites = true;

    public string $profileImageUrl = '';

    public string $coverImageUrl = '';

    public ?float $averageRating = null;

    public int $reviewCount = 0;

    public bool $isSaved = false;

    public bool $isHidden = false;

    public function mount()
    {
        // Set default images first
        $defaultProfileImage = Vite::asset('resources/images/CollabConnectMark.png');
        $defaultCoverImage = 'data:image/svg+xml;base64,'.base64_encode('
            <svg width="400" height="200" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#0ea5e9;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#0284c7;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <rect width="100%" height="100%" fill="url(#grad)"/>
            </svg>');

        $this->profileImageUrl = $defaultProfileImage;
        $this->coverImageUrl = $defaultCoverImage;

        $influencer = $this->user->influencer;

        // Check if user has valid influencer profile
        if (! $influencer) {
            return;
        }

        // Set promoted/verified status from influencer profile
        $this->isPromoted = (bool) $influencer->is_promoted;
        $this->isVerified = (bool) $influencer->is_verified;

        // Use uploaded images from media library, with fallbacks
        $this->profileImageUrl = $influencer->getProfileImageUrl() ?: $defaultProfileImage;
        $this->coverImageUrl = $influencer->getBannerImageUrl() ?: $defaultCoverImage;

        // Get real review data
        $reviewService = app(ReviewService::class);
        $this->averageRating = $reviewService->getAverageRating($this->user);
        $this->reviewCount = $reviewService->getReviewCount($this->user);

        // Check if current user has saved/hidden this user
        $currentUser = Auth::user();
        if ($currentUser) {
            $this->isSaved = $currentUser->hasSavedUser($this->user);
            $this->isHidden = $currentUser->hasHiddenUser($this->user);
        }
    }

    public function toggleSave(): void
    {
        $currentUser = Auth::user();
        if (! $currentUser) {
            return;
        }

        if ($this->isSaved) {
            $currentUser->unsaveUser($this->user);
            $this->isSaved = false;
        } else {
            // If hidden, unhide first
            if ($this->isHidden) {
                $currentUser->unhideUser($this->user);
                $this->isHidden = false;
            }
            $currentUser->saveUser($this->user);
            $this->isSaved = true;
        }
    }

    public function toggleHide(): void
    {
        $currentUser = Auth::user();
        if (! $currentUser) {
            return;
        }

        if ($this->isHidden) {
            $currentUser->unhideUser($this->user);
            $this->isHidden = false;
        } else {
            // If saved, unsave first
            if ($this->isSaved) {
                $currentUser->unsaveUser($this->user);
                $this->isSaved = false;
            }
            $currentUser->hideUser($this->user);
            $this->isHidden = true;
            $this->dispatch('user-hidden', userId: $this->user->id);
        }
    }

    public function getUsername(): string
    {
        return $this->user->username();
    }

    public function render()
    {
        $influencer = $this->user->influencer;

        // Handle case where influencer relationship is missing
        if (! $influencer) {
            return view('livewire.influencer-card', [
                'socialAccounts' => collect(),
                'totalFollowers' => 0,
            ]);
        }

        // Get social accounts from the influencer relationship (InfluencerSocial)
        $socialAccounts = $influencer->socialAccounts ?? collect();
        $totalFollowers = $influencer->totalFollowers ?? 0;

        return view('livewire.influencer-card', [
            'socialAccounts' => $socialAccounts,
            'totalFollowers' => $totalFollowers,
        ]);
    }
}
