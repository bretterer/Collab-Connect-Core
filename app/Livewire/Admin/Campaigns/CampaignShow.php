<?php

namespace App\Livewire\Admin\Campaigns;

use App\Models\Campaign;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CampaignShow extends Component
{
    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign->load([
            'compensation',
            'requirements',
            'brief',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.campaigns.campaign-show');
    }

    public function getCampaignStats(): array
    {
        return [
            'total_applications' => $this->campaign->applications()->count(),
            'pending_applications' => $this->campaign->applications()->where('status', \App\Enums\CampaignApplicationStatus::PENDING)->count(),
            'accepted_applications' => $this->campaign->applications()->where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)->count(),
            'rejected_applications' => $this->campaign->applications()->where('status', \App\Enums\CampaignApplicationStatus::REJECTED)->count(),
        ];
    }
}
