<?php

namespace App\Livewire\LinkInBio;

use App\Livewire\LinkInBio\Sections\Header\Index as HeaderSection;
use App\Livewire\LinkInBio\Sections\Links\Index as LinksSection;
use App\Livewire\LinkInBio\Sections\RatesCard\Index as RatesCardSection;
use App\Livewire\LinkInBio\Sections\WorkWithMe\Index as WorkWithMeSection;
use App\Models\Influencer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Show extends Component
{
    public Influencer $influencer;

    public bool $isOwner = false;

    public bool $isDraft = true;

    // Design Settings (remains in parent)
    public string $themeColor = '#dc2626';

    public string $font = 'sans';

    public string $containerStyle = 'round';

    // Section settings arrays (passed to child components)
    public array $headerSettings = [];

    public array $linksSettings = [];

    public array $ratesSettings = [];

    public array $workWithMeSettings = [];

    public function mount(string $username): void
    {
        $influencer = Influencer::where('username', $username)
            ->with(['linkInBioSettings', 'user', 'socials'])
            ->first();

        if (! $influencer) {
            abort(404, 'Profile not found');
        }

        $this->influencer = $influencer;

        // Check if current user is the owner
        $currentUser = Auth::user();
        $this->isOwner = $currentUser && $currentUser->influencer?->id === $influencer->id;

        // Load saved settings
        $settings = $influencer->linkInBioSettings;
        $this->isDraft = ! $settings?->is_published;

        // If not published and not the owner, show 404
        if ($this->isDraft && ! $this->isOwner) {
            abort(404, 'Profile not found');
        }

        // Load settings - use saved settings if available, otherwise use defaults
        if ($settings) {
            $this->loadFromSettings($settings->getMergedSettings());
        } else {
            $this->loadDefaultsFromInfluencer();
        }
    }

    protected function loadFromSettings(array $settings): void
    {
        // Design settings
        $this->themeColor = $settings['design']['themeColor'] ?? '#dc2626';
        $this->font = $settings['design']['font'] ?? 'sans';
        $this->containerStyle = $settings['design']['containerStyle'] ?? 'round';

        // Section settings (passed to children)
        $this->headerSettings = $settings['header'] ?? HeaderSection::defaultSettings();
        $this->linksSettings = $settings['links'] ?? LinksSection::defaultSettings();
        $this->ratesSettings = $settings['rates'] ?? RatesCardSection::defaultSettings();
        $this->workWithMeSettings = $settings['workWithMe'] ?? WorkWithMeSection::defaultSettings();

        // Ensure displayName has a fallback
        if (empty($this->headerSettings['displayName'])) {
            $this->headerSettings['displayName'] = $this->influencer->user->name ?? '';
        }
    }

    protected function loadDefaultsFromInfluencer(): void
    {
        // Header defaults
        $this->headerSettings = array_merge(
            HeaderSection::defaultSettings(),
            [
                'displayName' => $this->influencer->user->name ?? $this->influencer->username,
                'location' => $this->influencer->city && $this->influencer->state
                    ? "{$this->influencer->city}, {$this->influencer->state}"
                    : '',
                'bio' => $this->influencer->bio ?? '',
            ]
        );

        // Links defaults
        $this->linksSettings = array_merge(
            LinksSection::defaultSettings(),
            [
                'items' => $this->influencer->socials->map(fn ($social) => [
                    'title' => $social->platform->label(),
                    'url' => $social->url,
                    'icon' => $social->platform->value,
                    'enabled' => true,
                ])->toArray(),
            ]
        );

        // Rates defaults
        $ratesItems = [];
        if ($this->influencer->min_rate) {
            $ratesItems = [
                [
                    'platform' => 'Content Creation',
                    'rate' => '$'.number_format($this->influencer->min_rate),
                    'description' => 'Starting rates',
                    'enabled' => true,
                ],
            ];
        }
        $this->ratesSettings = array_merge(
            RatesCardSection::defaultSettings(),
            ['items' => $ratesItems]
        );

        // Work With Me defaults
        $this->workWithMeSettings = WorkWithMeSection::defaultSettings();
    }

    public function getDesignSettings(): array
    {
        return [
            'themeColor' => $this->themeColor,
            'containerStyle' => $this->containerStyle,
        ];
    }

    public function render()
    {
        return view('livewire.link-in-bio.show');
    }
}
