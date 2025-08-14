<?php

namespace App\Livewire\Onboarding;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompanySize;
use App\Enums\ContactRole;
use App\Enums\YearsInBusiness;
use App\Models\Business;
use App\Models\BusinessUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.onboarding')]
class BusinessOnboarding extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public ?Business $business = null;

    // Form data properties
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

    public string $zip = '';

    protected array $stepConfiguration = [
        1 => [
            'title' => 'Basic Business Information',
            'component' => 'step1',
            'fields' => ['businessName', 'businessEmail', 'phoneNumber', 'website', 'contactName', 'contactRole', 'yearsInBusiness', 'companySize'],
            'tips' => [
                'Complete your business profile to attract quality influencers',
                'Add your logo and business details for credibility',
                'Clear business goals help us recommend the right matches',
            ],
        ],
        2 => [
            'title' => 'Business Profile & Identity',
            'component' => 'step2',
            'fields' => ['businessType', 'industry', 'businessDescription', 'uniqueValueProposition', 'logo'],
            'tips' => [
                'Showcase your brand\'s personality through visuals',
                'Highlight unique selling points to stand out',
                'Consistent branding builds trust with influencers',
            ],
        ],
        3 => [
            'title' => 'Platform Preferences & Goals',
            'component' => 'step3',
            'fields' => ['instagramHandle', 'facebookHandle', 'tiktokHandle', 'linkedinHandle'],
            'tips' => [
                'Choose platforms that align with your target audience',
                'Set clear goals for your influencer marketing campaigns',
                'Consider your budget and resources when selecting platforms',
            ],
        ],
        4 => [
            'title' => 'Plan Selection & Setup',
            'component' => 'step4',
            'fields' => [],
            'tips' => [
                'Choose the right plan based on your business needs',
                'Take advantage of free trials to test features',
                'Consult with our team for personalized recommendations',
            ],
        ],
        5 => [
            'title' => 'Welcome to CollabConnect',
            'component' => 'step5',
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

    }

    private function fillBusinessData(): void
    {
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
                'logo' => 'nullable|image|max:5120', // 5MB max
            ],
            3 => [
                'instagramHandle' => 'nullable|string|max:255',
                'facebookHandle' => 'nullable|string|max:255',
                'tiktokHandle' => 'nullable|string|max:255',
                'linkedinHandle' => 'nullable|string|max:255',
            ],
            default => []
        };
    }

    public function getValidationMessagesForStep(int $step): array
    {
        return match ($step) {
            1 => [
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
                'logo.image' => 'Logo must be an image file.',
                'logo.max' => 'Logo file size cannot exceed 5MB.',
            ],
            default => []
        };
    }

    public function nextStep()
    {
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

        // Handle logo upload
        if ($this->logo) {
            $this->business->clearMediaCollection('logo');
            $this->business
                ->addMedia($this->logo->getRealPath())
                ->usingName($this->logo->getClientOriginalName())
                ->toMediaCollection('logo');
        }
    }

    private function createBusiness(): Business
    {
        if (! $this->business) {
            // Create business with all data in one go
            $this->business = Business::create([
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
        Auth::user()->update(['onboarding_completed_at' => now()]);

        // Clear the cached step since onboarding is complete
        $this->clearOnboardingStep();

        session()->flash('success', 'Welcome to CollabConnect! Your business profile is now complete.');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.onboarding.business-onboarding', [
            'currentStepData' => $this->getCurrentStepData(),
            'steps' => $this->stepConfiguration,
            'currentStep' => $this->step,
            'maxSteps' => $this->getMaxSteps(),
        ]);
    }
}
