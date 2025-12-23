<?php

namespace App\Livewire\Influencer;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Enums\DeliverableType;
use App\Enums\SocialPlatform;
use App\Livewire\BaseComponent;
use App\Models\StripePrice;
use App\Models\User;
use App\Rules\UniqueUsername;
use App\Settings\PromotionSettings;
use DateTime;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class InfluencerSettings extends BaseComponent
{
    use WithFileUploads;

    public string $activeTab = 'account';

    public ?string $activeSubtab = null;

    protected array $validTabs = ['account', 'match', 'social', 'portfolio', 'billing'];

    protected array $validBillingSubtabs = ['overview', 'plans', 'payment-methods', 'invoices'];

    // Account Settings
    public bool $is_searchable = true;

    public ?string $searchable_at = null;

    public bool $is_accepting_invitations = true;

    // Profile
    public string $username = '';

    public string $bio = '';

    public string $about_yourself = '';

    public string $passions = '';

    public $profile_image;

    public $banner_image;

    // Match Profile - Industry & Content
    public string $primary_industry = '';

    public array $content_types = [];

    public array $preferred_business_types = [];

    public array $preferred_campaign_types = [];

    public array $deliverable_types = [];

    // Match Profile - Compensation
    public array $compensation_types = [];

    public ?int $typical_lead_time_days = null;

    // Location
    public string $address = '';

    public string $city = '';

    public string $state = '';

    public string $county = '';

    public string $postal_code = '';

    public string $phone_number = '';

    // Profile Promotion
    public bool $is_promoted = false;

    public ?DateTime $promotion_ends_at = null;

    public int $promotion_credits = 0;

    public int $creditQuantity = 1;

    // Social accounts
    public array $social_accounts = [];

    public function mount(?string $tab = null, ?string $subtab = null): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isInfluencerAccount()) {
            $this->redirect(route('dashboard'));

            return;
        }

        // Handle URL tab parameter
        if ($tab && in_array($tab, $this->validTabs)) {
            $this->activeTab = $tab;
        }

        // Handle URL subtab parameter (for billing)
        if ($subtab && $this->activeTab === 'billing' && in_array($subtab, $this->validBillingSubtabs)) {
            $this->activeSubtab = $subtab;
        }

        $this->loadInfluencerProfile($user);

        $this->is_promoted = $user->influencer->is_promoted ?? false;
        $this->promotion_ends_at = $user->influencer->promoted_until ?? null;
        $this->promotion_credits = $user->influencer->promotion_credits ?? 0;
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->activeSubtab = null;

        $this->updateUrl();
    }

    public function setBillingSubtab(string $subtab): void
    {
        $this->activeSubtab = $subtab;
        $this->updateUrl();
    }

    protected function updateUrl(): void
    {
        $url = '/influencer/settings/'.$this->activeTab;

        if ($this->activeTab === 'billing' && $this->activeSubtab) {
            $url .= '/'.$this->activeSubtab;
        }

        $this->dispatch('update-url', url: $url);
    }

    private function loadInfluencerProfile(User $user): void
    {
        $profile = $user->influencer;
        if (! $profile) {
            return;
        }

        // Account Settings
        $this->is_searchable = $profile->is_searchable ?? true;
        $this->searchable_at = $profile->searchable_at?->format('Y-m-d') ?? null;
        $this->is_accepting_invitations = $profile->is_accepting_invitations ?? true;

        // Profile
        $this->username = $profile->username ?? '';
        $this->bio = $profile->bio ?? '';
        $this->about_yourself = $profile->about_yourself ?? '';
        $this->passions = $profile->passions ?? '';

        // Match Profile - Industry & Content
        $this->primary_industry = $profile->primary_industry?->value ?? '';
        // Filter out any invalid content types that don't exist in the enum
        $validCampaignTypes = array_column(CampaignType::cases(), 'value');
        $this->content_types = array_values(array_filter(
            $profile->content_types ?? [],
            fn ($type) => in_array($type, $validCampaignTypes)
        ));
        $this->preferred_business_types = $profile->preferred_business_types ?? [];
        $this->preferred_campaign_types = $profile->preferred_campaign_types ?? [];
        $this->deliverable_types = $profile->deliverable_types ?? [];

        // Match Profile - Compensation
        // Filter out any invalid compensation types that don't exist in the enum
        $validCompensationTypes = array_column(CompensationType::cases(), 'value');
        $this->compensation_types = array_values(array_filter(
            $profile->compensation_types ?? [],
            fn ($type) => in_array($type, $validCompensationTypes)
        ));
        $this->typical_lead_time_days = $profile->typical_lead_time_days;

        // Location
        $this->address = $profile->address ?? '';
        $this->city = $profile->city ?? '';
        $this->state = $profile->state ?? '';
        $this->county = $profile->county ?? '';
        $this->postal_code = $profile->postal_code ?? '';
        $this->phone_number = $profile->phone_number ?? '';

        // Load social accounts
        $this->loadSocialAccounts($profile);
    }

    private function loadSocialAccounts($profile): void
    {
        $this->social_accounts = [];
        foreach (SocialPlatform::cases() as $platform) {
            $this->social_accounts[$platform->value] = [
                'platform' => $platform->value,
                'username' => '',
                'followers' => null,
            ];
        }

        if ($profile?->socialAccounts) {
            foreach ($profile->socialAccounts as $account) {
                $this->social_accounts[$account->platform->value] = [
                    'platform' => $account->platform->value,
                    'username' => $account->username,
                    'followers' => $account->followers,
                ];
            }
        }
    }

    public function updatedUsername(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();
        $influencer = $user->influencer;

        $this->validateOnly('username', [
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername(null, $influencer?->id)],
        ]);
    }

    public function updatedContentTypes(): void
    {
        if (count($this->content_types) > 3) {
            $this->content_types = array_slice($this->content_types, 0, 3);
            \Flux::toast('You can only select up to 3 content types.', variant: 'danger', position: 'bottom right');
        }
    }

    public function updatedCompensationTypes(): void
    {
        if (count($this->compensation_types) > 3) {
            $this->compensation_types = array_slice($this->compensation_types, 0, 3);
            \Flux::toast('You can only select up to 3 compensation types.', variant: 'danger', position: 'bottom right');
        }
    }

    public function promoteProfile(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isInfluencerAccount()) {
            Toaster::error('Only influencer accounts can promote profiles.');

            return;
        }

        $influencer = $user->influencer;

        if (! $influencer) {
            Toaster::error('Influencer profile not found.');

            return;
        }

        if ($influencer->promotion_credits <= 0) {
            Toaster::error('You do not have enough promotion credits to promote your profile.');

            return;
        }

        $promotionSettings = app(PromotionSettings::class);
        $days = $promotionSettings->profilePromotionDays;

        $influencer->is_promoted = true;
        $influencer->promoted_until = now()->addDays($days);
        $influencer->promotion_credits -= 1;
        $influencer->save();

        $this->is_promoted = $influencer->is_promoted;
        $this->promotion_ends_at = $influencer->promoted_until;
        $this->promotion_credits = $influencer->promotion_credits;

        Toaster::success("Your profile has been promoted for {$days} days!");
    }

    #[Computed]
    public function promoCreditPrice(): ?StripePrice
    {
        return StripePrice::where('lookup_key', 'profile_promo_credit_current')
            ->where('active', true)
            ->first();
    }

    public function purchasePromotionCredits(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isInfluencerAccount()) {
            Toaster::error('Only influencer accounts can purchase promotion credits.');

            return;
        }

        $influencer = $user->influencer;

        if (! $influencer) {
            Toaster::error('Influencer profile not found.');

            return;
        }

        $price = $this->promoCreditPrice;

        if (! $price) {
            Toaster::error('Promotion credit pricing is not available. Please try again later.');

            return;
        }

        if ($this->creditQuantity < 1 || $this->creditQuantity > 10) {
            Toaster::error('Please select between 1 and 10 credits.');

            return;
        }

        try {
            // Calculate total amount (price is in cents)
            $totalAmount = $price->unit_amount * $this->creditQuantity;

            // Create or get Stripe customer
            if (! $influencer->hasStripeId()) {
                $influencer->createAsStripeCustomer([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
            }

            // Check for default payment method
            $paymentMethod = $influencer->defaultPaymentMethod();

            if (! $paymentMethod) {
                \Flux::modal('addPaymentMethodFirst')->show();

                return;
            }

            // Create a one-time charge
            $influencer->charge($totalAmount, $paymentMethod->id, [
                'description' => "Profile Promotion Credits x{$this->creditQuantity}",
                'metadata' => [
                    'type' => 'promotion_credits',
                    'quantity' => $this->creditQuantity,
                    'influencer_id' => $influencer->id,
                ],
                'confirm' => true,
                'payment_method_types' => ['card'],
            ]);

            // Add credits to the influencer
            $purchasedQuantity = $this->creditQuantity;
            $influencer->promotion_credits += $purchasedQuantity;
            $influencer->save();

            // Update local state
            $this->promotion_credits = $influencer->promotion_credits;
            $this->creditQuantity = 1;

            \Flux::modal('purchaseCredits')->close();
            Toaster::success("Successfully purchased {$purchasedQuantity} promotion credit(s)!");
        } catch (\Exception $e) {
            logger()->error('Failed to purchase promotion credits: '.$e->getMessage());
            Toaster::error('Failed to purchase credits: '.$e->getMessage());
        }
    }

    /**
     * Get validation rules for a specific tab.
     *
     * @return array<string, mixed>
     */
    protected function getValidationRulesForTab(string $tab, ?int $influencerId = null): array
    {
        return match ($tab) {
            'account' => $this->getAccountTabRules($influencerId),
            'match' => $this->getMatchTabRules(),
            'social' => [], // Social accounts don't have validation rules
            default => [],
        };
    }

    /**
     * Get validation rules for the account tab.
     *
     * @return array<string, mixed>
     */
    protected function getAccountTabRules(?int $influencerId = null): array
    {
        $rules = [
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername(null, $influencerId)],
            'bio' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'county' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone_number' => 'required|string|max:20',
            'is_searchable' => 'boolean',
            'searchable_at' => 'nullable|date',
            'is_accepting_invitations' => 'boolean',
        ];

        if ($this->profile_image) {
            $rules['profile_image'] = 'image|max:5120';
        }
        if ($this->banner_image) {
            $rules['banner_image'] = 'image|max:5120';
        }

        return $rules;
    }

    /**
     * Get validation rules for the match tab.
     *
     * @return array<string, mixed>
     */
    protected function getMatchTabRules(): array
    {
        return [
            'about_yourself' => 'nullable|string|max:2000',
            'passions' => 'nullable|string|max:2000',
            'primary_industry' => ['nullable', BusinessIndustry::validationRule()],
            'content_types' => 'nullable|array|max:3',
            'preferred_business_types' => 'nullable|array|max:2',
            'preferred_campaign_types' => 'nullable|array',
            'deliverable_types' => 'nullable|array',
            'compensation_types' => 'nullable|array|max:3',
            'typical_lead_time_days' => 'required|integer|min:1|max:365',
        ];
    }

    public function updateInfluencerSettings(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isInfluencerAccount()) {
            Toaster::error('Only influencer accounts can update influencer settings.');

            return;
        }

        $influencer = $user->influencer;

        // Only validate and save the current tab's fields
        $rules = $this->getValidationRulesForTab($this->activeTab, $influencer?->id);

        if (! empty($rules)) {
            try {
                $this->validate($rules);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Toaster::error('Could not save, errors on the page.');

                throw $e;
            }
        }

        // Save based on current tab
        match ($this->activeTab) {
            'account' => $this->saveAccountTab($user, $influencer),
            'match' => $this->saveMatchTab($user, $influencer),
            'social' => $this->saveSocialTab($user, $influencer),
            default => null,
        };

        Toaster::success('Settings saved successfully!');
    }

    /**
     * Save account tab fields.
     */
    protected function saveAccountTab(User $user, $influencer): void
    {
        $profileData = [
            'username' => $this->username,
            'bio' => $this->bio,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'county' => $this->county,
            'postal_code' => $this->postal_code,
            'phone_number' => $this->phone_number,
            'is_searchable' => $this->is_searchable,
            'searchable_at' => $this->searchable_at,
            'is_accepting_invitations' => $this->is_accepting_invitations,
        ];

        $influencer = $user->influencer()->updateOrCreate(['user_id' => $user->id], $profileData);

        // Handle image uploads
        if ($this->profile_image) {
            try {
                $influencer->clearMediaCollection('profile_image');
                $influencer->addMedia($this->profile_image->getRealPath())
                    ->usingName('Profile Image')
                    ->usingFileName($this->profile_image->getClientOriginalName())
                    ->toMediaCollection('profile_image');
                $this->profile_image = null;
            } catch (\Exception $e) {
                $this->addError('profile_image', 'Failed to upload profile image: '.$e->getMessage());
            }
        }

        if ($this->banner_image) {
            try {
                $influencer->clearMediaCollection('banner_image');
                $influencer->addMedia($this->banner_image->getRealPath())
                    ->usingName('Banner Image')
                    ->usingFileName($this->banner_image->getClientOriginalName())
                    ->toMediaCollection('banner_image');
                $this->banner_image = null;
            } catch (\Exception $e) {
                $this->addError('banner_image', 'Failed to upload banner image: '.$e->getMessage());
            }
        }
    }

    /**
     * Save match tab fields.
     */
    protected function saveMatchTab(User $user, $influencer): void
    {
        $profileData = [
            'about_yourself' => $this->about_yourself,
            'passions' => $this->passions,
            'primary_industry' => ! empty($this->primary_industry) ? BusinessIndustry::from($this->primary_industry) : null,
            'content_types' => array_filter($this->content_types),
            'preferred_business_types' => array_filter($this->preferred_business_types),
            'preferred_campaign_types' => array_filter($this->preferred_campaign_types),
            'deliverable_types' => array_filter($this->deliverable_types),
            'compensation_types' => array_filter($this->compensation_types),
            'typical_lead_time_days' => $this->typical_lead_time_days,
        ];

        $user->influencer()->updateOrCreate(['user_id' => $user->id], $profileData);
    }

    /**
     * Save social tab fields.
     */
    protected function saveSocialTab(User $user, $influencer): void
    {
        $influencer = $user->influencer()->updateOrCreate(['user_id' => $user->id], []);

        // Update social accounts
        $influencer->socialAccounts()->delete();

        foreach ($this->social_accounts as $accountData) {
            if (! empty($accountData['username'])) {
                $influencer->socialAccounts()->create([
                    'platform' => $accountData['platform'],
                    'username' => $accountData['username'],
                    'url' => SocialPlatform::from($accountData['platform'])->generateUrl($accountData['username']),
                    'followers' => $accountData['followers'] ?: null,
                ]);
            }
        }
    }

    /**
     * Check which tabs need attention (have incomplete fields).
     *
     * @return array<string, bool>
     */
    public function getTabNeedsAttentionProperty(): array
    {
        return [
            'account' => false,
            'match' => false,
            'social' => false,
            'portfolio' => false,
        ];

        // [
        //     'account' => empty($this->username),
        //     'match' => empty($this->about_yourself) && empty($this->passions) && empty(array_filter($this->content_types ?? [])),
        //     'social' => collect($this->social_accounts)->filter(fn ($a) => ! empty($a['username']))->count() === 0,
        //     'portfolio' => false,
        // ];
    }

    public function render()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        return view('livewire.influencer.influencer-settings', [
            'user' => $user,
            'businessIndustryOptions' => BusinessIndustry::toOptions(),
            'businessTypeOptions' => BusinessType::toOptions(),
            'compensationTypeOptions' => CompensationType::toOptions(),
            'socialPlatformOptions' => SocialPlatform::toOptions(),
            'campaignTypeOptions' => CampaignType::toOptions(),
            'deliverableTypeOptions' => DeliverableType::toOptions(),
        ]);
    }
}
