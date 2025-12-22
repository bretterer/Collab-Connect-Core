<?php

namespace App\Livewire\LinkInBio\Sections\WorkWithMe;

use App\Models\Influencer;
use Livewire\Component;

class Show extends Component
{
    public Influencer $influencer;

    public bool $enabled = true;

    public string $text = 'Work with me on CollabConnect';

    public string $style = 'primary';

    public string $profileUrl = '';

    public function mount(Influencer $influencer, array $settings = []): void
    {
        $this->influencer = $influencer;

        $this->enabled = $settings['enabled'] ?? true;
        $this->text = $settings['text'] ?? 'Work with me on CollabConnect';
        $this->style = $settings['style'] ?? 'primary';

        // Generate the profile URL
        $username = $influencer->username ?? $influencer->user_id;
        $this->profileUrl = route('influencer.profile', ['username' => $username]);
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.work-with-me.show');
    }
}
