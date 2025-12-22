<?php

namespace App\Livewire\LinkInBio;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\EnforcesTierAccess;
use App\Models\LinkInBioSettings;
use Laravel\Pennant\Feature;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class Index extends BaseComponent
{
    use EnforcesTierAccess;

    public string $themeColor = '#dc2626';

    public string $font = 'sans';

    public string $containerStyle = 'round';

    public array $headerSettings = [];

    public array $linksSettings = [];

    public array $ratesSettings = [];

    public array $footerSettings = [];

    public array $workWithMeSettings = [];

    public bool $isPublished = false;

    public function mount(): void
    {
        $influencer = auth()->user()?->influencer;

        if (! $influencer) {
            return;
        }

        // Get or create settings for this influencer
        $settings = $influencer->linkInBioSettings;

        if ($settings) {
            $mergedSettings = $settings->getMergedSettings();

            // Load design settings
            $this->themeColor = $mergedSettings['design']['themeColor'] ?? '#dc2626';
            $this->font = $mergedSettings['design']['font'] ?? 'sans';
            $this->containerStyle = $mergedSettings['design']['containerStyle'] ?? 'round';

            // Load section settings
            $this->headerSettings = $mergedSettings['header'] ?? [];
            $this->linksSettings = $mergedSettings['links'] ?? [];
            $this->ratesSettings = $mergedSettings['rates'] ?? [];
            $this->footerSettings = ['enabled' => true];
            $this->workWithMeSettings = $mergedSettings['workWithMe'] ?? [];

            // Load publish status
            $this->isPublished = $settings->is_published;
        } else {
            // Set defaults
            $defaults = LinkInBioSettings::getDefaultSettings();

            $this->themeColor = $defaults['design']['themeColor'];
            $this->font = $defaults['design']['font'];
            $this->containerStyle = $defaults['design']['containerStyle'];

            $this->headerSettings = array_merge($defaults['header'], [
                'displayName' => auth()->user()?->name ?? '',
            ]);
            $this->linksSettings = $defaults['links'];
            $this->ratesSettings = $defaults['rates'];
            $this->footerSettings = ['enabled' => true];
            $this->workWithMeSettings = $defaults['workWithMe'];
        }
    }

    #[On('section-updated')]
    public function handleSectionUpdate(string $section, array $settings): void
    {
        match ($section) {
            'header' => $this->headerSettings = $settings,
            'links' => $this->linksSettings = $settings,
            'rates' => $this->ratesSettings = $settings,
            'footer' => $this->footerSettings = $settings,
            'workWithMe' => $this->workWithMeSettings = $settings,
            default => null,
        };
    }

    // Note: Tier access is enforced via UI overlay - no need for updated() hook checks

    public function hasUsername(): bool
    {
        return ! empty(auth()->user()?->influencer?->username);
    }

    public function getPublicUrl(): string
    {
        $username = auth()->user()?->influencer?->username ?? 'username';

        return route('public.link-in-bio', ['username' => $username]);
    }

    #[Computed]
    public function influencer()
    {
        return auth()->user()?->influencer;
    }

    #[Computed]
    public function hasCustomizationAccess(): bool
    {
        $influencer = $this->influencer;

        if (! $influencer) {
            return false;
        }

        return $influencer->hasFeatureAccess('link_in_bio_customization');
    }

    #[Computed]
    public function currentTier(): ?string
    {
        return $this->influencer?->getSubscriptionTier();
    }

    #[Computed]
    public function requiredTierForCustomization(): ?string
    {
        return $this->influencer?->getTierRequiredFor('link_in_bio_customization');
    }

    public function togglePublish(): void
    {
        $this->save();
    }

    public function save(): void
    {
        $influencer = auth()->user()?->influencer;

        if (! $influencer) {
            Toaster::error('You must have an influencer profile to save settings.');

            return;
        }

        $settings = [
            'design' => [
                'themeColor' => $this->themeColor,
                'font' => $this->font,
                'containerStyle' => $this->containerStyle,
            ],
            'header' => $this->headerSettings,
            'links' => $this->linksSettings,
            'rates' => $this->ratesSettings,
            'workWithMe' => $this->workWithMeSettings,
        ];

        $influencer->linkInBioSettings()->updateOrCreate(
            ['influencer_id' => $influencer->id],
            [
                'settings' => $settings,
                'is_published' => $this->isPublished,
            ]
        );

        Toaster::success('Your Link in Bio settings have been saved.');
    }

    public function render()
    {
        if (Feature::for(auth()->user())->active('link-in-bio')) {
            return view('livewire.link-in-bio.index');
        }

        return view('livewire.components.coming-soon', [
            'title' => 'Link in Bio',
            'description' => 'Create your personalized link page to share with your audience.',
            'features' => ['Custom branded page', 'Track click analytics', 'Unlimited links'],
            'icon' => 'link',
            'expectedDate' => null,
            'showNotifyButton' => false,
        ]);
    }
}
