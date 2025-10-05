<?php

namespace App\Livewire\Influencers;

use App\Models\User;
use App\Models\CampaignApplication;
use App\Enums\CampaignApplicationStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ViewProfile extends Component
{
    public User $user;
    public $influencer;
    public $socialAccounts;
    public $completedCampaigns;
    public $totalFollowers;
    public $avgEngagement;

    public function mount($userId)
    {
        $this->user = User::with([
            'influencer.socialAccounts',
            'influencer.postalCodeInfo'
        ])->findOrFail($userId);

        $this->influencer = $this->user->influencer;

        if (!$this->influencer) {
            abort(404, 'Influencer profile not found');
        }

        $this->socialAccounts = $this->influencer->socialAccounts;

        // Get completed campaigns (accepted applications)
        $this->completedCampaigns = CampaignApplication::with('campaign.business')
            ->where('user_id', $this->user->id)
            ->where('status', CampaignApplicationStatus::ACCEPTED)
            ->latest()
            ->limit(5)
            ->get();

        // Calculate total followers
        $this->totalFollowers = $this->socialAccounts->sum('followers');

        // For now, avg engagement will be null since we don't have that data
        $this->avgEngagement = null;
    }

    public function getProfileImageUrl()
    {
        return $this->influencer->getProfileImageUrl()
            ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name) . '&size=200';
    }

    public function getLocation()
    {
        $parts = array_filter([
            $this->influencer->city,
            $this->influencer->state,
        ]);

        return !empty($parts) ? implode(', ', $parts) : 'Location not provided';
    }

    public function getJoinedDate()
    {
        return 'Member since ' . $this->user->created_at->format('M Y');
    }

    public function render()
    {
        return view('livewire.influencers.view-profile', [
            'profileImageUrl' => $this->getProfileImageUrl(),
            'location' => $this->getLocation(),
            'joinedDate' => $this->getJoinedDate(),
        ]);
    }
}
