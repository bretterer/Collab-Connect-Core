<?php

namespace App\Livewire\LinkInBio\Sections\Header;

use App\Models\Influencer;
use Livewire\Component;

class Show extends Component
{
    public Influencer $influencer;

    public bool $enabled = true;

    public string $profilePictureShape = 'round';

    public int $profilePictureSize = 100;

    public bool $profilePictureBorder = true;

    public string $displayName = '';

    public string $displayNameSize = 'medium';

    public string $location = '';

    public string $bio = '';

    public bool $showShareButton = true;

    public function mount(Influencer $influencer, array $settings = []): void
    {
        $this->influencer = $influencer;

        $this->enabled = $settings['enabled'] ?? true;
        $this->profilePictureShape = $settings['profilePictureShape'] ?? 'round';
        $this->profilePictureSize = $settings['profilePictureSize'] ?? 100;
        $this->profilePictureBorder = $settings['profilePictureBorder'] ?? true;
        $this->displayName = $settings['displayName'] ?? $influencer->user->name ?? '';
        $this->displayNameSize = $settings['displayNameSize'] ?? 'medium';
        $this->location = $settings['location'] ?? '';
        $this->bio = $settings['bio'] ?? '';
        $this->showShareButton = $settings['showShareButton'] ?? true;

        // Set defaults from influencer if not provided
        if (empty($this->displayName)) {
            $this->displayName = $influencer->user->name ?? $influencer->username ?? '';
        }

        if (empty($this->location) && $influencer->city && $influencer->state) {
            $this->location = "{$influencer->city}, {$influencer->state}";
        }

        if (empty($this->bio)) {
            $this->bio = $influencer->bio ?? '';
        }
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.header.show');
    }
}
