<?php

namespace App\Livewire\LinkInBio\Sections\Header;

use App\Livewire\LinkInBio\Contracts\SectionContract;
use App\Livewire\LinkInBio\Traits\HasSectionSettings;
use Livewire\Component;

class Index extends Component implements SectionContract
{
    use HasSectionSettings;

    public bool $enabled = true;

    public string $profilePictureShape = 'round';

    public string $displayName = '';

    public string $displayNameSize = 'medium';

    public string $location = '';

    public string $bio = '';

    public string $headerFormat = 'vertical';

    public bool $showShareButton = true;

    public static function sectionKey(): string
    {
        return 'header';
    }

    public static function defaultSettings(): array
    {
        return [
            'enabled' => true,
            'profilePictureShape' => 'round',
            'displayName' => '',
            'displayNameSize' => 'medium',
            'location' => '',
            'bio' => '',
            'headerFormat' => 'vertical',
            'showShareButton' => true,
        ];
    }

    public function loadSettings(array $settings): void
    {
        $this->enabled = $settings['enabled'] ?? true;
        $this->profilePictureShape = $settings['profilePictureShape'] ?? 'round';
        $this->displayName = $settings['displayName'] ?? '';
        $this->displayNameSize = $settings['displayNameSize'] ?? 'medium';
        $this->location = $settings['location'] ?? '';
        $this->bio = $settings['bio'] ?? '';
        $this->headerFormat = $settings['headerFormat'] ?? 'vertical';
        $this->showShareButton = $settings['showShareButton'] ?? true;
    }

    public function toSettingsArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'profilePictureShape' => $this->profilePictureShape,
            'displayName' => $this->displayName,
            'displayNameSize' => $this->displayNameSize,
            'location' => $this->location,
            'bio' => $this->bio,
            'headerFormat' => $this->headerFormat,
            'showShareButton' => $this->showShareButton,
        ];
    }

    public function mount(array $settings = []): void
    {
        $merged = array_merge(static::defaultSettings(), $settings);
        $this->loadSettings($merged);
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.header.index');
    }
}
