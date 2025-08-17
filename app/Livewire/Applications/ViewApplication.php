<?php

namespace App\Livewire\Applications;

use App\Enums\CampaignApplicationStatus;
use App\Livewire\BaseComponent;
use App\Models\CampaignApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ViewApplication extends BaseComponent
{
    public CampaignApplication $application;

    public function mount(CampaignApplication $application)
    {
        // Ensure the user owns the campaign this application is for
        if ($application->campaign->business->owner->first()->id !== Auth::user()->id) {
            abort(403, 'You do not have permission to view this application.');
        }

        $this->application = $application->load([
            'user.socialMediaAccounts',
            'campaign.compensation',
            'campaign.requirements',
            'campaign.brief',
        ]);
    }

    public function acceptApplication()
    {
        if ($this->application->status !== CampaignApplicationStatus::PENDING) {
            $this->flashError('This application has already been processed.');

            return;
        }

        $this->application->update([
            'status' => CampaignApplicationStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        $this->flashSuccess('Application accepted successfully! The influencer has been notified.');
    }

    public function declineApplication()
    {
        if ($this->application->status !== CampaignApplicationStatus::PENDING) {
            $this->flashError('This application has already been processed.');

            return;
        }

        $this->application->update([
            'status' => CampaignApplicationStatus::REJECTED,
            'rejected_at' => now(),
        ]);

        $this->flashSuccess('Application declined. The influencer has been notified.');
    }

    public function render()
    {
        return view('livewire.applications.view-application');
    }
}
