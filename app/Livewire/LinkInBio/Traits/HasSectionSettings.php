<?php

namespace App\Livewire\LinkInBio\Traits;

trait HasSectionSettings
{
    /**
     * Dispatch settings update to parent component.
     */
    public function dispatchSettingsUpdate(): void
    {
        $this->dispatch('section-updated', [
            'key' => static::sectionKey(),
            'settings' => $this->toSettingsArray(),
        ]);
    }

    /**
     * Hook called when any property is updated.
     */
    public function updated($property): void
    {
        $this->dispatchSettingsUpdate();
    }
}
