<?php

namespace App\Livewire\LinkInBio\Sections\Footer;

use App\Livewire\LinkInBio\Contracts\SectionContract;
use App\Livewire\LinkInBio\Traits\HasSectionSettings;
use Livewire\Component;

class Index extends Component implements SectionContract
{
    use HasSectionSettings;

    public bool $enabled = true;

    /**
     * Get the unique key for this section (used in settings JSON).
     */
    public static function sectionKey(): string
    {
        return 'footer';
    }

    /**
     * Get the default settings for this section.
     */
    public static function defaultSettings(): array
    {
        return [
            'enabled' => true,
        ];
    }

    /**
     * Load settings into component properties.
     */
    public function loadSettings(array $settings): void
    {
        // No settings for footer yet
    }

    /**
     * Build and return the settings array for this section.
     */
    public function toSettingsArray(): array
    {
        return [
            // No settings for footer yet
        ];
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.footer.index');
    }
}
