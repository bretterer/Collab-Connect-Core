<?php

namespace App\Livewire\Campaigns;

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

    public array $campaignType = [];

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
            6 => 'Review & Publish',
        ];
    }

    public function mount($campaignId = null)
    {
        // Use the policy to authorize creation
        $this->authorize('create', Campaign::class);

        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if (! $campaign) {
                abort(404, 'Campaign not found.');
            }
            $this->campaignId = $campaign->id;

            // Use the policy to authorize editing
            $this->authorize('update', $campaign);

        }

        // Load campaign data
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
            'target_platforms' => ! empty($this->targetPlatforms) ? $this->targetPlatforms : [],
            'deliverables' => ! empty($this->deliverables) ? $this->deliverables : [],
            'success_metrics' => ! empty($this->successMetrics) ? $this->successMetrics : [],
            'timing_details' => $this->timingDetails,
            // Enhanced Campaign Structure
            'target_audience' => $this->targetAudience,
            'content_guidelines' => $this->contentGuidelines,
            'brand_guidelines' => $this->brandGuidelines,
            'main_contact' => $this->mainContact,
            'project_name' => $this->projectName,
        ];

        $campaign = CampaignService::saveDraft($this->getAuthenticatedUser(), $campaignData);
        $this->campaignId = $campaign->id;
        $this->lastSavedAt = now()->format('H:i:s');
        $this->hasUnsavedChanges = false;
    }

    public function loadCampaign()
    {
        if (! $this->campaignId) {
            return;
        }

        $campaign = Campaign::find($this->campaignId);
        if (! $campaign) {
            return;
        }

        // Use the policy to authorize editing
        $this->authorize('update', $campaign);

        $this->campaignGoal = $campaign->campaign_goal;
        $this->campaignType = $campaign->campaign_type ? $campaign->campaign_type->map(fn ($enum) => $enum->value)->toArray() : [];
        $this->targetZipCode = $campaign->target_zip_code ?? '';
        $this->targetArea = $campaign->target_area ?? '';
        $this->campaignDescription = $campaign->campaign_description;
        $this->socialRequirements = $campaign->social_requirements ?? [];
        $this->placementRequirements = $campaign->placement_requirements ?? [];
        $this->compensationType = $campaign->compensation_type?->value ?? 'monetary';
        $this->compensationAmount = $campaign->compensation_amount ?? 0;
        $this->compensationDescription = $campaign->compensation_description ?? '';
        $this->compensationDetails = $campaign->compensation_details ?? [];
        $this->influencerCount = $campaign->influencer_count;
        $this->exclusivityPeriod = $campaign->exclusivity_period ?? 0; // New field for exclusivity period
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

    public function nextStep()
    {
        $this->autoSave();
        $this->validateCurrentStep();
        if ($this->currentStep < $this->getTotalSteps()) {
            $this->currentStep++;
            $this->updateUrl();
        }
    }

    public function previousStep()
    {
        $this->autoSave();
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->updateUrl();
        }
    }

    protected function updateUrl()
    {
        $url = request()->url();
        $query = request()->query();
        $query['step'] = $this->currentStep;

        $this->dispatch('url-updated', url: $url.'?'.http_build_query($query));
    }

    protected function validateCurrentStep(): void
    {
        // Only validate the current step's fields
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'campaignGoal' => 'required|min:10',
                    'campaignType' => 'required|array|min:1',
                    'targetZipCode' => 'required|regex:/^\d{5}$/',
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
                    'compensationType' => 'required|in:monetary,barter,free_product,discount,gift_card,experience,other',
                    'compensationDescription' => 'required|string|min:10',
                    'influencerCount' => 'required|integer|min:1|max:50',
                    'exclusivityPeriod' => 'nullable|integer|min:0', // New field for exclusivity period
                    'applicationDeadline' => 'required|date|after:today',
                    'campaignCompletionDate' => 'required|date|after:applicationDeadline',
                ]);
                break;
            case 6:
                // Step 6 is review only, no validation needed
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
        // Only allow going to completed steps or the next step
        if ($step <= $this->currentStep + 1 && $step >= 1 && $step <= $this->getTotalSteps()) {
            $this->currentStep = $step;
            $this->autoSave();
            $this->updateUrl();
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
        if (! $this->campaignId) {
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
        return view('livewire.campaigns.create-campaign');
    }
}
