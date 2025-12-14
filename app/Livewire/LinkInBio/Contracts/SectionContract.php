<?php

namespace App\Livewire\LinkInBio\Contracts;

interface SectionContract
{
    /**
     * Get the unique key for this section (used in settings JSON).
     */
    public static function sectionKey(): string;

    /**
     * Get the default settings for this section.
     */
    public static function defaultSettings(): array;

    /**
     * Load settings into component properties.
     */
    public function loadSettings(array $settings): void;

    /**
     * Build and return the settings array for this section.
     */
    public function toSettingsArray(): array;
}
