<?php

namespace App\Livewire\Onboarding;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompanySize;
use App\Enums\ContactRole;
use App\Enums\YearsInBusiness;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\StripeProduct;
use App\Rules\UniqueUsername;
use App\Settings\SubscriptionSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.onboarding')]
class BusinessOnboarding extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public ?Business $business = null;

    public bool $isNavigationDisabled = false;

    // Form data properties
    public string $username = '';

    public string $businessName = '';

    public string $businessEmail = '';

    public string $phoneNumber = '';

    public string $website = '';

    public string $contactName = '';

    public string $contactRole = '';

    public string $yearsInBusiness = '';

    public string $companySize = '';

    public ?string $businessType = null;

    public ?string $industry = null;

    public string $businessDescription = '';

    public string $uniqueValueProposition = '';

    public string $instagramHandle = '';

    public string $facebookHandle = '';

    public string $tiktokHandle = '';

    public string $linkedinHandle = '';

    public string $city = '';

    public string $state = '';

    public string $postalCode = '';

    public array $businessGoals = [];

    public array $platforms = [];

    public bool $emailNotifications = true;

    public bool $marketingEmails = false;

    public ?int $selectedPriceId = null;

    // Branding uploads
    public $businessLogo = null;

    public $businessBanner = null;

    // Add this method for debugging
    public function updatedBusinessGoals()
    {
        // This will help ensure the array updates are tracked
        $this->businessGoals = array_values($this->businessGoals);
    }

    public function updatedPlatforms()
    {
        // This will help ensure the array updates are tracked
        $this->platforms = array_values($this->platforms);
    }

    protected array $stepConfiguration = [
        1 => [
            'title' => 'Basic Business Information',
            'component' => 'step1',
            'fields' => ['username', 'businessName', 'businessEmail', 'phoneNumber', 'website', 'contactName', 'contactRole', 'yearsInBusiness', 'companySize'],
            'tips' => [
                'Your username creates your unique profile URL',
                'Complete your business profile to attract quality influencers',
                'Clear business goals help us recommend the right matches',
            ],
        ],
        2 => [
            'title' => 'Business Profile & Identity',
            'component' => 'step2',
            'fields' => ['businessType', 'industry', 'businessDescription', 'uniqueValueProposition'],
            'tips' => [
                'Showcase your brand\'s personality through visuals',
                'Highlight unique selling points to stand out',
                'Consistent branding builds trust with influencers',
            ],
        ],
        3 => [
            'title' => 'Branding',
            'component' => 'step3',
            'fields' => ['businessLogo', 'businessBanner'],
            'tips' => [
                'Upload your logo to help influencers recognize your brand',
                'A professional banner image creates a strong first impression',
                'Images help build trust and credibility with creators',
            ],
        ],
        4 => [
            'title' => 'Platform Preferences & Goals',
            'component' => 'step4',
            'fields' => ['city', 'state', 'postalCode', 'businessGoals', 'platforms'],
            'tips' => [
                'Choose platforms that align with your target audience',
                'Set clear goals for your influencer marketing campaigns',
                'Consider your budget and resources when selecting platforms',
            ],
        ],
        5 => [
            'title' => 'Subscription Plan',
            'component' => 'step5',
            'fields' => [],
            'tips' => [
                'Select a plan that fits your business needs and budget',
                'Consider starting with a basic plan and upgrading later',
                'Take advantage of free trials to explore features',
            ],
        ],
        6 => [
            'title' => 'Welcome to CollabConnect',
            'component' => 'step6',
            'fields' => [],
            'tips' => [
                'Your profile is complete and ready to attract influencers',
                'Start creating campaigns to connect with your perfect matches',
                'Use our analytics to track and optimize your campaign performance',
            ],
        ],
    ];

    public function mount()
    {
        $this->business = Auth::user()->currentBusiness;

        if ($this->business) {
            $this->fillBusinessData();
        }

        // Load step from cache or default to 1
        $this->step = $this->getOnboardingStep();

        // Ensure step is valid
        $this->step = max(1, min($this->step, $this->getMaxSteps()));

        $this->selectedPriceId = $this->getSubscriptionProducts()->first()?->prices->first()?->id ?? null;

    }

    private function fillBusinessData(): void
    {
        $this->username = $this->business->username ?? '';
        $this->businessName = $this->business->name ?? '';
        $this->businessEmail = $this->business->email ?? '';
        $this->phoneNumber = $this->business->phone ?? '';
        $this->website = $this->business->website ?? '';
        $this->contactName = $this->business->primary_contact ?? '';
        $this->contactRole = $this->business->contact_role ?? '';
        $this->yearsInBusiness = $this->business->maturity ?? '';
        $this->companySize = $this->business->size ?? '';
        $this->businessType = $this->business->type?->value ?? '';
        $this->industry = $this->business->industry?->value ?? '';
        $this->businessDescription = $this->business->description ?? '';
        $this->uniqueValueProposition = $this->business->selling_points ?? '';
        $this->city = $this->business->city ?? '';
        $this->state = $this->business->state ?? '';
        $this->postalCode = $this->business->postal_code ?? '';
        $this->businessGoals = $this->business->business_goals ?? [];
        $this->platforms = $this->business->platforms ?? [];
    }

    private function getOnboardingCacheKey(): string
    {
        $businessId = $this->business?->id ?? 'new';

        return 'onboarding_step_business_'.$businessId;
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
                'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername($this->business?->id, null)],
                'businessName' => 'required|string|max:255',
                'businessEmail' => 'required|email|max:255',
                'phoneNumber' => 'required|string|max:20',
                'website' => 'nullable|url|max:255',
                'contactName' => 'required|string|max:255',
                'contactRole' => ['required', ContactRole::validationRule()],
                'yearsInBusiness' => ['required', YearsInBusiness::validationRule()],
                'companySize' => ['required', CompanySize::validationRule()],
            ],
            2 => [
                'businessType' => ['required', BusinessType::validationRule()],
                'industry' => ['required', BusinessIndustry::validationRule()],
                'businessDescription' => 'required|string|max:1000',
                'uniqueValueProposition' => 'nullable|string|max:500',
            ],
            3 => [
                'businessLogo' => 'nullable|image|max:5120',
                'businessBanner' => 'nullable|image|max:5120',
            ],
            4 => [
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'postalCode' => 'nullable|string|max:20',
                'businessGoals' => 'nullable|array',
                'platforms' => 'nullable|array',
            ],
            default => []
        };
    }

    public function getValidationMessagesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'username.alpha_dash' => 'Username can only contain letters, numbers, dashes, and underscores.',
                'username.max' => 'Username cannot exceed 255 characters.',
                'businessName.required' => 'Business name is required.',
                'businessEmail.required' => 'Business email is required.',
                'businessEmail.email' => 'Please enter a valid email address.',
                'phoneNumber.required' => 'Phone number is required.',
                'website.url' => 'Please enter a valid website URL.',
                'contactName.required' => 'Contact name is required.',
                'contactRole.required' => 'Please select your role.',
                'yearsInBusiness.required' => 'Please select years in business.',
                'companySize.required' => 'Please select company size.',
            ],
            2 => [
                'businessType.required' => 'Business type is required.',
                'industry.required' => 'Industry is required.',
                'businessDescription.required' => 'Business description is required.',
                'businessDescription.max' => 'Business description cannot exceed 1000 characters.',
                'uniqueValueProposition.max' => 'Value proposition cannot exceed 500 characters.',
            ],
            3 => [
                'businessLogo.image' => 'Logo must be an image file.',
                'businessLogo.max' => 'Logo cannot exceed 5MB.',
                'businessBanner.image' => 'Banner must be an image file.',
                'businessBanner.max' => 'Banner cannot exceed 5MB.',
            ],
            4 => [
                'city.max' => 'City cannot exceed 255 characters.',
                'state.max' => 'State cannot exceed 255 characters.',
                'postalCode.max' => 'Postal code cannot exceed 20 characters.',
                'businessGoals.array' => 'Business goals must be an array.',
                'platforms.array' => 'Platforms must be an array.',
            ],
            default => []
        };
    }

    public function nextStep()
    {

        // If on step 5 (subscription), handle Stripe payment first
        if ($this->business !== null && ! $this->business->subscribed('default') && $this->step === 5) {
            if (! empty($this->selectedPriceId)) {
                // Dispatch event to create Stripe payment method
                $this->dispatch('createStripePaymentMethod');
                $this->isNavigationDisabled = true;

                // The JS will handle creating the payment method and dispatch back
                return;
            }
            // If no price selected, skip to next step
        }
        $this->dispatch('reloadStripeFromLivewire');
        $this->validateCurrentStep();
        $this->saveStepData();

        if ($this->step < $this->getMaxSteps()) {
            $this->step++;
            $this->setOnboardingStep($this->step);
        }
    }

    #[On('stripePaymentMethodCreated')]
    public function handleStripePaymentMethod($paymentMethodId)
    {
        try {
            // Create the subscription using Laravel Cashier
            $user = Auth::user();
            $business = $user->currentBusiness;
            $business->deletePaymentMethods();

            // Get the selected price
            $price = \App\Models\StripePrice::find($this->selectedPriceId);

            if (! $price) {
                $this->dispatch('stripePaymentMethodError', message: 'Invalid subscription plan selected.');

                return;
            }

            // Create subscription with Cashier
            $subscription = $business->newSubscription('default', $price->stripe_id)
                ->trialDays(app(SubscriptionSettings::class)->trialPeriodDays)
                ->create($paymentMethodId, [
                    'email' => $business->email,
                    'name' => $business->name,
                    'business_name' => $business->name,
                    'individual_name' => $user->name,
                ]);

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
        $this->isNavigationDisabled = false;
        // Error is already displayed in the Stripe form
        // Just log it or handle additional error logic if needed
        logger()->error('Stripe payment error: '.$message);
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
                $this->saveStep3Branding();
                break;

            case 4:
                $this->saveStep4();
                break;
        }
    }

    private function saveStep1(): void
    {
        if ($this->business === null) {
            $this->business = $this->createBusiness();
            $this->clearOnboardingStep();
            Auth::user()->setCurrentBusiness($this->business);
        } else {
            $this->business->update([
                'username' => $this->username ?: null,
                'name' => $this->businessName,
                'email' => $this->businessEmail,
                'phone' => $this->phoneNumber,
                'website' => $this->website,
                'primary_contact' => $this->contactName,
                'contact_role' => $this->contactRole,
                'maturity' => $this->yearsInBusiness,
                'size' => $this->companySize,
            ]);
        }
    }

    private function saveStep2(): void
    {
        $this->business->update([
            'type' => (! empty($this->businessType)) ? BusinessType::from($this->businessType) : null,
            'industry' => (! empty($this->industry)) ? BusinessIndustry::from($this->industry) : null,
            'description' => $this->businessDescription,
            'selling_points' => $this->uniqueValueProposition,
        ]);
    }

    private function saveStep3Branding(): void
    {
        if ($this->businessLogo) {
            $this->business->clearMediaCollection('logo');
            $this->business->addMedia($this->businessLogo->getRealPath())
                ->usingName('Business Logo')
                ->usingFileName($this->businessLogo->getClientOriginalName())
                ->toMediaCollection('logo');
        }

        if ($this->businessBanner) {
            $this->business->clearMediaCollection('banner_image');
            $this->business->addMedia($this->businessBanner->getRealPath())
                ->usingName('Business Banner')
                ->usingFileName($this->businessBanner->getClientOriginalName())
                ->toMediaCollection('banner_image');
        }
    }

    private function saveStep4(): void
    {
        $this->business->update([
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'business_goals' => $this->businessGoals,
            'platforms' => $this->platforms,
        ]);
    }

    private function createBusiness(): Business
    {
        if (! $this->business) {
            // Create business with all data in one go
            $this->business = Business::create([
                'username' => $this->username ?: null,
                'name' => $this->businessName ?: 'New Business',
                'email' => $this->businessEmail ?: Auth::user()->email,
                'phone' => $this->phoneNumber,
                'website' => $this->website,
                'primary_contact' => $this->contactName ?: Auth::user()->name,
                'contact_role' => $this->contactRole,
                'maturity' => $this->yearsInBusiness,
                'size' => $this->companySize,
            ]);

            // Create the user-business link
            BusinessUser::create([
                'business_id' => $this->business->id,
                'user_id' => Auth::id(),
                'role' => 'owner',
            ]);
        }

        return $this->business;
    }

    public function completeOnboarding()
    {
        $this->validateCurrentStep();
        $this->saveStepData();

        // Mark onboarding as complete
        Auth::user()->currentBusiness()->update(['onboarding_complete' => true]);

        // Clear the cached step since onboarding is complete
        $this->clearOnboardingStep();

        session()->flash('success', 'Welcome to CollabConnect! Your business profile is now complete.');

        return redirect()->route('dashboard');
    }

    public function getSubscriptionProducts()
    {
        return StripeProduct::query()
            ->where('active', true)
            ->where('billable_type', 'App\Models\Business')
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

    public function render()
    {
        return view('livewire.onboarding.business-onboarding', [
            'currentStepData' => $this->getCurrentStepData(),
            'steps' => $this->stepConfiguration,
            'currentStep' => $this->step,
            'maxSteps' => $this->getMaxSteps(),
            'subscriptionProducts' => $this->getSubscriptionProducts(),
            'trialPeriodDays' => app(SubscriptionSettings::class)->trialPeriodDays,
        ]);
    }
}
