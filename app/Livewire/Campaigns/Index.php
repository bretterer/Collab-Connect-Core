<?php

namespace App\Livewire\Campaigns;

use App\Livewire\BaseComponent;
use App\Services\CampaignService;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends BaseComponent
{
    public string $activeTab = 'drafts';

    public function mount()
    {
        // Ensure user is authenticated and is a business account
        if (!$this->getAuthenticatedUser() || !$this->getAuthenticatedUser()->isBusinessAccount()) {
            return redirect()->route('dashboard');
        }
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function getDrafts()
    {
        return CampaignService::getUserDrafts($this->getAuthenticatedUser());
    }

    public function getPublished()
    {
        return CampaignService::getUserPublished($this->getAuthenticatedUser());
    }

    public function getScheduled()
    {
        return CampaignService::getUserScheduled($this->getAuthenticatedUser());
    }

    public function getArchived()
    {
        return CampaignService::getUserArchived($this->getAuthenticatedUser());
    }

    public function archiveCampaign($campaignId)
    {
        $campaign = $this->getAuthenticatedUser()->campaigns()->find($campaignId);

        if ($campaign) {
            CampaignService::archiveCampaign($campaign);
            session()->flash('message', 'Campaign archived successfully!');
        }
    }

    public function render()
    {
        return view('livewire.campaigns.index');
    }
}
