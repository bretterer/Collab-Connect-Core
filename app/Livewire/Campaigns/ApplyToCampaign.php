<?php

namespace App\Livewire\Campaigns;

use App\Enums\CampaignApplicationStatus;
use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use Illuminate\Support\Facades\Auth;

class ApplyToCampaign extends BaseComponent
{
    public Campaign $campaign;

    public string $message = '';

    public bool $showModal = false;

    public string $buttonText = 'Apply Now';

    public ?CampaignApplication $existingApplication = null;

    public function mount(Campaign $campaign, string $buttonText = 'Apply Now')
    {
        $this->campaign = $campaign->load(['compensation', 'business']);
        $this->buttonText = $buttonText;

        // Check if user already applied
        $this->existingApplication = CampaignApplication::where('campaign_id', $this->campaign->id)
            ->where('user_id', Auth::user()->id)
            ->first();
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->message = '';
    }

    public function submitApplication()
    {
        // Double-check if user already applied (in case state changed)
        if ($this->existingApplication) {
            $this->flashError('You have already applied to this campaign.');

            return;
        }

        $this->validate([
            'message' => 'required|string|min:50|max:1000',
        ]);

        $user = Auth::user();

        // Create application
        $application = CampaignApplication::create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $user->id,
            'message' => $this->message,
            'status' => CampaignApplicationStatus::PENDING,
            'submitted_at' => now(),
        ]);

        // Update the existing application property
        $this->existingApplication = $application;

        $this->flashSuccess('Your application has been submitted successfully!');
        $this->closeModal();

        // Redirect to campaign or discovery page
        $this->safeRedirect('discover');
    }

    public function getMatchScore()
    {
        // Simple placeholder match score - could be enhanced with actual scoring logic
        return 85.5;
    }

    public function render()
    {
        return view('livewire.campaigns.apply-to-campaign');
    }
}
