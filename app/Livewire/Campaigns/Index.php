<?php

namespace App\Livewire\Campaigns;

use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Services\CampaignService;
use Livewire\Attributes\Layout;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class Index extends BaseComponent
{
    public string $activeTab = 'drafts';

    public bool $showArchiveModal = false;
    public ?int $campaignToArchive = null;

    public function mount()
    {
        // Ensure user is authenticated and is a business account
        if (! $this->getAuthenticatedUser() || ! $this->getAuthenticatedUser()->isBusinessAccount()) {
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

    public function getInProgress()
    {
        return CampaignService::getUserInProgress($this->getAuthenticatedUser());
    }

    public function getArchived()
    {
        return CampaignService::getUserArchived($this->getAuthenticatedUser());
    }

    public function confirmArchive($campaignId)
    {
        $this->campaignToArchive = $campaignId;
        $this->showArchiveModal = true;
    }

    public function archiveCampaign()
    {
        if (!$this->campaignToArchive) {
            return;
        }

        $campaign = $this->getAuthenticatedUser()->campaigns()->find($this->campaignToArchive);

        if ($campaign) {
            CampaignService::archiveCampaign($campaign);
            session()->flash('message', 'Campaign archived successfully!');
        }

        $this->closeArchiveModal();
    }

    public function closeArchiveModal()
    {
        $this->showArchiveModal = false;
        $this->campaignToArchive = null;
    }

    public function startCampaign($campaignId)
    {
        $campaign = Campaign::query()->find($campaignId);

        if($campaign->applications->isEmpty()) {
            Toaster::error('You must have at least one influencer application to start this campaign.');
            return;
        }

        if ($campaign) {
            CampaignService::startCampaign($campaign);
            session()->flash('message', 'Campaign started successfully!');
        }
    }

    public function render()
    {
        return view('livewire.campaigns.index');
    }
}
