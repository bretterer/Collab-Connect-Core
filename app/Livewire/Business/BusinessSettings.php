<?php

namespace App\Livewire\Business;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompanySize;
use App\Enums\ContactRole;
use App\Enums\SocialPlatform;
use App\Enums\YearsInBusiness;
use App\Jobs\InviteMemberToBusiness;
use App\Livewire\BaseComponent;
use App\Models\BusinessMemberInvite;
use App\Models\BusinessUser;
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
class BusinessSettings extends BaseComponent
{
    use WithFileUploads;

    public $username = '';

    public $business_name = '';

    public $business_email = '';

    public $phone_number = '';

    public $website = '';

    public $contact_name = '';

    public $contact_role = '';

    public $years_in_business = '';

    public $company_size = '';

    public $business_type = '';

    public $industry = '';

    public $business_description = '';

    public $unique_value_proposition = '';

    public $city = '';

    public $state = '';

    public $postal_code = '';

    public $target_gender = [];

    public $target_age_range = [];

    public $business_goals = [];

    public $platforms = [];

    public $instagram_handle = '';

    public $facebook_handle = '';

    public $tiktok_handle = '';

    public $linkedin_handle = '';

    public $business_logo;

    public $business_banner;

    public string $invite_email = '';

    public string $activeTab = 'profile';

    public ?string $activeSubtab = null;

    protected array $validTabs = ['profile', 'branding', 'location', 'social', 'campaigns', 'team', 'billing'];

    protected array $validBillingSubtabs = ['overview', 'plans', 'payment-methods', 'invoices'];

    // Campaign Defaults
    public string $default_brand_overview = '';

    public string $default_brand_story = '';

    public string $default_brand_guidelines = '';

    public string $default_current_advertising_campaign = '';

    public string $default_key_insights = '';

    public string $default_fan_motivator = '';

    public string $default_posting_restrictions = '';

    // Profile Promotion
    public bool $is_promoted = false;

    public ?DateTime $promotion_ends_at = null;

    public int $promotion_credits = 0;

    public int $creditQuantity = 1;

