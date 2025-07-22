<?php

namespace App\Livewire\Campaigns;

use App\Enums\CampaignProductPlacement;
use App\Enums\CampaignSocialRequirement;
use App\Enums\CampaignStatus;
use App\Enums\CompensationType;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\HasWizardSteps;
use App\Models\Campaign;
use App\Services\CampaignService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class CreateCampaign extends BaseComponent
{
    use HasWizardSteps;

    // Step 1: Campaign Goal & Type
    public string $campaignGoal = '';
    public string $campaignType = '';
    public string $targetZipCode = '';
    public string $targetArea = '';

    // Step 2: Campaign Details
    public string $campaignDescription = '';
    public array $socialRequirements = [];
    public array $placementRequirements = [];

    // Step 3: Campaign Settings
    public string $compensationType = 'monetary';
    public int $compensationAmount = 0;
    public ?string $compensationDescription = '';
    public ?array $compensationDetails = [];
    public int $influencerCount = 1;
    public string $applicationDeadline = '';
    public string $campaignCompletionDate = '';
    public string $additionalRequirements = '';

    // Step 4: Review & Publish
    public string $publishAction = 'publish'; // 'publish' or 'schedule'
    public ?string $scheduledDate = '';

    // Campaign ID for editing existing campaign
    public ?int $campaignId = null;

    // Auto-save state
    public bool $hasUnsavedChanges = false;
    public string $lastSavedAt = '';

    // View mode for non-owners
    public bool $isViewMode = false;

    // Track if current user is the campaign owner
    public bool $isOwner = false;

    public function getTotalSteps(): int
    {
        return 4;
    }

    protected function getWizardSteps(): array
    {
        return [
            1 => 'Campaign Goal',
            2 => 'Campaign Details',
            3 => 'Campaign Settings',
            4 => 'Review & Publish'
        ];
    }

        public function mount($campaignId = null)
    {
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if (!$campaign) {
                abort(404, 'Campaign not found.');
            }

            // If user doesn't own the campaign, show view mode
            if ($campaign->user_id !== $this->getAuthenticatedUser()->id) {
                $this->showViewMode($campaign);
                return;
            }

            if ($campaign->status === CampaignStatus::PUBLISHED) {
                $this->showViewMode($campaign);
                return;
            }

            $this->campaignId = $campaign->id;
            $this->isOwner = true;
        }
        $this->loadCampaign();
    }

    private function showViewMode(Campaign $campaign)
    {
        $this->campaignId = $campaign->id;
        $this->isViewMode = true;
        $this->isOwner = false;
        $this->loadCampaign();
    }

    public function updated($propertyName)
    {
        $this->hasUnsavedChanges = true;
        $this->autoSave();
    }

    public function autoSave()
    {
        // Save current step data to database
        $campaignData = [
            'campaign_id' => $this->campaignId,
            'current_step' => $this->currentStep,
            'campaign_goal' => $this->campaignGoal,
            'campaign_type' => $this->campaignType,
            'target_zip_code' => $this->targetZipCode,
            'target_area' => $this->targetArea,
            'campaign_description' => $this->campaignDescription,
            'social_requirements' => $this->socialRequirements,
            'placement_requirements' => $this->placementRequirements,
            'compensation_type' => $this->compensationType,
            'compensation_amount' => $this->compensationAmount,
            'compensation_description' => $this->compensationDescription,
            'compensation_details' => $this->compensationDetails,
            'influencer_count' => $this->influencerCount,
            'application_deadline' => $this->applicationDeadline,
            'campaign_completion_date' => $this->campaignCompletionDate,
            'additional_requirements' => $this->additionalRequirements,
            'publish_action' => $this->publishAction,
            'scheduled_date' => $this->scheduledDate,
        ];

        $campaign = CampaignService::saveDraft($this->getAuthenticatedUser(), $campaignData);
        $this->campaignId = $campaign->id;
        $this->lastSavedAt = now()->format('H:i:s');
        $this->hasUnsavedChanges = false;
    }

    public function loadCampaign()
    {
        if ($this->campaignId) {
            $campaign = Campaign::where('id', $this->campaignId)->first();

            if ($campaign) {
                // In view mode, we can load any published campaign
                if ($this->isViewMode) {
                    if ($campaign->status !== \App\Enums\CampaignStatus::PUBLISHED) {
                        abort(404, 'Campaign not found.');
                    }
                } else {
                    // In edit mode, user must own the campaign
                    if ($campaign->user_id !== $this->getAuthenticatedUser()->id) {
                        $this->showViewMode($campaign);
                    }
                }

                $this->currentStep = $campaign->current_step;
                $this->campaignGoal = $campaign->campaign_goal;
                $this->campaignType = $campaign->campaign_type->value;
                $this->targetZipCode = $campaign->target_zip_code;
                $this->targetArea = $campaign->target_area;
                $this->campaignDescription = $campaign->campaign_description;
                $this->socialRequirements = $campaign->social_requirements ?? [];
                $this->placementRequirements = $campaign->placement_requirements ?? [];
                $this->compensationType = $campaign->compensation_type->value;
                $this->compensationAmount = $campaign->compensation_amount;
                $this->compensationDescription = $campaign->compensation_description;
                $this->compensationDetails = $campaign->compensation_details ?? [];
                $this->influencerCount = $campaign->influencer_count;
                $this->applicationDeadline = $campaign->application_deadline?->format('Y-m-d');
                $this->campaignCompletionDate = $campaign->campaign_completion_date?->format('Y-m-d');
                $this->additionalRequirements = $campaign->additional_requirements ?? '';
                $this->publishAction = $campaign->publish_action;
                $this->scheduledDate = $campaign->scheduled_date?->format('Y-m-d') ?? '';
            } else {
                // Campaign not found
                abort(404, 'Campaign not found.');
            }
        }
    }

    public function nextStep()
    {
        $this->autoSave();
        $this->validateCurrentStep();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        $this->autoSave();
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    protected function validateCurrentStep(): void
    {
        // Only validate the current step's fields
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'campaignGoal' => 'required|min:10',
                    'campaignType' => 'required|in:user_generated_content,social_post,product_placement,freeform',
                    'targetZipCode' => 'required|regex:/^\d{5}$/',
                ]);
                break;
            case 2:
                $this->validate([
                    'campaignDescription' => 'required|min:50',
                    'socialRequirements' => 'array|min:1',
                    'placementRequirements' => 'array',
                ]);
                break;
            case 3:
                $this->validate([
                    'compensationType' => 'required|in:monetary,barter,free_product,discount,gift_card,experience,other',
                    'compensationDescription' => 'required|string|min:10',
                    'influencerCount' => 'required|integer|min:1|max:50',
                    'applicationDeadline' => 'required|date|after:today',
                    'campaignCompletionDate' => 'required|date|after:applicationDeadline',
                ]);
                break;
            case 4:
                // Step 4 is review only, no validation needed
                break;
        }
    }

    public function getCampaignTypeOptions(): array
    {
        return [
            'user_generated_content' => 'User Generated Content',
            'social_post' => 'Social Post',
            'product_placement' => 'Product Placement',
            'freeform' => 'Freeform',
        ];
    }

    public function getSocialRequirementsOptions(): array
    {
        return collect(CampaignSocialRequirement::cases())->mapWithKeys(function ($requirement) {
            return [$requirement->value => $requirement->name()];
        })->toArray();
    }

    public function getPlacementRequirementsOptions(): array
    {
        return collect(CampaignProductPlacement::cases())->mapWithKeys(function ($placement) {
            return [$placement->value => $placement->name()];
        })->toArray();
    }

    public function getCompensationTypeOptions(): array
    {
        $options = [];
        foreach (CompensationType::cases() as $type) {
            $options[$type->value] = $type->label();
        }
        return $options;
    }

    public function isEditing(): bool
    {
        return $this->campaignId !== null;
    }

    public function goToStep(int $step)
    {
        // Only allow going to completed steps or the next step
        if ($step <= $this->currentStep + 1 && $step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->autoSave();
        }
    }

    public function publishCampaign()
    {
        $rules = [
            'publishAction' => 'required|in:publish,schedule',
        ];

        // Only validate scheduledDate if we're scheduling
        if ($this->publishAction === 'schedule') {
            $rules['scheduledDate'] = 'required|date|after:today';
        }

        $this->validate($rules);

        // Save final campaign data
        $this->autoSave();

        // Get the campaign
        $campaign = Campaign::find($this->campaignId);

        if ($this->publishAction === 'publish') {
            CampaignService::publishCampaign($campaign);
            session()->flash('message', 'Campaign published successfully!');
        } else {
            CampaignService::scheduleCampaign($campaign, $this->scheduledDate);
            session()->flash('message', 'Campaign scheduled successfully!');
        }

        return redirect()->route('dashboard');
    }

    public function saveDraft()
    {
        $this->autoSave();
        session()->flash('message', 'Draft saved successfully!');
    }

    public function saveAndExit()
    {
        $this->autoSave();
        session()->flash('message', 'Campaign draft saved successfully!');
        return redirect()->route('campaigns.index');
    }

    public function unscheduleCampaign()
    {
        if (!$this->campaignId) {
            return;
        }

        $campaign = Campaign::find($this->campaignId);

        if ($campaign && $campaign->isScheduled()) {
            CampaignService::unscheduleCampaign($campaign);
            session()->flash('message', 'Campaign unscheduled and converted to draft!');
            return redirect()->route('campaigns.index');
        }
    }

    public function completeOnboarding(): void
    {
        // This method is required by the HasWizardSteps trait
        // For campaign creation, we use publishCampaign() instead
    }

    #[On('beforeunload')]
    public function handleBeforeUnload()
    {
        if ($this->hasUnsavedChanges) {
            $this->autoSave();
        }
    }

    public function render()
    {
        if ($this->isViewMode) {
            return view('livewire.campaigns.view-campaign');
        }

        return view('livewire.campaigns.create-campaign');
    }
}