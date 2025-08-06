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
    public int $exclusivityPeriod = 0; // New field for exclusivity period
    public string $applicationDeadline = '';
    public string $campaignCompletionDate = '';
    public string $additionalRequirements = '';

    // Step 4: Review & Publish
    public string $publishAction = 'publish'; // 'publish' or 'schedule'
    public ?string $scheduledDate = '';

    // Brand Information
    public ?string $brandOverview = '';
    public ?string $currentAdvertisingCampaign = '';
    public ?string $brandStory = '';

    // Campaign Briefing
    public ?string $campaignObjective = '';
    public ?string $keyInsights = '';
    public ?string $fanMotivator = '';
    public ?string $creativeConnection = '';
    public ?string $specificProducts = '';
    public ?string $postingRestrictions = '';
    public ?string $additionalConsiderations = '';

    // Deliverables & Success Metrics
    public array $targetPlatforms = [];
    public array $deliverables = [];
    public array $successMetrics = [];
    public ?string $timingDetails = '';

    // Enhanced Campaign Structure
    public ?string $targetAudience = '';
    public ?string $contentGuidelines = '';
    public ?string $brandGuidelines = '';
    public ?string $mainContact = '';
    public ?string $projectName = '';

    // Campaign ID for editing existing campaign
    public ?int $campaignId = null;

    // Auto-save state
    public bool $hasUnsavedChanges = false;
    public string $lastSavedAt = '';

    public function getTotalSteps(): int
    {
        return 6;
    }

    protected function getWizardSteps(): array
    {
        return [
            1 => 'Campaign Goal',
            2 => 'Brand Information',
            3 => 'Campaign Briefing',
            4 => 'Deliverables & Metrics',
            5 => 'Campaign Settings',
            6 => 'Review & Publish'
        ];
    }


    public function mount(Campaign $campaign)
    {
        // Use the policy to authorize editing
        $this->authorize('update', $campaign);

        $this->campaignId = $campaign->id;
        $this->loadCampaign();

        // Handle URL step parameter
        $step = request()->query('step');
        if ($step && is_numeric($step) && $step >= 1 && $step <= $this->getTotalSteps()) {
            $this->currentStep = (int) $step;
        }
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

        // Brand Information
        $this->brandOverview = $campaign->brand_overview ?? '';
        $this->currentAdvertisingCampaign = $campaign->current_advertising_campaign ?? '';
        $this->brandStory = $campaign->brand_story ?? '';

        // Campaign Briefing
        $this->campaignObjective = $campaign->campaign_objective ?? '';
        $this->keyInsights = $campaign->key_insights ?? '';
        $this->fanMotivator = $campaign->fan_motivator ?? '';
        $this->creativeConnection = $campaign->creative_connection ?? '';
        $this->specificProducts = $campaign->specific_products ?? '';
        $this->postingRestrictions = $campaign->posting_restrictions ?? '';
        $this->additionalConsiderations = $campaign->additional_considerations ?? '';

        // Deliverables & Success Metrics
        $this->targetPlatforms = $campaign->target_platforms ?? [];
        $this->deliverables = $campaign->deliverables ?? [];
        $this->successMetrics = $campaign->success_metrics ?? [];
        $this->timingDetails = $campaign->timing_details ?? '';

        // Enhanced Campaign Structure
        $this->targetAudience = $campaign->target_audience ?? '';
        $this->contentGuidelines = $campaign->content_guidelines ?? '';
        $this->brandGuidelines = $campaign->brand_guidelines ?? '';
        $this->mainContact = $campaign->main_contact ?? '';
        $this->projectName = $campaign->project_name ?? '';
    }

    protected function validateCurrentStep(): void
    {
        switch ($this->currentStep) {
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
                    'brandOverview' => 'required|min:20',
                ]);
                break;
            case 3:
                $this->validate([
                    'campaignObjective' => 'required|min:20',
                    'keyInsights' => 'required|min:20',
                    'fanMotivator' => 'required|min:20',
                    'creativeConnection' => 'required|min:20',
                ]);
                break;
            case 4:
                $this->validate([
                    'targetPlatforms' => 'array',
                    'deliverables' => 'array',
                    'successMetrics' => 'array',
                ]);
                break;
            case 5:
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

    public function getTargetPlatformOptions(): array
    {
        return \App\Enums\TargetPlatform::toOptions();
    }

    public function getDeliverableTypeOptions(): array
    {
        return \App\Enums\DeliverableType::toOptions();
    }

    public function getSuccessMetricOptions(): array
    {
        return \App\Enums\SuccessMetric::toOptions();
    }

    public function isEditing(): bool
    {
        return $this->campaignId !== null;
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= $this->getTotalSteps()) {
            $this->currentStep = $step;
            $this->updateUrl();
        }
    }

    protected function updateUrl()
    {
        $url = request()->url();
        $query = request()->query();
        $query['step'] = $this->currentStep;

        $this->dispatch('url-updated', url: $url . '?' . http_build_query($query));
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        $this->currentStep++;
        $this->updateUrl();
    }

    public function previousStep()
    {
        $this->currentStep--;
        $this->updateUrl();
    }

    public function publishCampaign()
    {
        $this->validate([
            'campaignGoal' => 'required|string|max:255',
            'campaignType' => 'required|string',
            'compensationType' => 'required|string',
            'compensationAmount' => 'required_if:compensationType,monetary|integer|min:0',
            'influencerCount' => 'required|integer|min:1',
            'exclusivityPeriod' => 'nullable|integer|min:0', // New field for exclusivity period
            'applicationDeadline' => 'required|date|after:today',
            'campaignCompletionDate' => 'required|date|after:applicationDeadline',

        ]);

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
            'exclusivity_period' => $this->exclusivityPeriod,
            'application_deadline' => $this->applicationDeadline,
            'campaign_completion_date' => $this->campaignCompletionDate,
            'additional_requirements' => $this->additionalRequirements,
            'publish_action' => $this->publishAction,
            'scheduled_date' => $this->scheduledDate,
            // Brand Information
            'brand_overview' => $this->brandOverview,
            'current_advertising_campaign' => $this->currentAdvertisingCampaign,
            'brand_story' => $this->brandStory,
            // Campaign Briefing
            'campaign_objective' => $this->campaignObjective,
            'key_insights' => $this->keyInsights,
            'fan_motivator' => $this->fanMotivator,
            'creative_connection' => $this->creativeConnection,
            'specific_products' => $this->specificProducts,
            'posting_restrictions' => $this->postingRestrictions,
            'additional_considerations' => $this->additionalConsiderations,
            // Deliverables & Success Metrics
            'target_platforms' => !empty($this->targetPlatforms) ? $this->targetPlatforms : [],
            'deliverables' => !empty($this->deliverables) ? $this->deliverables : [],
            'success_metrics' => !empty($this->successMetrics) ? $this->successMetrics : [],
            'timing_details' => $this->timingDetails,
            // Enhanced Campaign Structure
            'target_audience' => $this->targetAudience,
            'content_guidelines' => $this->contentGuidelines,
            'brand_guidelines' => $this->brandGuidelines,
            'main_contact' => $this->mainContact,
            'project_name' => $this->projectName,
        ];

        $campaign = CampaignService::updateCampaign($this->campaignId, $campaignData);

        $this->hasUnsavedChanges = false;
        $this->lastSavedAt = now()->format('M j, Y g:i A');

        if ($this->publishAction === 'publish') {
            CampaignService::publishCampaign($campaign);
            session()->flash('message', 'Campaign published successfully!');
        } else {
            CampaignService::scheduleCampaign($campaign, $this->scheduledDate);
            session()->flash('message', 'Campaign scheduled successfully!');
        }

        return redirect()->route('campaigns.show', $campaign->id);
    }

    public function saveDraft()
    {
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
            'exclusivity_period' => $this->exclusivityPeriod,
            'application_deadline' => $this->applicationDeadline,
            'campaign_completion_date' => $this->campaignCompletionDate,
            'additional_requirements' => $this->additionalRequirements,
            'publish_action' => 'draft',
            'scheduled_date' => $this->scheduledDate,
            // Brand Information
            'brand_overview' => $this->brandOverview,
            'current_advertising_campaign' => $this->currentAdvertisingCampaign,
            'brand_story' => $this->brandStory,
            // Campaign Briefing
            'campaign_objective' => $this->campaignObjective,
            'key_insights' => $this->keyInsights,
            'fan_motivator' => $this->fanMotivator,
            'creative_connection' => $this->creativeConnection,
            'specific_products' => $this->specificProducts,
            'posting_restrictions' => $this->postingRestrictions,
            'additional_considerations' => $this->additionalConsiderations,
            // Deliverables & Success Metrics
            'target_platforms' => !empty($this->targetPlatforms) ? $this->targetPlatforms : [],
            'deliverables' => !empty($this->deliverables) ? $this->deliverables : [],
            'success_metrics' => !empty($this->successMetrics) ? $this->successMetrics : [],
            'timing_details' => $this->timingDetails,
            // Enhanced Campaign Structure
            'target_audience' => $this->targetAudience,
            'content_guidelines' => $this->contentGuidelines,
            'brand_guidelines' => $this->brandGuidelines,
            'main_contact' => $this->mainContact,
            'project_name' => $this->projectName,
        ];

        $campaign = CampaignService::updateCampaign($this->campaignId, $campaignData);

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
        $campaign = Campaign::find($this->campaignId);
        CampaignService::unscheduleCampaign($campaign);

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