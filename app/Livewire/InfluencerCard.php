<?php

namespace App\Livewire;

use App\Models\Influencer;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Livewire\Component;

class InfluencerCard extends Component
{
    public Influencer $influencer;

    public bool $isPromoted = false;

    public bool $isVerified = false;

    public bool $acceptingInvitations = true;

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

        // Set promoted/verified status from influencer profile
        $this->isPromoted = (bool) $this->influencer->is_promoted;
        $this->isVerified = (bool) $this->influencer->is_verified;
        $this->acceptingInvitations = (bool) $this->influencer->is_accepting_invitations;

        // Use uploaded images from media library, with fallbacks
        $this->profileImageUrl = $this->influencer->getProfileImageUrl() ?: $defaultProfileImage;
        $this->coverImageUrl = $this->influencer->getBannerImageUrl() ?: $defaultCoverImage;

        // Get real review data
        $user = $this->influencer->user;
        if ($user) {
            $reviewService = app(ReviewService::class);
            $this->averageRating = $reviewService->getAverageRating($user);
            $this->reviewCount = $reviewService->getReviewCount($user);

            // Check if current user has saved/hidden this user
            $currentUser = Auth::user();
            if ($currentUser) {
                $this->isSaved = $currentUser->hasSavedUser($user);
                $this->isHidden = $currentUser->hasHiddenUser($user);
            }
        }
    }

    public function toggleSave(): void
    {
        $currentUser = Auth::user();
        $user = $this->influencer->user;
        if (! $currentUser || ! $user) {
            return;
        }

        if ($this->isSaved) {
            $currentUser->unsaveUser($user);
            $this->isSaved = false;
        } else {
            // If hidden, unhide first
            if ($this->isHidden) {
                $currentUser->unhideUser($user);
                $this->isHidden = false;
            }
            $currentUser->saveUser($user);
            $this->isSaved = true;
        }
    }

    public function toggleHide(): void
    {
        $currentUser = Auth::user();
        $user = $this->influencer->user;
        if (! $currentUser || ! $user) {
            return;
        }

        if ($this->isHidden) {
            $currentUser->unhideUser($user);
            $this->isHidden = false;
        } else {
            // If saved, unsave first
            if ($this->isSaved) {
                $currentUser->unsaveUser($user);
                $this->isSaved = false;
            }
            $currentUser->hideUser($user);
            $this->isHidden = true;
            $this->dispatch('user-hidden', userId: $user->id);
        }
    }

    public function getUsername(): string
    {
        return $this->influencer->username ?? (string) $this->influencer->id;
    }

    public function render()
    {
        // Get social accounts from the influencer relationship (InfluencerSocial)
        $socialAccounts = $this->influencer->socialAccounts ?? collect();
        $totalFollowers = $this->influencer->totalFollowers ?? 0;

        return view('livewire.influencer-card', [
            'socialAccounts' => $socialAccounts,
            'totalFollowers' => $totalFollowers,
        ]);
    }
}
