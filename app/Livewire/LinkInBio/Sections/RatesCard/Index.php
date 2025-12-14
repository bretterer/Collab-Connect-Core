<?php

namespace App\Livewire\LinkInBio\Sections\RatesCard;

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

    public string $title = 'My Rates';

    public string $subtitle = '';

    public string $size = 'small';

    /** @var array<int, array{platform: string, rate: string, description: string, enabled: bool}> */
    public array $items = [];

    public static function sectionKey(): string
    {
        return 'rates';
    }

    public static function defaultSettings(): array
    {
        return [
            'enabled' => true,
            'title' => 'My Rates',
            'subtitle' => '',
            'size' => 'small',
            'items' => [],
        ];
    }

    public function loadSettings(array $settings): void
    {
        $this->enabled = $settings['enabled'] ?? true;
        $this->title = $settings['title'] ?? 'My Rates';
        $this->subtitle = $settings['subtitle'] ?? '';
        $this->size = $settings['size'] ?? 'small';
        $this->items = $settings['items'] ?? [];
    }

    public function toSettingsArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'size' => $this->size,
            'items' => $this->items,
        ];
    }

    public function mount(array $settings = []): void
    {
        $merged = array_merge(static::defaultSettings(), $settings);
        $this->loadSettings($merged);
    }

    /**
     * Override the updated hook to dispatch settings.
     * Note: Tier access is enforced via UI overlay and in action methods.
     */
    public function updated($property): void
    {
        $this->dispatchSettingsUpdate();
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
    public function requiredTierForCustomization(): ?string
    {
        return $this->influencer?->getTierRequiredFor('link_in_bio_customization');
    }

    public function addRate(): void
    {
        // Enforce tier access for adding rates
        $this->enforceTierAccess('link_in_bio_customization');

        $this->items[] = [
            'platform' => '',
            'rate' => '',
            'description' => '',
            'enabled' => true,
        ];
        $this->dispatchSettingsUpdate();
    }

    public function removeRate(int $index): void
    {
        // Enforce tier access for removing rates
        $this->enforceTierAccess('link_in_bio_customization');

        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->dispatchSettingsUpdate();
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.rates-card.index');
    }
}
