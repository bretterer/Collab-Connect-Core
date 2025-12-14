<?php

namespace App\Livewire\LinkInBio\Sections\Links;

use App\Livewire\LinkInBio\Contracts\SectionContract;
use App\Livewire\LinkInBio\Traits\HasSectionSettings;
use Flux\Flux;
use Livewire\Component;

class Index extends Component implements SectionContract
{
    use HasSectionSettings;

    public bool $enabled = true;

    public string $title = '';

    public string $subtitle = '';

    public string $visibility = 'exposed';

    public string $layout = 'classic';

    public string $size = 'medium';

    public string $textAlign = 'center';

    public bool $shadow = true;

    public bool $outline = false;

    /** @var array<int, array{title: string, url: string, icon: string|null, enabled: bool}> */
    public array $items = [];

    public ?int $editingLinkIndex = null;

    public array $linkForm = [
        'title' => '',
        'url' => '',
        'icon' => null,
    ];

    public static function sectionKey(): string
    {
        return 'links';
    }

    public static function defaultSettings(): array
    {
        return [
            'enabled' => true,
            'title' => '',
            'subtitle' => '',
            'visibility' => 'exposed',
            'layout' => 'classic',
            'size' => 'medium',
            'textAlign' => 'center',
            'shadow' => true,
            'outline' => false,
            'items' => [],
        ];
    }

    public function loadSettings(array $settings): void
    {
        $this->enabled = $settings['enabled'] ?? true;
        $this->title = $settings['title'] ?? '';
        $this->subtitle = $settings['subtitle'] ?? '';
        $this->visibility = $settings['visibility'] ?? 'exposed';
        $this->layout = $settings['layout'] ?? 'classic';
        $this->size = $settings['size'] ?? 'medium';
        $this->textAlign = $settings['textAlign'] ?? 'center';
        $this->shadow = $settings['shadow'] ?? true;
        $this->outline = $settings['outline'] ?? false;
        $this->items = $settings['items'] ?? [];
    }

    public function toSettingsArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'visibility' => $this->visibility,
            'layout' => $this->layout,
            'size' => $this->size,
            'textAlign' => $this->textAlign,
            'shadow' => $this->shadow,
            'outline' => $this->outline,
            'items' => $this->items,
        ];
    }

    public function mount(array $settings = []): void
    {
        $merged = array_merge(static::defaultSettings(), $settings);
        $this->loadSettings($merged);
    }

    public function addLink(): void
    {
        $this->items[] = [
            'title' => '',
            'url' => '',
            'icon' => null,
            'enabled' => true,
        ];
        $this->dispatchSettingsUpdate();
    }

    public function removeLink(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->dispatchSettingsUpdate();
    }

    public function editLink(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $this->editingLinkIndex = $index;
        $this->linkForm = [
            'title' => $this->items[$index]['title'] ?? '',
            'url' => $this->items[$index]['url'] ?? '',
            'icon' => $this->items[$index]['icon'] ?? null,
        ];

        Flux::modal('edit-link-modal')->show();
    }

    public function cancelLinkEdit(): void
    {
        $this->editingLinkIndex = null;
        $this->linkForm = [
            'title' => '',
            'url' => '',
            'icon' => null,
        ];

        Flux::modal('edit-link-modal')->close();
    }

    public function saveLinkEdit(): void
    {
        if ($this->editingLinkIndex === null || ! isset($this->items[$this->editingLinkIndex])) {
            return;
        }

        $this->items[$this->editingLinkIndex]['title'] = $this->linkForm['title'];
        $this->items[$this->editingLinkIndex]['url'] = $this->linkForm['url'];
        $this->items[$this->editingLinkIndex]['icon'] = $this->linkForm['icon'] ?: null;

        $this->editingLinkIndex = null;
        $this->linkForm = [
            'title' => '',
            'url' => '',
            'icon' => null,
        ];

        $this->dispatchSettingsUpdate();
        Flux::modal('edit-link-modal')->close();
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.links.index');
    }
}
