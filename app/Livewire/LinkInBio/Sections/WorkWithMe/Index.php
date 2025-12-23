<?php

namespace App\Livewire\LinkInBio\Sections\WorkWithMe;

use App\Livewire\LinkInBio\Contracts\SectionContract;
use App\Livewire\LinkInBio\Traits\HasSectionSettings;
use App\Livewire\Traits\EnforcesTierAccess;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component implements SectionContract
{
    use EnforcesTierAccess;
    use HasSectionSettings;

    public bool $enabled = true;

    public string $text = 'Work With Me';

    public string $style = 'secondary';

    public string $buttonColor = '#000000';

    public static function sectionKey(): string
    {
        return 'workWithMe';
    }

    public static function defaultSettings(): array
    {
        return [
            'enabled' => true,
            'text' => 'Work With Me',
            'style' => 'secondary',
            'buttonColor' => '#000000',
        ];
    }

    public function loadSettings(array $settings): void
    {
        $this->enabled = $settings['enabled'] ?? true;
        $this->text = $settings['text'] ?? 'Work With Me';
        $this->style = $settings['style'] ?? 'secondary';
        $this->buttonColor = $settings['buttonColor'] ?? '#000000';
    }

    public function toSettingsArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'text' => $this->text,
            'style' => $this->style,
            'buttonColor' => $this->buttonColor,
        ];
    }

    public function mount(array $settings = []): void
    {
        $merged = array_merge(static::defaultSettings(), $settings);
        $this->loadSettings($merged);
    }

    #[Computed]
    public function influencer()
    {
        return auth()->user()?->influencer;
    }

    #[Computed]
    public function profileUrl(): string
    {
        $influencer = $this->influencer;

        if (! $influencer) {
            return '';
        }

        $username = $influencer->username ?: $influencer->user_id;

        return route('influencer.profile', ['username' => $username]);
    }

    #[Computed]
    public function isProfileSearchable(): bool
    {
        return $this->influencer?->is_searchable ?? false;
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
    public function requiredTierForCustomization(): ?string
    {
        return $this->influencer?->getTierRequiredFor('link_in_bio_customization');
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.work-with-me.index');
    }
}
