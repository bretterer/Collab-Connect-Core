<?php

namespace App\Livewire\Profile;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompanySize;
use App\Enums\CompensationType;
use App\Enums\ContactRole;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\SubscriptionPlan;
use App\Enums\YearsInBusiness;
use App\Jobs\InviteMemberToBusiness;
use App\Livewire\BaseComponent;
use App\Models\BusinessMemberInvite;
use App\Models\BusinessUser;
use App\Models\User;
use App\Rules\UniqueUsername;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class EditProfile extends BaseComponent
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    public $username = '';

    #[Validate('nullable|string')]
    public $current_password = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public $password = '';

    #[Validate('nullable|string')]
    public $password_confirmation = '';

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

    // Legacy fields for backwards compatibility
    public $websites = [''];

    public $primary_zip_code = '';

    public $location_count = 1;

    public $is_franchise = false;

    public $is_national_brand = false;

    public $contact_email = '';

    public $collaboration_goals = [''];

    public $campaign_types = [''];

    public $team_members = [''];

    public $creator_name = '';

    public $bio = '';

    public $primary_niche = '';

    public $content_types = [];

    public $preferred_business_types = [];

    public $address = '';

    public $county = '';

    public $compensation_types = [];

    public $typical_lead_time_days = null;

    public $social_accounts = [];

    public $media_kit_url = '';

    public $has_media_kit = false;

    public $collaboration_preferences = [''];

    public $preferred_brands = [''];

    public $follower_count = '';

    // Image upload properties
    public $profile_image;

    public $banner_image;

    public $business_logo;

    public $business_banner;

    public string $invite_email = '';

    // Current business selection for multi-business users

    private function loadBusinessProfile(User $user)
    {
        $profile = $user->currentBusiness;
        if (! $profile) {
            return;
        }

        // Load all onboarding fields
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

        // Legacy fields for backwards compatibility
        $this->primary_zip_code = $this->postal_code;
        $this->contact_email = $this->business_email;
        $this->websites = ! empty($this->website) ? [$this->website] : [''];
        $this->collaboration_goals = [''];
        $this->campaign_types = [''];
        $this->team_members = [''];
    }

    private function loadInfluencerProfile(User $user)
    {
        $profile = $user->influencer;
        if (! $profile) {
            return;
        }

        $this->username = $profile->username ?? '';
        $this->creator_name = $user->name ?? '';
        $this->bio = $profile->bio ?? '';
        $this->primary_niche = $profile->primary_niche?->value ?? '';
        $this->content_types = $profile->content_types ?? [];
        $this->preferred_business_types = $profile->preferred_business_types ?? [];
        $this->address = $profile->address ?? '';
        $this->city = $profile->city ?? '';
        $this->state = $profile->state ?? '';
        $this->county = $profile->county ?? '';
        $this->primary_zip_code = $profile->postal_code ?? '';
        $this->phone_number = $profile->phone_number ?? '';
        $this->compensation_types = $profile->compensation_types ?? [];
        $this->typical_lead_time_days = $profile->typical_lead_time_days;
        $this->media_kit_url = '';
        $this->has_media_kit = false;
        $this->collaboration_preferences = [''];
        $this->preferred_brands = [''];
        $this->follower_count = '';

        // Load social accounts
        $this->social_accounts = [];
        foreach (SocialPlatform::cases() as $platform) {
            $this->social_accounts[$platform->value] = [
                'platform' => $platform->value,
                'username' => '',
                'followers' => null,
            ];
        }

        if ($profile->socialAccounts) {
            foreach ($profile->socialAccounts as $account) {
                $this->social_accounts[$account->platform->value] = [
                    'platform' => $account->platform->value,
                    'username' => $account->username,
                    'followers' => $account->followers,
                ];
            }
        }
    }

    public function addWebsite()
    {
        $this->addToArray('websites');
    }

    public function removeWebsite($index)
    {
        $this->removeFromArray('websites', $index);
    }

    public function addCollaborationGoal()
    {
        $this->addToArray('collaboration_goals');
    }

    public function removeCollaborationGoal($index)
    {
        $this->removeFromArray('collaboration_goals', $index);
    }

    public function addCampaignType()
    {
        $this->addToArray('campaign_types');
    }

    public function removeCampaignType($index)
    {
        $this->removeFromArray('campaign_types', $index);
    }

    public function addTeamMember()
    {
        $this->addToArray('team_members');
    }

    public function removeTeamMember($index)
    {
        $this->removeFromArray('team_members', $index);
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

    public function addCollaborationPreference()
    {
        $this->addToArray('collaboration_preferences');
    }

    public function removeCollaborationPreference($index)
    {
        $this->removeFromArray('collaboration_preferences', $index);
    }

    public function addPreferredBrand()
    {
        $this->addToArray('preferred_brands');
    }

    public function removePreferredBrand($index)
    {
        $this->removeFromArray('preferred_brands', $index);
    }

    public function addContentType()
    {
        if (count($this->content_types) < 3) {
            $this->content_types[] = '';
        }
    }

    public function removeContentType($index)
    {
        if (count($this->content_types) > 1) {
            unset($this->content_types[$index]);
            $this->content_types = array_values($this->content_types);
        }
    }

    public function addBusinessType()
    {
        if (count($this->preferred_business_types) < 2) {
            $this->preferred_business_types[] = '';
        }
    }

    public function removeBusinessType($index)
    {
        if (count($this->preferred_business_types) > 1) {
            unset($this->preferred_business_types[$index]);
            $this->preferred_business_types = array_values($this->preferred_business_types);
        }
    }

    public function addCompensationType()
    {
        if (count($this->compensation_types) < 3) {
            $this->compensation_types[] = '';
        }
    }

    public function removeCompensationType($index)
    {
        if (count($this->compensation_types) > 1) {
            unset($this->compensation_types[$index]);
            $this->compensation_types = array_values($this->compensation_types);
        }
    }

    public function updatedUsername($value)
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if ($user->isBusinessAccount()) {
            $business = $user->currentBusiness;
            $this->validateOnly('username', [
                'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername($business?->id, null)],
            ]);
        } elseif ($user->isInfluencerAccount()) {
            $influencer = $user->influencer;
            $this->validateOnly('username', [
                'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername(null, $influencer?->id)],
            ]);
        }
    }

    public function resetOnboarding(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if ($user->isBusinessAccount()) {
            $user->currentBusiness->onboarding_complete = false;
            $user->currentBusiness->save();
        } elseif ($user->isInfluencerAccount()) {
            $user->influencer->onboarding_complete = false;
            $user->influencer->save();
        }

        $this->redirect(route('dashboard'));
    }

    public function updateProfile()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ];

        // Add username validation based on account type
        if ($user->isBusinessAccount()) {
            $business = $user->currentBusiness;
            $rules['username'] = ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername($business?->id, null)];
        } elseif ($user->isInfluencerAccount()) {
            $influencer = $user->influencer;
            $rules['username'] = ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername(null, $influencer?->id)];
        }

        // Add password validation rules if password change is requested
        if (! empty($this->password) || ! empty($this->current_password)) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        // Add image validation rules
        if ($this->profile_image) {
            $rules['profile_image'] = 'image|max:5120'; // 5MB max
        }
        if ($this->banner_image) {
            $rules['banner_image'] = 'image|max:5120'; // 5MB max
        }
        if ($this->business_logo) {
            $rules['business_logo'] = 'image|max:5120'; // 5MB max
        }
        if ($this->business_banner) {
            $rules['business_banner'] = 'image|max:5120'; // 5MB max
        }

        $this->validate($rules);

        // If password change is requested, verify current password
        if (! empty($this->password)) {
            if (! Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'The current password is incorrect.');

                return;
            }
        }

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (! empty($this->password)) {
            $userData['password'] = Hash::make($this->password);
        }

        $user->update($userData);

        if ($user->isBusinessAccount()) {
            $this->updateBusinessProfile($user);
        } elseif ($user->isInfluencerAccount()) {
            $this->updateInfluencerProfile($user);
        }

        $this->flashSuccess('Profile updated successfully!');
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    private function updateBusinessProfile(User $user)
    {
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
        ];

        $business = $user->currentBusiness;
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

                // Clear the uploaded file to prevent re-upload
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

                // Clear the uploaded file to prevent re-upload
                $this->business_banner = null;
            } catch (\Exception $e) {
                $this->addError('business_banner', 'Failed to upload business banner: '.$e->getMessage());
            }
        }
    }

    private function updateInfluencerProfile(User $user)
    {
        $profileData = [
            'username' => $this->username,
            'bio' => $this->bio,
            'content_types' => array_filter($this->content_types),
            'preferred_business_types' => array_filter($this->preferred_business_types),
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'county' => $this->county,
            'postal_code' => $this->primary_zip_code,
            'phone_number' => $this->phone_number,
            'compensation_types' => array_filter($this->compensation_types),
            'typical_lead_time_days' => $this->typical_lead_time_days,
        ];

        $influencer = $user->influencer()->updateOrCreate(['user_id' => $user->id], $profileData);

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

        // Handle image uploads
        if ($this->profile_image) {
            try {
                $influencer->clearMediaCollection('profile_image');

                $influencer->addMediaFromRequest('profile_image')
                    ->usingName('Profile Image')
                    ->toMediaCollection('profile_image');

                // Clear the uploaded file to prevent re-upload
                $this->profile_image = null;
            } catch (\Exception $e) {
                // Try alternative approach with temporary file path
                try {
                    $influencer->addMedia($this->profile_image->getRealPath())
                        ->usingName('Profile Image')
                        ->usingFileName($this->profile_image->getClientOriginalName())
                        ->toMediaCollection('profile_image');

                    $this->profile_image = null;
                } catch (\Exception $e2) {
                    $this->addError('profile_image', 'Failed to upload profile image: '.$e2->getMessage());
                }
            }
        }

        if ($this->banner_image) {
            try {
                $influencer->clearMediaCollection('banner_image');

                $influencer->addMedia($this->banner_image->getRealPath())
                    ->usingName('Banner Image')
                    ->usingFileName($this->banner_image->getClientOriginalName())
                    ->toMediaCollection('banner_image');

                // Clear the uploaded file to prevent re-upload
                $this->banner_image = null;
            } catch (\Exception $e) {
                $this->addError('banner_image', 'Failed to upload banner image: '.$e->getMessage());
            }
        }
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

        // Refresh the component to show the new invite
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

    public function render()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        return view('livewire.profile.edit-profile', [
            'user' => $user,
            'nicheOptions' => Niche::toOptions(),
            'businessIndustryOptions' => BusinessIndustry::toOptions(),
            'businessTypeOptions' => BusinessType::toOptions(),
            'companySizeOptions' => CompanySize::toOptions(),
            'contactRoleOptions' => ContactRole::toOptions(),
            'yearsInBusinessOptions' => YearsInBusiness::toOptions(),
            'compensationTypeOptions' => CompensationType::toOptions(),
            'socialPlatformOptions' => SocialPlatform::toOptions(),
            'subscriptionPlanOptions' => SubscriptionPlan::toOptions(),
        ]);
    }

    public function mount()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        $this->name = $user->name;
        $this->email = $user->email;

        if ($user->isBusinessAccount()) {
            $this->loadBusinessProfile($user);
        } elseif ($user->isInfluencerAccount()) {
            $this->loadInfluencerProfile($user);

            // Initialize arrays if empty
            if (empty($this->content_types)) {
                $this->content_types = [''];
            }
            if (empty($this->preferred_business_types)) {
                $this->preferred_business_types = [''];
            }
            if (empty($this->compensation_types)) {
                $this->compensation_types = [''];
            }
        } else {
            // Initialize business arrays if empty
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
        }
    }
}
