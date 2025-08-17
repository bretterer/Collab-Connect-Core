<?php

namespace App\Livewire\Admin\Users;

use App\Models\CampaignApplication;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserShow extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user->load(['businesses', 'influencer', 'businesses.campaigns', 'businesses.campaigns.applications']);
    }

    public function render()
    {
        return view('livewire.admin.users.user-show');
    }

    public function getUserStats(): array
    {
        if ($this->user->isBusinessAccount()) {
            $campaignsQuery = $this->user->businesses->flatMap(fn($business) => $business->campaigns);
            return [
                'campaigns_created' => $campaignsQuery->count(),
                'published_campaigns' => $campaignsQuery->where('status', \App\Enums\CampaignStatus::PUBLISHED)->count(),
                'draft_campaigns' => $campaignsQuery->where('status', \App\Enums\CampaignStatus::DRAFT)->count(),
                'total_applications' => $campaignsQuery->flatMap(fn($campaign) => $campaign->applications)->count(),
            ];
        } elseif ($this->user->isInfluencerAccount()) {
            $baseQuery = CampaignApplication::query()
                ->where('user_id', $this->user->id);
            return [
                'applications_submitted' => $baseQuery->count(),
                'applications_accepted' => $baseQuery->where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)->count(),
                'applications_pending' => $baseQuery->where('status', \App\Enums\CampaignApplicationStatus::PENDING)->count(),
                'applications_rejected' => $baseQuery->where('status', \App\Enums\CampaignApplicationStatus::REJECTED)->count(),
            ];
        }

        return [];
    }
}
