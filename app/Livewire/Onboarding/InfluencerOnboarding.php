<?php

namespace App\Livewire\Onboarding;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompensationType;
use App\Enums\SocialPlatform;
use App\Models\Influencer;
use App\Models\InfluencerSocial;
use App\Models\StripeProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.onboarding')]
class InfluencerOnboarding extends Component
{
    public int $step = 1;

    public ?Influencer $influencer = null;

    // Step 1: Basic Info
    public string $bio = '';

    // Step 2: Social Media Accounts
    public array $socialAccounts = [];

    // Step 3: Content & Business Preferences
    public array $contentTypes = [];

    public array $preferredBusinessTypes = [];

    // Step 4: Address Info
    public string $address = '';

    public string $city = '';

    public string $state = '';

    public string $county = '';

    public string $postalCode = '';

    public string $phoneNumber = '';

    // Step 5: Compensation & Lead Time
    public array $compensationTypes = [];

    public ?int $typicalLeadTimeDays = null;

    // Step 6: Subscription
    public ?int $selectedPriceId = null;

    protected array $stepConfiguration = [
        1 => [
            'title' => 'Basic Information',
            'component' => 'step1',
            'fields' => ['bio'],
            'tips' => [
                'Your name and email help businesses identify you',
                'A compelling bio attracts the right brand partnerships',
                'Keep your bio concise but engaging',
            ],
        ],
        2 => [
            'title' => 'Social Media Accounts',
            'component' => 'step2',
            'fields' => ['socialAccounts'],
            'tips' => [
                'Add all your social media platforms to increase visibility',
                'Include follower counts to help businesses find the right fit',
                'Keep your usernames accurate and up-to-date',
            ],
        ],
        3 => [
            'title' => 'Content & Business Preferences',
            'component' => 'step3',
            'fields' => ['contentTypes', 'preferredBusinessTypes'],
            'tips' => [
                'Select content types that match your expertise',
                'Choose business types you are most interested in working with',
                'Being selective helps you get better brand matches',
            ],
        ],
        4 => [
            'title' => 'Location & Contact',
            'component' => 'step4',
            'fields' => ['address', 'city', 'state', 'county', 'postalCode', 'phoneNumber'],
            'tips' => [
                'Location helps businesses find local influencers',
                'Contact information is used for campaign coordination',
                'Your privacy is protected - only matched businesses see details',
            ],
        ],
        5 => [
            'title' => 'Compensation & Timeline',
            'component' => 'step5',
            'fields' => ['compensationTypes', 'typicalLeadTimeDays'],
            'tips' => [
                'Select compensation types you prefer',
                'Lead time helps businesses plan campaigns effectively',
                'Being flexible with compensation can increase opportunities',
            ],
        ],
        6 => [
            'title' => 'Subscription Plan',
            'component' => 'step6',
            'fields' => [],
            'tips' => [
                'Choose a plan that fits your collaboration needs',
                'Consider the features offered in each subscription tier',
                'Upgrading your plan can unlock more opportunities',
            ],
        ],
        7 => [
            'title' => 'Welcome to CollabConnect',
            'component' => 'step7',
            'fields' => [],
            'tips' => [
                'Your profile is complete and ready to attract brands',
                'Browse campaigns to find exciting collaboration opportunities',
                'Build meaningful partnerships with businesses in your niche',
            ],
        ],
    ];

    public function mount()
    {
        $this->influencer = Auth::user()->influencer;

        if ($this->influencer) {
            $this->fillInfluencerData();
        }

        // Initialize social accounts array with platform options
        if (empty($this->socialAccounts)) {
            foreach (SocialPlatform::cases() as $platform) {
                $this->socialAccounts[$platform->value] = [
                    'platform' => $platform->value,
                    'username' => '',
                    'followers' => null,
                ];
            }
        }

        // Load step from cache or default to 1
        $this->step = $this->getOnboardingStep();

        // Ensure step is valid
        $this->step = max(1, min($this->step, $this->getMaxSteps()));
    }