    public function mount(?string $tab = null, ?string $subtab = null)
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isBusinessAccount()) {
            return $this->redirect(route('dashboard'));
        }

        // Handle URL tab parameter
        if ($tab && in_array($tab, $this->validTabs)) {
            $this->activeTab = $tab;
        }

        // Handle URL subtab parameter (for billing)
        if ($subtab && $this->activeTab === 'billing' && in_array($subtab, $this->validBillingSubtabs)) {
            $this->activeSubtab = $subtab;
        }

        $this->loadBusinessProfile($user);

        // Load promotion data
        $business = $user->currentBusiness;
        if ($business) {
            $this->is_promoted = $business->is_promoted ?? false;
            $this->promotion_ends_at = $business->promoted_until ?? null;
            $this->promotion_credits = $business->promotion_credits ?? 0;
        }
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
        $url = '/business/settings/'.$this->activeTab;

        if ($this->activeTab === 'billing' && $this->activeSubtab) {
            $url .= '/'.$this->activeSubtab;
        }

        $this->dispatch('update-url', url: $url);
    }

    private function loadBusinessProfile(User $user)
    {
        $profile = $user->currentBusiness;
        if (! $profile) {
            return;
        }

        $this->username = $profile->username ?? '';
        $this->business_name = $profile->name ?? '';
        $this->business_email = $profile->email ?? '';
        $this->phone_number = $profile->phone ?? '';
        $this->website = $profile->website ?? '';
        $this->contact_name = $profile->primary_contact ?? '';
        $this->contact_role = $profile->contact_role ?? '';
        $this->years_in_business = $profile->maturity ?? '';
        $this->company_size = $profile->size ?? '';
        $this->business_type = $profile->type?->value ?? '';
        $this->industry = $profile->industry?->value ?? '';
        $this->business_description = $profile->description ?? '';
        $this->unique_value_proposition = $profile->selling_points ?? '';
        $this->city = $profile->city ?? '';
        $this->state = $profile->state ?? '';
        $this->postal_code = $profile->postal_code ?? '';
        $this->target_gender = $profile->target_gender ?? [];
        $this->target_age_range = $profile->target_age_range ?? [];
        $this->business_goals = $profile->business_goals ?? [];
        $this->platforms = $profile->platforms ?? [];

        // Load social handles
        $socials = $profile->socials;
        foreach ($socials as $social) {
            switch ($social->platform->value) {
                case 'instagram':
                    $this->instagram_handle = $social->username ?? '';
                    break;
                case 'facebook':
                    $this->facebook_handle = $social->username ?? '';
                    break;
                case 'tiktok':
                    $this->tiktok_handle = $social->username ?? '';
                    break;
                case 'linkedin':
                    $this->linkedin_handle = $social->username ?? '';
                    break;
            }
        }

        // Initialize arrays if empty
        if (empty($this->business_goals)) {
            $this->business_goals = [''];
        }
        if (empty($this->platforms)) {
            $this->platforms = [''];
        }
        if (empty($this->target_age_range)) {
            $this->target_age_range = [''];
        }
        if (empty($this->target_gender)) {
            $this->target_gender = [''];
        }

        // Load campaign defaults
        $defaults = $profile->getCampaignDefaults();
        $this->default_brand_overview = $defaults['brand_overview'] ?? '';
        $this->default_brand_story = $defaults['brand_story'] ?? '';
        $this->default_brand_guidelines = $defaults['brand_guidelines'] ?? '';
        $this->default_current_advertising_campaign = $defaults['current_advertising_campaign'] ?? '';
        $this->default_key_insights = $defaults['default_key_insights'] ?? '';
        $this->default_fan_motivator = $defaults['default_fan_motivator'] ?? '';
        $this->default_posting_restrictions = $defaults['default_posting_restrictions'] ?? '';
    }

    public function updatedUsername($value)
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();
        $business = $user->currentBusiness;

        $this->validateOnly('username', [
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername($business?->id, null)],
        ]);
    }

    public function addBusinessGoal()
    {
        $this->business_goals[] = '';
    }

    public function removeBusinessGoal($index)
    {
        if (count($this->business_goals) > 1) {
            unset($this->business_goals[$index]);
            $this->business_goals = array_values($this->business_goals);
        }
    }

    public function addPlatform()
    {
        $this->platforms[] = '';
    }

    public function removePlatform($index)
    {
        if (count($this->platforms) > 1) {
            unset($this->platforms[$index]);
            $this->platforms = array_values($this->platforms);
        }
    }

    public function addTargetAge()
    {
        $this->target_age_range[] = '';
    }

    public function removeTargetAge($index)
    {
        if (count($this->target_age_range) > 1) {
            unset($this->target_age_range[$index]);
            $this->target_age_range = array_values($this->target_age_range);
        }
    }

    public function addTargetGender()
    {
        $this->target_gender[] = '';
    }

    public function removeTargetGender($index)
    {
        if (count($this->target_gender) > 1) {
            unset($this->target_gender[$index]);
            $this->target_gender = array_values($this->target_gender);
        }
    }

    public function updateBusinessSettings()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isBusinessAccount()) {
            Toaster::error('Only business accounts can update business settings.');

            return;
        }

        $business = $user->currentBusiness;

        $rules = [
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername($business?->id, null)],
            'business_name' => 'required|string|max:255',
            'business_email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_role' => ['required', ContactRole::validationRule()],
            'years_in_business' => ['required', YearsInBusiness::validationRule()],
            'company_size' => ['required', CompanySize::validationRule()],
            'business_type' => ['required', BusinessType::validationRule()],
            'industry' => ['required', BusinessIndustry::validationRule()],
            'business_description' => 'required|string|max:1000',
            'unique_value_proposition' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'target_gender' => 'nullable|array',
            'target_age_range' => 'nullable|array',
            'business_goals' => 'nullable|array',
            'platforms' => 'nullable|array',
        ];

        if ($this->business_logo) {
            $rules['business_logo'] = 'image|max:5120';
        }
        if ($this->business_banner) {
            $rules['business_banner'] = 'image|max:5120';
        }

        $this->validate($rules);

        $profileData = [
            'username' => $this->username,
            'name' => $this->business_name,
            'email' => $this->business_email,
            'phone' => $this->phone_number,
            'website' => $this->website,
            'primary_contact' => $this->contact_name,
            'contact_role' => $this->contact_role,
            'maturity' => $this->years_in_business,
            'size' => $this->company_size,
            'type' => ! empty($this->business_type) ? BusinessType::from($this->business_type) : null,
            'industry' => ! empty($this->industry) ? BusinessIndustry::from($this->industry) : null,
            'description' => $this->business_description,
            'selling_points' => $this->unique_value_proposition,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'target_gender' => array_filter($this->target_gender),
            'target_age_range' => array_filter($this->target_age_range),
            'business_goals' => array_filter($this->business_goals),
            'platforms' => array_filter($this->platforms),
            'campaign_defaults' => [
                'brand_overview' => $this->default_brand_overview,
                'brand_story' => $this->default_brand_story,
                'brand_guidelines' => $this->default_brand_guidelines,
                'current_advertising_campaign' => $this->default_current_advertising_campaign,
                'default_key_insights' => $this->default_key_insights,
                'default_fan_motivator' => $this->default_fan_motivator,
                'default_posting_restrictions' => $this->default_posting_restrictions,
            ],
        ];

        $business->update($profileData);

        // Update social handles
        $business->socials()->delete();

        $socialHandles = [
            'instagram' => $this->instagram_handle,
            'facebook' => $this->facebook_handle,
            'tiktok' => $this->tiktok_handle,
            'linkedin' => $this->linkedin_handle,
        ];

        foreach ($socialHandles as $platform => $handle) {
            if (! empty($handle)) {
                $business->socials()->create([
                    'platform' => SocialPlatform::from($platform),
                    'username' => $handle,
                    'url' => SocialPlatform::from($platform)->generateUrl($handle),
                ]);
            }
        }

        // Handle business image uploads
        if ($this->business_logo) {
            try {
                $business->clearMediaCollection('logo');

                $business->addMedia($this->business_logo->getRealPath())
                    ->usingName('Business Logo')
                    ->usingFileName($this->business_logo->getClientOriginalName())
                    ->toMediaCollection('logo');

                $this->business_logo = null;
            } catch (\Exception $e) {
                $this->addError('business_logo', 'Failed to upload business logo: '.$e->getMessage());
            }
        }

        if ($this->business_banner) {
            try {
                $business->clearMediaCollection('banner_image');

                $business->addMedia($this->business_banner->getRealPath())
                    ->usingName('Business Banner')
                    ->usingFileName($this->business_banner->getClientOriginalName())
                    ->toMediaCollection('banner_image');

                $this->business_banner = null;
            } catch (\Exception $e) {
                $this->addError('business_banner', 'Failed to upload business banner: '.$e->getMessage());
            }
        }

        Toaster::success('Business settings updated successfully!');
    }

    public function sendInvite()
    {
        try {
            $this->validate([
                'invite_email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:business_member_invites,email,NULL,id,business_id,'.$this->getAuthenticatedUser()->current_business,
                ],
            ], [
                'invite_email.unique' => 'An invite has already been sent to this email for your business.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Toaster::error('Failed to send invite. '.$e->getMessage());

            return;
        }

        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isBusinessAccount()) {
            Toaster::error('Only business accounts can send invites.');
            $this->addError('invite_email', 'Only business accounts can send invites.');

            return;
        }

        $business = $user->currentBusiness;
        if (! $business) {
            Toaster::error('You must have a business profile to send invites.');
            $this->addError('invite_email', 'You must have a business profile to send invites.');

            return;
        }

        // Check if the user is already a member of the business
        $existingMember = $business->members()->where('email', $this->invite_email)->first();
        if ($existingMember) {
            Toaster::error('This user is already a member of your business.');
            $this->addError('invite_email', 'This user is already a member of your business.');

            return;
        }

        InviteMemberToBusiness::dispatchSync($business, $this->invite_email, $user);

        Toaster::success('Invitation sent to '.$this->invite_email);
        $this->invite_email = '';

        $this->dispatch('$refresh');
    }

    public function removeMember($memberId)
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isBusinessAccount()) {
            Toaster::error('Only business accounts can remove members.');

            return;
        }

        if (! $memberId === $user->id) {
            Toaster::error('You cannot remove yourself from the business. Please contact another admin to remove your account.');

            return;
        }

        $business = $user->currentBusiness;
        if (! $business) {
            Toaster::error('You must have a business profile to remove members.');

            return;
        }

        $member = BusinessUser::query()->where('user_id', $memberId)->where('business_id', $user->currentBusiness->id)->first();

        if (! $member) {
            Toaster::error('Member not found.');

            return;
        }

        $member->delete();

        BusinessMemberInvite::query()
            ->where('email', $member->user->email)
            ->where('business_id', $business->id)
            ->delete();

        Toaster::success('Member removed successfully.');
    }

    public function rescindInvite($inviteId)
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isBusinessAccount()) {
            Toaster::error('Only business accounts can remove members.');

            return;
        }

        $business = $user->currentBusiness;
        if (! $business) {
            Toaster::error('You must have a business profile to remove members.');

            return;
        }

        $invite = $business->pendingInvites()->where('id', $inviteId)->first();
        if (! $invite) {
            Toaster::error('Invite not found.');

            return;
        }

        $invite->delete();

        Toaster::success('Invite rescinded successfully.');
    }

    #[Computed]
    public function promoCreditPrice(): ?StripePrice
    {
        return StripePrice::where('lookup_key', 'profile_promo_credit_current')
            ->where('active', true)
            ->first();
    }

    public function promoteProfile(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isBusinessAccount()) {
            Toaster::error('Only business accounts can promote profiles.');

            return;
        }

        $business = $user->currentBusiness;

        if (! $business) {
            Toaster::error('Business profile not found.');

            return;
        }

        if ($business->promotion_credits <= 0) {
            Toaster::error('You do not have enough promotion credits to promote your profile.');

            return;
        }

        $promotionSettings = app(PromotionSettings::class);
        $days = $promotionSettings->profilePromotionDays;

        $business->is_promoted = true;
        $business->promoted_until = now()->addDays($days);
        $business->promotion_credits -= 1;
        $business->save();

        $this->is_promoted = $business->is_promoted;
        $this->promotion_ends_at = $business->promoted_until;
        $this->promotion_credits = $business->promotion_credits;

        Toaster::success("Your profile has been promoted for {$days} days!");
    }

    public function purchasePromotionCredits(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isBusinessAccount()) {
            Toaster::error('Only business accounts can purchase promotion credits.');

            return;
        }

        $business = $user->currentBusiness;

        if (! $business) {
            Toaster::error('Business profile not found.');

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
            if (! $business->hasStripeId()) {
                $business->createAsStripeCustomer([
                    'name' => $business->name,
                    'email' => $business->email,
                ]);
            }

            // Check for default payment method
            $paymentMethod = $business->defaultPaymentMethod();

            if (! $paymentMethod) {
                \Flux::modal('addPaymentMethodFirst')->show();

                return;
            }

            // Create a one-time charge
            $business->charge($totalAmount, $paymentMethod->id, [
                'description' => "Profile Promotion Credits x{$this->creditQuantity}",
                'metadata' => [
                    'type' => 'promotion_credits',
                    'quantity' => $this->creditQuantity,
                    'business_id' => $business->id,
                ],
                'confirm' => true,
                'payment_method_types' => ['card'],
            ]);

            // Add credits to the business
            $purchasedQuantity = $this->creditQuantity;
            $business->promotion_credits += $purchasedQuantity;
            $business->save();

            // Update local state
            $this->promotion_credits = $business->promotion_credits;
            $this->creditQuantity = 1;

            \Flux::modal('purchaseCredits')->close();
            Toaster::success("Successfully purchased {$purchasedQuantity} promotion credit(s)!");
        } catch (\Exception $e) {
            logger()->error('Failed to purchase promotion credits: '.$e->getMessage());
            Toaster::error('Failed to purchase credits: '.$e->getMessage());
        }
    }

    public function render()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        return view('livewire.business.business-settings', [
            'user' => $user,
            'businessIndustryOptions' => BusinessIndustry::toOptions(),
            'businessTypeOptions' => BusinessType::toOptions(),
            'companySizeOptions' => CompanySize::toOptions(),
            'contactRoleOptions' => ContactRole::toOptions(),
            'yearsInBusinessOptions' => YearsInBusiness::toOptions(),
            'socialPlatformOptions' => SocialPlatform::toOptions(),
        ]);
    }
}
