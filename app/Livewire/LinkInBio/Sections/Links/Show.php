<?php

namespace App\Livewire\LinkInBio\Sections\Links;

use App\Models\Influencer;
use Livewire\Component;

class Show extends Component
{
    public bool $enabled = true;

    public string $size = 'medium';

    public string $textAlign = 'center';

    public bool $shadow = true;

    public bool $outline = false;

    /** @var array<int, array{title: string, url: string, icon: string|null, enabled: bool}> */
    public array $items = [];

    // Design settings passed from parent
    public string $containerStyle = 'round';

    public function mount(array $settings = [], array $designSettings = [], ?Influencer $influencer = null): void
    {
        $this->enabled = $settings['enabled'] ?? true;
        $this->size = $settings['size'] ?? 'medium';
        $this->textAlign = $settings['textAlign'] ?? 'center';
        $this->shadow = $settings['shadow'] ?? true;
        $this->outline = $settings['outline'] ?? false;
        $this->items = $settings['items'] ?? [];

        // Design settings
        $this->containerStyle = $designSettings['containerStyle'] ?? 'round';

        // Load default links from influencer socials if no items and influencer provided
        if (empty($this->items) && $influencer) {
            $this->items = $influencer->socials->map(fn ($social) => [
                'title' => $social->platform->label(),
                'url' => $social->url,
                'icon' => $social->platform->value,
                'enabled' => true,
            ])->toArray();
        }
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.links.show');
    }
}
