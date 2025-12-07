<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ComingSoon extends Component
{
    public string $title = 'Coming Soon';

    public string $description = 'We\'re working hard to bring you this exciting new feature. Stay tuned!';

    public array $features = [];

    public ?string $icon = 'rocket-launch';

    public ?string $expectedDate = null;

    public bool $showNotifyButton = false;

    public function mount(
        ?string $title = null,
        ?string $description = null,
        ?array $features = null,
        ?string $icon = null,
        ?string $expectedDate = null,
        bool $showNotifyButton = false
    ): void {
        if ($title) {
            $this->title = $title;
        }
        if ($description) {
            $this->description = $description;
        }
        if ($features) {
            $this->features = $features;
        }
        if ($icon) {
            $this->icon = $icon;
        }
        if ($expectedDate) {
            $this->expectedDate = $expectedDate;
        }
        $this->showNotifyButton = $showNotifyButton;
    }

    public function render()
    {
        return view('livewire.components.coming-soon');
    }
}
