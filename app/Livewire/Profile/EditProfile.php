<?php

namespace App\Livewire\Profile;

use App\Enums\Niche;
use App\Enums\SubscriptionPlan;
use App\Livewire\BaseComponent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class EditProfile extends BaseComponent
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('nullable|string')]
    public $current_password = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public $password = '';

    #[Validate('nullable|string')]
    public $password_confirmation = '';

    public $business_name = '';

    public $industry = '';

    public $websites = [''];

    public $primary_zip_code = '';

    public $location_count = 1;

    public $is_franchise = false;

    public $is_national_brand = false;

    public $contact_name = '';

    public $contact_email = '';

    public $collaboration_goals = [''];

    public $campaign_types = [''];

    public $team_members = [''];

    public $creator_name = '';

    public $primary_niche = '';

    public $media_kit_url = '';

    public $has_media_kit = false;

    public $collaboration_preferences = [''];

    public $preferred_brands = [''];

    public $follower_count = '';

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
        }
    }

    private function loadBusinessProfile(User $user)
    {
        $profile = $user->businessProfile;
        if (! $profile) {
            return;
        }

        $this->business_name = $profile->business_name ?? '';
        $this->industry = $profile->industry?->value ?? '';
        $this->websites = $profile->websites ?? [''];
        $this->primary_zip_code = $profile->primary_zip_code ?? '';
        $this->location_count = $profile->location_count ?? 1;
        $this->is_franchise = $profile->is_franchise ?? false;
        $this->is_national_brand = $profile->is_national_brand ?? false;
        $this->contact_name = $profile->contact_name ?? '';
        $this->contact_email = $profile->contact_email ?? '';
        $this->collaboration_goals = $profile->collaboration_goals ?? [''];
        $this->campaign_types = $profile->campaign_types ?? [''];
        $this->team_members = $profile->team_members ?? [''];
    }

    private function loadInfluencerProfile(User $user)
    {
        $profile = $user->influencerProfile;
        if (! $profile) {
            return;
        }

        $this->creator_name = $profile->creator_name ?? '';
        $this->primary_niche = $profile->primary_niche?->value ?? '';
        $this->primary_zip_code = $profile->primary_zip_code ?? '';
        $this->media_kit_url = $profile->media_kit_url ?? '';
        $this->has_media_kit = $profile->has_media_kit ?? false;
        $this->collaboration_preferences = $profile->collaboration_preferences ?? [''];
        $this->preferred_brands = $profile->preferred_brands ?? [''];
        $this->follower_count = $profile->follower_count ?? '';
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

        // Add password validation rules if password change is requested
        if (! empty($this->password) || ! empty($this->current_password)) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:8|confirmed';
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
            'name' => $this->name,
            'industry' => ! empty($this->industry) ? Niche::from($this->industry) : null,
            'websites' => $this->filterEmptyValues($this->websites),
            'primary_zip_code' => $this->primary_zip_code,
            'location_count' => $this->location_count,
            'is_franchise' => $this->is_franchise,
            'is_national_brand' => $this->is_national_brand,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'collaboration_goals' => $this->filterEmptyValues($this->collaboration_goals),
            'campaign_types' => $this->filterEmptyValues($this->campaign_types),
            'team_members' => $this->filterEmptyValues($this->team_members),
        ];

        $user->currentBusiness()->updateOrCreate(['id' => $user->currentBusiness->id], $profileData);
    }

    private function updateInfluencerProfile(User $user)
    {
        $profileData = [
            'creator_name' => $this->creator_name,
            'primary_niche' => ! empty($this->primary_niche) ? Niche::from($this->primary_niche) : null,
            'primary_zip_code' => $this->primary_zip_code,
            'media_kit_url' => $this->media_kit_url,
            'has_media_kit' => $this->has_media_kit,
            'collaboration_preferences' => $this->filterEmptyValues($this->collaboration_preferences),
            'preferred_brands' => $this->filterEmptyValues($this->preferred_brands),
            'follower_count' => $this->follower_count,
        ];

        $user->influencer()->updateOrCreate(['user_id' => $user->id], $profileData);
    }

    public function render()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        return view('livewire.profile.edit-profile', [
            'user' => $user,
            'nicheOptions' => Niche::toOptions(),
            'subscriptionPlanOptions' => SubscriptionPlan::toOptions(),
        ]);
    }
}