    private function fillInfluencerData(): void
    {
        $this->bio = $this->influencer->bio ?? '';
        $this->address = $this->influencer->address ?? '';
        $this->city = $this->influencer->city ?? '';
        $this->state = $this->influencer->state ?? '';
        $this->county = $this->influencer->county ?? '';
        $this->postalCode = $this->influencer->postal_code ?? '';
        $this->phoneNumber = $this->influencer->phone_number ?? '';
        $this->contentTypes = $this->influencer->content_types ?? [];
        $this->preferredBusinessTypes = $this->influencer->preferred_business_types ?? [];
        $this->compensationTypes = $this->influencer->compensation_types ?? [];
        $this->typicalLeadTimeDays = $this->influencer->typical_lead_time_days;

        // Load social accounts
        if ($this->influencer->socialAccounts) {
            foreach ($this->influencer->socialAccounts as $account) {
                $this->socialAccounts[$account->platform->value] = [
                    'platform' => $account->platform->value,
                    'username' => $account->username,
                    'followers' => $account->followers,
                ];
            }
        }
    }

    private function getOnboardingCacheKey(): string
    {
        $influencerId = $this->influencer?->id ?? 'new';

        return 'onboarding_step_influencer_'.$influencerId;
    }

    private function getOnboardingStep(): int
    {
        return Cache::get($this->getOnboardingCacheKey(), $this->step ?? 1);
    }

    private function setOnboardingStep(int $step): void
    {
        Cache::put($this->getOnboardingCacheKey(), $step, now()->addHours(24));
    }

    private function clearOnboardingStep(): void
    {
        Cache::forget($this->getOnboardingCacheKey());
    }

    public function getCurrentStepData()
    {
        return $this->stepConfiguration[$this->step] ?? $this->stepConfiguration[1];
    }

    public function getMaxSteps()
    {
        return count($this->stepConfiguration);
    }

    public function validateCurrentStep()
    {
        $rules = $this->getValidationRulesForStep($this->step);

        if (! empty($rules)) {
            $this->validate($rules, $this->getValidationMessagesForStep($this->step));
        }
    }

