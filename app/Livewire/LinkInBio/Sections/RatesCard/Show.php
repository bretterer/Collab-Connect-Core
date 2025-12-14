<?php

namespace App\Livewire\LinkInBio\Sections\RatesCard;

use Livewire\Component;

class Show extends Component
{
    public bool $enabled = true;

    public string $title = 'My Rates';

    public string $size = 'small';

    /** @var array<int, array{platform: string, rate: string, description: string, enabled: bool}> */
    public array $items = [];

    // Design settings passed from parent
    public string $themeColor = '#000000';

    public function mount(array $settings = [], array $designSettings = []): void
    {
        $this->enabled = $settings['enabled'] ?? true;
        $this->title = $settings['title'] ?? 'My Rates';
        $this->size = $settings['size'] ?? 'small';
        $this->items = $settings['items'] ?? [];

        // Design settings
        $this->themeColor = $designSettings['themeColor'] ?? '#000000';
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.rates-card.show');
    }
}
