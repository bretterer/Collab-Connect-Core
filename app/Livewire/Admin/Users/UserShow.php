<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserShow extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user->load(['businessProfile', 'influencerProfile', 'campaigns', 'campaignApplications']);
    }

    public function render()
    {
        return view('livewire.admin.users.user-show');
    }

    public function getUserStats(): array
    {
        if ($this->user->isBusinessAccount()) {
            return [
                'campaigns_created' => $this->user->campaigns()->count(),
                'published_campaigns' => $this->user->campaigns()->where('status', \App\Enums\CampaignStatus::PUBLISHED)->count(),
                'draft_campaigns' => $this->user->campaigns()->where('status', \App\Enums\CampaignStatus::DRAFT)->count(),
                'total_applications' => $this->user->campaigns()->withCount('applications')->get()->sum('applications_count'),
            ];
        } elseif ($this->user->isInfluencerAccount()) {
            return [
                'applications_submitted' => $this->user->campaignApplications()->count(),
                'applications_accepted' => $this->user->campaignApplications()->where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)->count(),
                'applications_pending' => $this->user->campaignApplications()->where('status', \App\Enums\CampaignApplicationStatus::PENDING)->count(),
                'applications_rejected' => $this->user->campaignApplications()->where('status', \App\Enums\CampaignApplicationStatus::REJECTED)->count(),
            ];
        }

        return [];
    }
}
