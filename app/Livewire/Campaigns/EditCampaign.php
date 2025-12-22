<?php

namespace App\Livewire\Campaigns;

use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Enums\DeliverableType;
use App\Enums\SuccessMetric;
use App\Enums\TargetPlatform;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\HasWizardSteps;
use App\Models\Campaign;
use App\Services\CampaignService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class EditCampaign extends BaseComponent
{
    use HasWizardSteps;

    // Step 1: Campaign Goal & Type
    public string $campaignGoal = '';

    public array $campaignType = [];

    public string $targetZipCode = '';

    public string $projectName = '';

    // Step 2: Brand & Briefing
    public string $campaignObjective = '';

    public string $keyInsights = '';

    public string $fanMotivator = '';

    public string $creativeConnection = '';

    public string $brandOverview = '';

    public string $brandStory = '';

    public string $currentAdvertisingCampaign = '';

    public string $specificProducts = '';

    public string $postingRestrictions = '';

    public string $additionalConsiderations = '';

    // Step 3: Deliverables & Settings
    public array $targetPlatforms = [];

    public array $deliverables = [];

    public array $successMetrics = [];

    public string $compensationType = 'monetary';

    public string $compensationDescription = '';

    public int $influencerCount = 1;

    public int $exclusivityPeriod = 0;

    public string $applicationDeadline = '';

    public string $campaignStartDate = '';

    public string $campaignCompletionDate = '';

    public string $additionalRequirements = '';

    // Step 4: Review & Publish
    public string $publishAction = 'publish';

    public string $scheduledDate = '';

    // Campaign state
    public ?int $campaignId = null;

    public bool $hasUnsavedChanges = false;

    public string $lastSavedAt = '';

    public bool $hasAppliedDefaults = false;

    public bool $canSkipBrandStep = false;

    public array $visitedSteps = [];

    public function getTotalSteps(): int
    {
        return 4;
    }

    protected function getWizardSteps(): array
    {
        return [
            1 => 'Campaign Goal',
            2 => 'Brand & Briefing',
            3 => 'Deliverables & Settings',
            4 => 'Review & Publish',
        ];
    }

    public function mount($campaign = null)
    {
        // Handle both route model binding (Campaign object) and manual ID passing
        if ($campaign instanceof Campaign) {
            // Editing an existing campaign (passed as model)
            $this->authorize('update', $campaign);
            $this->campaignId = $campaign->id;
            $this->loadCampaign();
        } elseif ($campaign !== null) {
            // Campaign ID passed as integer/string - resolve it
            $campaignModel = Campaign::findOrFail($campaign);
            $this->authorize('update', $campaignModel);
            $this->campaignId = $campaignModel->id;
            $this->loadCampaign();
        } else {
            // Creating a new campaign
            $this->authorize('create', Campaign::class);
            $this->applyBusinessDefaults();
        }

        // Handle URL step parameter
        $step = request()->query('step');
        if ($step && is_numeric($step) && $step >= 1 && $step <= $this->getTotalSteps()) {
            $this->currentStep = (int) $step;
        }
    }

    protected function applyBusinessDefaults(): void
    {
        $user = $this->getAuthenticatedUser();
        $business = $user->currentBusiness;

        if (! $business) {
            return;
        }

        $defaults = $business->getCampaignDefaults();

        // Apply target zip code from business location
        if (! empty($business->postal_code)) {
            $this->targetZipCode = $business->postal_code;
        }

        if (empty($defaults)) {
            return;
        }

        // Apply brand information defaults
        if (! empty($defaults['brand_overview'])) {
            $this->brandOverview = $defaults['brand_overview'];
            $this->hasAppliedDefaults = true;
        }
        if (! empty($defaults['current_advertising_campaign'])) {
            $this->currentAdvertisingCampaign = $defaults['current_advertising_campaign'];
        }
        if (! empty($defaults['brand_story'])) {
            $this->brandStory = $defaults['brand_story'];
        }

        // Apply briefing defaults
        if (! empty($defaults['default_key_insights'])) {
            $this->keyInsights = $defaults['default_key_insights'];
        }
        if (! empty($defaults['default_fan_motivator'])) {
            $this->fanMotivator = $defaults['default_fan_motivator'];
        }
        if (! empty($defaults['default_posting_restrictions'])) {
            $this->postingRestrictions = $defaults['default_posting_restrictions'];
        }

        // Check if brand step can be skipped
        $this->canSkipBrandStep = $business->hasBrandDefaults();
    }

    public function skipStep(): void
    {
        // Only allow skipping step 2 if we have defaults
        if ($this->currentStep === 2 && $this->canSkipBrandStep) {
            $this->saveDraft();
            $this->currentStep = 3;
            $this->updateUrl();
        }
    }

    public function canSkipCurrentStep(): bool
    {
        return $this->currentStep === 2 && $this->canSkipBrandStep && ! empty($this->brandOverview);
    }

    public function updated($propertyName)
    {
        $this->hasUnsavedChanges = true;
        $this->autoSave();
    }

    public function autoSave()
    {
        if ($this->campaignId) {
            // Update existing campaign
            $this->saveDraft();
        } else {
            // Create new campaign draft
            $this->createDraft();
        }
    }

    /**
     * Build campaign data array from component properties
     */
    protected function buildCampaignData(string $publishAction = 'draft'): array
    {
        return [
            'current_step' => $this->currentStep,
            'project_name' => $this->projectName,
            'campaign_goal' => $this->campaignGoal,
            'campaign_type' => $this->campaignType,
            'target_zip_code' => $this->targetZipCode,
            'campaign_objective' => $this->campaignObjective,
            'key_insights' => $this->keyInsights,
            'fan_motivator' => $this->fanMotivator,
            'creative_connection' => $this->creativeConnection,
            'brand_overview' => $this->brandOverview,
            'brand_story' => $this->brandStory,
            'current_advertising_campaign' => $this->currentAdvertisingCampaign,
            'specific_products' => $this->specificProducts,
            'posting_restrictions' => $this->postingRestrictions,
            'additional_considerations' => $this->additionalConsiderations,
            'target_platforms' => $this->targetPlatforms ?: [],
            'deliverables' => $this->deliverables ?: [],
            'success_metrics' => $this->successMetrics ?: [],
            'compensation_type' => $this->compensationType,
            'compensation_description' => $this->compensationDescription,
            'influencer_count' => $this->influencerCount,
            'exclusivity_period' => $this->exclusivityPeriod,
            'application_deadline' => $this->applicationDeadline ?: null,
            'campaign_start_date' => $this->campaignStartDate ?: null,
            'campaign_completion_date' => $this->campaignCompletionDate ?: null,
            'additional_requirements' => $this->additionalRequirements,
            'publish_action' => $publishAction,
            'scheduled_date' => $this->scheduledDate ?: null,
        ];
    }

    protected function createDraft(): void
    {
        $campaignData = $this->buildCampaignData();
        $campaignData['campaign_id'] = null;

        $campaign = CampaignService::saveDraft($this->getAuthenticatedUser(), $campaignData);
        $this->campaignId = $campaign->id;
        $this->markSaved();

        // Track AddToCart when campaign draft is first created
        MetaPixel::track('AddToCart', [
            'content_ids' => [$campaign->id],
            'content_name' => $this->campaignGoal ?: 'New Campaign',
            'content_type' => 'campaign_draft',
        ]);
    }

    protected function markSaved(): void
    {
        $this->lastSavedAt = now()->format('H:i:s');
        $this->hasUnsavedChanges = false;
    }

    public function loadCampaign(): void
    {
        if (! $this->campaignId) {
            return;
        }

        $campaign = Campaign::find($this->campaignId);
        if (! $campaign) {
            return;
        }

        // Restore wizard step and mark previous steps as visited
        $this->currentStep = $campaign->current_step ?? 1;
        $this->visitedSteps = range(1, $this->currentStep);

        // Step 1: Campaign Goal & Type
        $this->projectName = $campaign->project_name ?? '';
        $this->campaignGoal = $campaign->campaign_goal ?? '';
        $this->campaignType = $campaign->campaign_type
            ? $campaign->campaign_type->map(fn ($enum) => $enum->value)->toArray()
            : [];
        $this->targetZipCode = $campaign->target_zip_code ?? '';

        // Step 2: Brand & Briefing
        $this->campaignObjective = $campaign->campaign_objective ?? '';
        $this->keyInsights = $campaign->key_insights ?? '';
        $this->fanMotivator = $campaign->fan_motivator ?? '';
        $this->creativeConnection = $campaign->creative_connection ?? '';
        $this->brandOverview = $campaign->brand_overview ?? '';
        $this->brandStory = $campaign->brand_story ?? '';
        $this->currentAdvertisingCampaign = $campaign->current_advertising_campaign ?? '';
        $this->specificProducts = $campaign->specific_products ?? '';
        $this->postingRestrictions = $campaign->posting_restrictions ?? '';
        $this->additionalConsiderations = $campaign->additional_considerations ?? '';

        // Step 3: Deliverables & Settings
        $this->targetPlatforms = $campaign->target_platforms ?? [];
        $this->deliverables = $campaign->deliverables ?? [];
        $this->successMetrics = $campaign->success_metrics ?? [];
        $this->compensationType = $campaign->compensation_type->value;
        $this->compensationDescription = $campaign->compensation_description ?? '';
        $this->influencerCount = $campaign->influencer_count ?? 1;
        $this->exclusivityPeriod = $campaign->exclusivity_period ?? 0;
        $this->applicationDeadline = $campaign->application_deadline?->format('Y-m-d') ?? '';
        $this->campaignStartDate = $campaign->campaign_start_date?->format('Y-m-d') ?? '';
        $this->campaignCompletionDate = $campaign->campaign_completion_date?->format('Y-m-d') ?? '';
        $this->additionalRequirements = $campaign->additional_requirements ?? '';

        // Step 4: Review & Publish
        $this->publishAction = $campaign->status->value;
        $this->scheduledDate = $campaign->scheduled_date?->format('Y-m-d') ?? '';
    }

    protected function validateCurrentStep(): void
    {
        $rules = $this->getStepValidationRules($this->currentStep);

        if (! empty($rules)) {
            $this->validate($rules);
        }
    }

    /**
     * Validate all steps before publishing
     */
    protected function validateAllSteps(): void
    {
        $allRules = [];

        for ($step = 1; $step < $this->getTotalSteps(); $step++) {
            $allRules = array_merge($allRules, $this->getStepValidationRules($step));
        }

        $this->validate($allRules);
    }

    public function getCampaignTypeOptions(): array
    {
        return CampaignType::toOptions();
    }

    public function getCompensationTypeOptions(): array
    {
        return CompensationType::toOptions();
    }

    public function getTargetPlatformOptions(): array
    {
        return TargetPlatform::toOptions();
    }

    public function getDeliverableTypeOptions(): array
    {
        return DeliverableType::toOptions();
    }

    public function getSuccessMetricOptions(): array
    {
        return SuccessMetric::toOptions();
    }

    public function isEditing(): bool
    {
        return $this->campaignId !== null;
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= $this->getTotalSteps()) {
            $this->currentStep = $step;
            $this->markStepVisited($step);
            $this->saveDraft();
            $this->updateUrl();
        }
    }

    /**
     * Mark a step as visited
     */
    protected function markStepVisited(int $step): void
    {
        if (! in_array($step, $this->visitedSteps)) {
            $this->visitedSteps[] = $step;
        }
    }

    /**
     * Check if a specific step has validation errors (without triggering validation messages)
     * Only returns true for steps that have been visited
     */
    public function stepHasErrors(int $step): bool
    {
        // Don't show errors for steps not yet visited
        if (! in_array($step, $this->visitedSteps)) {
            return false;
        }

        return ! empty($this->getStepErrors($step));
    }

    /**
     * Get validation errors for a specific step without displaying them
     * Only returns errors for steps that have been visited
     */
    public function getStepErrors(int $step): array
    {
        // Don't return errors for unvisited steps
        if (! in_array($step, $this->visitedSteps)) {
            return [];
        }

        $rules = $this->getStepValidationRules($step);

        if (empty($rules)) {
            return [];
        }

        $validator = \Illuminate\Support\Facades\Validator::make(
            $this->all(),
            $rules
        );

        return $validator->errors()->toArray();
    }

    /**
     * Get all steps that have validation errors
     */
    public function getStepsWithErrors(): array
    {
        $stepsWithErrors = [];

        for ($step = 1; $step < $this->getTotalSteps(); $step++) {
            if ($this->stepHasErrors($step)) {
                $stepsWithErrors[] = $step;
            }
        }

        return $stepsWithErrors;
    }

    /**
     * Get validation rules for a specific step
     */
    protected function getStepValidationRules(int $step): array
    {
        return match ($step) {
            1 => [
                'campaignGoal' => 'required|min:10',
                'campaignType' => 'required|array|min:1',
                'targetZipCode' => 'required|regex:/^\d{5}$/',
            ],
            2 => [
                'campaignObjective' => 'required|min:20',
                'keyInsights' => 'required|min:20',
                'fanMotivator' => 'required|min:20',
            ],
            3 => [
                'compensationType' => 'required|in:monetary,free_product,discount,gift_card,experience,other',
                'compensationDescription' => 'required|string|min:10',
                'influencerCount' => 'required|integer|min:1|max:50',
                'applicationDeadline' => 'required|date|after:today',
                'campaignStartDate' => 'required|date|after:applicationDeadline',
                'campaignCompletionDate' => 'required|date|after:campaignStartDate',
            ],
            default => [],
        };
    }

    protected function updateUrl()
    {
        $url = request()->url();
        $query = request()->query();
        $query['step'] = $this->currentStep;

        $this->dispatch('url-updated', url: $url.'?'.http_build_query($query));
    }

    public function nextStep()
    {
        // Mark current step as visited before validation (so errors show)
        $this->markStepVisited($this->currentStep);
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
        $this->validateAllSteps();

        if ($this->publishAction === 'schedule') {
            $this->validate(['scheduledDate' => 'required|date|after:today']);
        }

        $campaignData = $this->buildCampaignData($this->publishAction);
        $campaign = CampaignService::updateCampaign($this->campaignId, $campaignData);
        $this->markSaved();

        if ($this->publishAction === 'publish') {
            CampaignService::publishCampaign($campaign);
            session()->flash('message', 'Campaign published successfully!');
        } else {
            CampaignService::scheduleCampaign($campaign, $this->scheduledDate);

            // Track Schedule event when campaign is scheduled
            MetaPixel::track('Schedule', [
                'content_ids' => [$campaign->id],
                'content_name' => $this->campaignGoal,
            ]);

            session()->flash('message', 'Campaign scheduled successfully!');
        }

        return redirect()->route('campaigns.show', $campaign->id);
    }

    public function saveDraft(): void
    {
        $campaignData = $this->buildCampaignData();
        CampaignService::updateCampaign($this->campaignId, $campaignData);
        $this->markSaved();
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