    public function getValidationRulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'bio' => 'required|string|max:500',
            ],
            2 => [
                'socialAccounts' => 'array',
            ],
            3 => [
                'contentTypes' => 'required|array|max:3',
                'contentTypes.*' => [BusinessIndustry::validationRule()],
                'preferredBusinessTypes' => 'required|array',
                'preferredBusinessTypes.*' => [BusinessType::validationRule()],
            ],
            4 => [
                'address' => 'nullable|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'county' => 'nullable|string|max:255',
                'postalCode' => 'required|string|max:20',
                'phoneNumber' => 'required|string|max:20',
            ],
            5 => [
                'compensationTypes' => 'required|array',
                'compensationTypes.*' => [CompensationType::validationRule()],
                'typicalLeadTimeDays' => 'required|integer|min:1|max:365',
            ],
            default => []
        };
    }

    public function getValidationMessagesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'bio.required' => 'Bio is required.',
                'bio.max' => 'Bio cannot exceed 500 characters.',
            ],
            3 => [
                'contentTypes.required' => 'Please select at least one content type.',
                'contentTypes.max' => 'You can select up to 3 content types.',
                'preferredBusinessTypes.required' => 'Please select at least one business type.',
                'preferredBusinessTypes.max' => 'You can select up to 2 business types.',
            ],
            4 => [
                'city.required' => 'City is required.',
                'state.required' => 'State is required.',
                'postalCode.required' => 'Postal code is required.',
                'phoneNumber.required' => 'Phone number is required.',
            ],
            5 => [
                'compensationTypes.required' => 'Please select at least one compensation type.',
                'compensationTypes.max' => 'You can select up to 3 compensation types.',
                'typicalLeadTimeDays.required' => 'Typical lead time is required.',
                'typicalLeadTimeDays.min' => 'Lead time must be at least 1 day.',
                'typicalLeadTimeDays.max' => 'Lead time cannot exceed 365 days.',
            ],
            default => []
        };
    }

    public function nextStep()
    {
        // If on step 6 (subscription), handle Stripe payment first
        if ($this->step === 6) {
            if ($this->selectedPriceId) {
                // Dispatch event to create Stripe payment method
                $this->dispatch('createStripePaymentMethod');

                // The JS will handle creating the payment method and dispatch back
                return;
            }
            // If no price selected, skip to next step
        }

        $this->validateCurrentStep();
        $this->saveStepData();

        if ($this->step < $this->getMaxSteps()) {
            $this->step++;
            $this->setOnboardingStep($this->step);
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->setOnboardingStep($this->step);
        }
    }

    public function saveStepData()
    {
        switch ($this->step) {
            case 1:
                $this->saveStep1();
                break;
            case 2:
                $this->saveStep2();
                break;
            case 3:
                $this->saveStep3();
                break;
            case 4:
                $this->saveStep4();
                break;
            case 5:
                $this->saveStep5();
                break;
        }
    }

    private function saveStep1(): void
    {
        if ($this->influencer === null) {
            $this->influencer = $this->createInfluencer();
            $this->clearOnboardingStep();
        } else {
            $this->influencer->update([
                'bio' => $this->bio,
            ]);
        }
    }

    private function saveStep2(): void
    {
        // Delete existing social accounts
        $this->influencer->socials()->delete();

        // Create new social accounts for platforms with usernames
        foreach ($this->socialAccounts as $accountData) {
            if (! empty($accountData['username'])) {
                InfluencerSocial::create([
                    'influencer_id' => $this->influencer->id,
                    'platform' => $accountData['platform'],
                    'username' => $accountData['username'],
                    'url' => SocialPlatform::from($accountData['platform'])->generateUrl($accountData['username']),
                    'followers' => $accountData['followers'] ?: null,
                ]);
            }
        }
    }

    private function saveStep3(): void
    {
        $this->influencer->update([
            'content_types' => $this->contentTypes,
            'preferred_business_types' => $this->preferredBusinessTypes,
        ]);
    }

    private function saveStep4(): void
    {
        $this->influencer->update([
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'county' => $this->county,
            'postal_code' => $this->postalCode,
            'phone_number' => $this->phoneNumber,
        ]);
    }

    private function saveStep5(): void
    {
        $this->influencer->update([
            'compensation_types' => $this->compensationTypes,
            'typical_lead_time_days' => $this->typicalLeadTimeDays,
        ]);
    }

    private function createInfluencer(): Influencer
    {
        if (! $this->influencer) {
            // Create influencer with all data in one go
            $this->influencer = Influencer::create([
                'user_id' => Auth::id(),
                'bio' => $this->bio,
            ]);

        }

        return $this->influencer;
    }

    public function completeOnboarding()
    {
        $this->validateCurrentStep();
        $this->saveStepData();

        // Mark onboarding as complete
        $this->influencer->update(['onboarding_complete' => true]);

        // Clear the cached step since onboarding is complete
        $this->clearOnboardingStep();

        session()->flash('success', 'Welcome to CollabConnect! Your influencer profile is now complete.');

        return redirect()->route('dashboard');
    }

    public function getSubscriptionProducts()
    {
        return StripeProduct::query()
            ->where('active', true)
            ->where('billable_type', 'App\Models\Influencer')
            ->with(['prices' => function ($query) {
                $query->where('active', true)
                    ->orderBy('unit_amount');
            }])
            ->get();
    }

    public function selectPrice(int $priceId)
    {
        $this->selectedPriceId = $priceId;
    }

    #[On('stripePaymentMethodCreated')]
    public function handleStripePaymentMethod($paymentMethodId)
    {
        try {
            // Create the subscription using Laravel Cashier
            $user = Auth::user();

            // Get the selected price
            $price = \App\Models\StripePrice::find($this->selectedPriceId);

            if (! $price) {
                $this->dispatch('stripePaymentMethodError', message: 'Invalid subscription plan selected.');

                return;
            }

            // Create subscription with Cashier
            $user->newSubscription('default', $price->stripe_id)
                ->create($paymentMethodId);

            // Move to next step after successful subscription
            $this->validateCurrentStep();
            $this->saveStepData();

            if ($this->step < $this->getMaxSteps()) {
                $this->step++;
                $this->setOnboardingStep($this->step);
            }
        } catch (\Exception $e) {
            $this->dispatch('stripePaymentMethodError', message: $e->getMessage());
        }
    }

    #[On('stripePaymentMethodError')]
    public function handleStripeError($message)
    {
        // Error is already displayed in the Stripe form
        // Just log it or handle additional error logic if needed
        logger()->error('Stripe payment error: '.$message);
    }

    public function render()
    {
        return view('livewire.onboarding.influencer-onboarding', [
            'currentStepData' => $this->getCurrentStepData(),
            'steps' => $this->stepConfiguration,
            'currentStep' => $this->step,
            'maxSteps' => $this->getMaxSteps(),
            'subscriptionProducts' => $this->getSubscriptionProducts(),
        ]);
    }
}
