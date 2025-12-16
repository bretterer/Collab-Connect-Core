<?php

namespace App\Livewire\Campaigns;

use App\Enums\CampaignApplicationStatus;
use App\Events\CampaignApplicationSubmitted;
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

    public string $buttonVariant = 'primary';

    public ?CampaignApplication $existingApplication = null;

    public function mount(
        Campaign $campaign,
        string $buttonText = 'Apply Now',
        string $buttonVariant = 'primary',
        mixed $existingApplication = null,
        bool $applicationPreloaded = false
    ) {
        // Only load business if not already loaded
        $this->campaign = $campaign->relationLoaded('business')
            ? $campaign
            : $campaign->load(['business']);

        $this->buttonText = $buttonText;
        $this->buttonVariant = $buttonVariant;

        // Handle the existing application - could be a model instance or null
        if ($existingApplication instanceof CampaignApplication) {
            $this->existingApplication = $existingApplication;
        } elseif ($applicationPreloaded) {
            // Data was pre-loaded, null means no application exists
            $this->existingApplication = null;
        } else {
            // Query if not provided (fallback for other usages)
            $this->existingApplication = CampaignApplication::where('campaign_id', $this->campaign->id)
                ->where('user_id', Auth::id())
                ->first();
        }
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
        // Authorize using policy - ensures user is an influencer and campaign is published
        $this->authorize('apply', $this->campaign);

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

        CampaignApplicationSubmitted::dispatch($this->campaign, $user, $application);

        $this->campaign->business->owner->each(fn ($owner) => $owner->notify(new \App\Notifications\CampaignApplicationSubmittedNotification($application)));

        // Update the existing application property
        $this->existingApplication = $application;

        $this->flashSuccess('Your application has been submitted successfully!');
        $this->closeModal();

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
