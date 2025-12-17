<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Livewire\Component;

class BusinessCard extends Component
{
    public Business $business;

    public bool $isPromoted = false;

    public bool $isVerified = false;

    public bool $showFavorites = true;

    public string $profileImageUrl = '';

    public string $coverImageUrl = '';

    public ?float $averageRating = null;

    public int $reviewCount = 0;

    public bool $isSaved = false;

    public bool $isHidden = false;

    /**
     * Get the owner's User model for save/hide functionality.
     */
    private function getOwnerUser(): ?User
    {
        return $this->business->owner()->first();
    }

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

        // Get real review data for the business
        $reviewService = app(ReviewService::class);
        $this->averageRating = $reviewService->getAverageRatingForBusiness($this->business);
        $this->reviewCount = $reviewService->getReviewCountForBusiness($this->business);

        // Use uploaded images from media library, with fallbacks
        $this->profileImageUrl = $this->business->getLogoUrl() ?: $defaultProfileImage;
        $this->coverImageUrl = $this->business->getBannerImageUrl() ?: $defaultCoverImage;

        // Check if current user has saved/hidden this user
        $currentUser = Auth::user();
        $ownerUser = $this->getOwnerUser();
        if ($currentUser && $ownerUser) {
            $this->isSaved = $currentUser->hasSavedUser($ownerUser);
            $this->isHidden = $currentUser->hasHiddenUser($ownerUser);
        }

        // Check if promoted/verified
        $this->isPromoted = $this->business->is_promoted;
        // $this->isVerified = $this->business->is_verified;
    }

    public function toggleSave(): void
    {
        $currentUser = Auth::user();
        $ownerUser = $this->getOwnerUser();
        if (! $currentUser || ! $ownerUser) {
            return;
        }

        if ($this->isSaved) {
            $currentUser->unsaveUser($ownerUser);
            $this->isSaved = false;
        } else {
            // If hidden, unhide first
            if ($this->isHidden) {
                $currentUser->unhideUser($ownerUser);
                $this->isHidden = false;
            }
            $currentUser->saveUser($ownerUser);
            $this->isSaved = true;
        }
    }

    public function toggleHide(): void
    {
        $currentUser = Auth::user();
        $ownerUser = $this->getOwnerUser();
        if (! $currentUser || ! $ownerUser) {
            return;
        }

        if ($this->isHidden) {
            $currentUser->unhideUser($ownerUser);
            $this->isHidden = false;
        } else {
            // If saved, unsave first
            if ($this->isSaved) {
                $currentUser->unsaveUser($ownerUser);
                $this->isSaved = false;
            }
            $currentUser->hideUser($ownerUser);
            $this->isHidden = true;
            $this->dispatch('user-hidden', userId: $ownerUser->id);
        }
    }

    public function viewBusinessProfile()
    {
        $username = ! empty($this->business->username) ? $this->business->username : $this->business->id;

        return $this->redirect(route('business.profile', ['username' => $username]), navigate: true);
    }

    public function viewBusinessCampaigns()
    {
        $ownerUser = $this->getOwnerUser();
        if (! $ownerUser) {
            return;
        }

        return $this->redirect(route('business.campaigns', ['user' => $ownerUser->id]), navigate: true);
    }

    public function render()
    {
        return view('livewire.business-card');
    }
}
