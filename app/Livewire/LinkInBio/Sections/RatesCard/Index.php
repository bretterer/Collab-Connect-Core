<?php

namespace App\Livewire\LinkInBio\Sections\RatesCard;

use App\Livewire\LinkInBio\Contracts\SectionContract;
use App\Livewire\LinkInBio\Traits\HasSectionSettings;
use Livewire\Component;

class Index extends Component implements SectionContract
{
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

    public function addRate(): void
    {
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
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->dispatchSettingsUpdate();
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.rates-card.index');
    }
}
