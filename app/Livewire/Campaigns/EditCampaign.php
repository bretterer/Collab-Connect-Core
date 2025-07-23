<?php

namespace App\Livewire\Campaigns;

use App\Enums\CampaignProductPlacement;
use App\Enums\CampaignSocialRequirement;
use App\Enums\CompensationType;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\HasWizardSteps;
use App\Models\Campaign;
use App\Services\CampaignService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class EditCampaign extends BaseComponent
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

    public function mount(Campaign $campaign)
    {
        // Use the policy to authorize editing
        $this->authorize('update', $campaign);

        $this->campaignId = $campaign->id;
        $this->loadCampaign();
    }

    public function updated($propertyName)
    {
        $this->hasUnsavedChanges = true;
        $this->autoSave();
    }

    public function autoSave()
    {
        if ($this->campaignId) {
            $this->saveDraft();
        }
    }

    public function loadCampaign()
    {
        if (!$this->campaignId) {
            return;
        }

        $campaign = Campaign::find($this->campaignId);
        if (!$campaign) {
            return;
        }

        $this->campaignGoal = $campaign->campaign_goal;
        $this->campaignType = $campaign->campaign_type->value;
        $this->targetZipCode = $campaign->target_zip_code ?? '';
        $this->targetArea = $campaign->target_area ?? '';
        $this->campaignDescription = $campaign->campaign_description;
        $this->socialRequirements = $campaign->social_requirements ?? [];
        $this->placementRequirements = $campaign->placement_requirements ?? [];
        $this->compensationType = $campaign->compensation_type->value;
        $this->compensationAmount = $campaign->compensation_amount ?? 0;
        $this->compensationDescription = $campaign->compensation_description ?? '';
        $this->compensationDetails = $campaign->compensation_details ?? [];
        $this->influencerCount = $campaign->influencer_count;
        $this->applicationDeadline = $campaign->application_deadline ? $campaign->application_deadline->format('Y-m-d') : '';
        $this->campaignCompletionDate = $campaign->campaign_completion_date ? $campaign->campaign_completion_date->format('Y-m-d') : '';
        $this->additionalRequirements = $campaign->additional_requirements ?? '';
        $this->publishAction = $campaign->status->value;
        $this->scheduledDate = $campaign->scheduled_date ? $campaign->scheduled_date->format('Y-m-d') : '';
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        $this->setCurrentStep($this->getCurrentStep() + 1);
    }

    public function previousStep()
    {
        $this->setCurrentStep($this->getCurrentStep() - 1);
    }

    protected function validateCurrentStep(): void
    {
        switch ($this->getCurrentStep()) {
            case 1:
                $this->validate([
                    'campaignGoal' => 'required|string|max:255',
                    'campaignType' => 'required|string',
                    'targetZipCode' => 'nullable|string|max:10',
                    'targetArea' => 'nullable|string|max:255',
                ]);
                break;
            case 2:
                $this->validate([
                    'campaignDescription' => 'required|string|max:1000',
                    'socialRequirements' => 'array',
                    'placementRequirements' => 'array',
                ]);
                break;
            case 3:
                $this->validate([
                    'compensationType' => 'required|string',
                    'compensationAmount' => 'required_if:compensationType,monetary|integer|min:0',
                    'influencerCount' => 'required|integer|min:1',
                    'applicationDeadline' => 'nullable|date|after:today',
                    'campaignCompletionDate' => 'nullable|date|after:applicationDeadline',
                ]);
                break;
        }
    }

    public function getCampaignTypeOptions(): array
    {
        return \App\Enums\CampaignType::toOptions();
    }

    public function getSocialRequirementsOptions(): array
    {
        return \App\Enums\CampaignSocialRequirement::toOptions();
    }

    public function getPlacementRequirementsOptions(): array
    {
        return \App\Enums\CampaignProductPlacement::toOptions();
    }

    public function getCompensationTypeOptions(): array
    {
        return \App\Enums\CompensationType::toOptions();
    }

    public function isEditing(): bool
    {
        return $this->campaignId !== null;
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= $this->getTotalSteps()) {
            $this->setCurrentStep($step);
        }
    }

    public function publishCampaign()
    {
        $this->validate([
            'campaignGoal' => 'required|string|max:255',
            'campaignType' => 'required|string',
            'campaignDescription' => 'required|string|max:1000',
            'compensationType' => 'required|string',
            'compensationAmount' => 'required_if:compensationType,monetary|integer|min:0',
            'influencerCount' => 'required|integer|min:1',
        ]);

        $campaignService = app(CampaignService::class);

        $campaignData = [
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

        $campaign = $campaignService->updateCampaign($this->campaignId, $campaignData);

        $this->hasUnsavedChanges = false;
        $this->lastSavedAt = now()->format('M j, Y g:i A');

        session()->flash('message', 'Campaign updated successfully!');

        return redirect()->route('campaigns.show', $campaign->id);
    }

    public function saveDraft()
    {
        $campaignService = app(CampaignService::class);

        $campaignData = [
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
            'publish_action' => 'draft',
            'scheduled_date' => $this->scheduledDate,
        ];

        $campaign = $campaignService->updateCampaign($this->campaignId, $campaignData);

        $this->hasUnsavedChanges = false;
        $this->lastSavedAt = now()->format('M j, Y g:i A');
    }

    public function saveAndExit()
    {
        $this->saveDraft();
        return redirect()->route('campaigns.index');
    }

    public function unscheduleCampaign()
    {
        $campaignService = app(CampaignService::class);
        $campaignService->unscheduleCampaign($this->campaignId);

        session()->flash('message', 'Campaign unscheduled successfully!');

        return redirect()->route('campaigns.show', $this->campaignId);
    }

    public function completeOnboarding(): void
    {
        // This method is required by the trait but not used in this context
    }

    #[On('beforeunload')]
    public function handleBeforeUnload()
    {
        if ($this->hasUnsavedChanges) {
            $this->dispatch('show-unsaved-changes-warning');
        }
    }

    public function render()
    {
        return view('livewire.campaigns.create-campaign');
    }
}