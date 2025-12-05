<?php

namespace App\Livewire\Influencer;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompensationType;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Livewire\BaseComponent;
use App\Models\User;
use App\Rules\UniqueUsername;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class InfluencerSettings extends BaseComponent
{
    use WithFileUploads;

    public $username = '';

    public $creator_name = '';

    public $bio = '';

    public $primary_niche = '';

    public $content_types = [];

    public $preferred_business_types = [];

    public $address = '';

    public $city = '';

    public $state = '';

    public $county = '';

    public $postal_code = '';

    public $phone_number = '';

    public $compensation_types = [];

    public $typical_lead_time_days = null;

    public $social_accounts = [];

    public $media_kit_url = '';

    public $has_media_kit = false;

    public $collaboration_preferences = [''];

    public $preferred_brands = [''];

    public $profile_image;

    public $banner_image;

    public function mount()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isInfluencerAccount()) {
            return $this->redirect(route('dashboard'));
        }

        $this->loadInfluencerProfile($user);
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
        $this->postal_code = $profile->postal_code ?? '';
        $this->phone_number = $profile->phone_number ?? '';
        $this->compensation_types = $profile->compensation_types ?? [];
        $this->typical_lead_time_days = $profile->typical_lead_time_days;
        $this->media_kit_url = '';
        $this->has_media_kit = false;
        $this->collaboration_preferences = [''];
        $this->preferred_brands = [''];

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
    }

    public function updatedUsername($value)
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();
        $influencer = $user->influencer;

        $this->validateOnly('username', [
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername(null, $influencer?->id)],
        ]);
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

    public function addCollaborationPreference()
    {
        $this->collaboration_preferences[] = '';
    }

    public function removeCollaborationPreference($index)
    {
        if (count($this->collaboration_preferences) > 1) {
            unset($this->collaboration_preferences[$index]);
            $this->collaboration_preferences = array_values($this->collaboration_preferences);
        }
    }

    public function addPreferredBrand()
    {
        $this->preferred_brands[] = '';
    }

    public function removePreferredBrand($index)
    {
        if (count($this->preferred_brands) > 1) {
            unset($this->preferred_brands[$index]);
            $this->preferred_brands = array_values($this->preferred_brands);
        }
    }

    public function updateInfluencerSettings()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if (! $user->isInfluencerAccount()) {
            Toaster::error('Only influencer accounts can update influencer settings.');

            return;
        }

        $influencer = $user->influencer;

        $rules = [
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', new UniqueUsername(null, $influencer?->id)],
            'bio' => 'nullable|string|max:1000',
            'primary_niche' => ['nullable', Niche::validationRule()],
            'content_types' => 'nullable|array|max:3',
            'preferred_business_types' => 'nullable|array|max:2',
            'address' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'county' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone_number' => 'required|string|max:20',
            'compensation_types' => 'nullable|array|max:3',
            'typical_lead_time_days' => 'required|integer|min:1|max:365',
        ];

        if ($this->profile_image) {
            $rules['profile_image'] = 'image|max:5120';
        }
        if ($this->banner_image) {
            $rules['banner_image'] = 'image|max:5120';
        }

        $this->validate($rules);

        $profileData = [
            'username' => $this->username,
            'bio' => $this->bio,
            'primary_niche' => ! empty($this->primary_niche) ? Niche::from($this->primary_niche) : null,
            'content_types' => array_filter($this->content_types),
            'preferred_business_types' => array_filter($this->preferred_business_types),
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'county' => $this->county,
            'postal_code' => $this->postal_code,
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

        Toaster::success('Influencer settings updated successfully!');
    }

    public function render()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        return view('livewire.influencer.influencer-settings', [
            'user' => $user,
            'nicheOptions' => Niche::toOptions(),
            'businessIndustryOptions' => BusinessIndustry::toOptions(),
            'businessTypeOptions' => BusinessType::toOptions(),
            'compensationTypeOptions' => CompensationType::toOptions(),
            'socialPlatformOptions' => SocialPlatform::toOptions(),
        ]);
    }
}
