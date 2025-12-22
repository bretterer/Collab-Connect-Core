<?php

namespace App\Livewire\LinkInBio\Sections\JoinReferral;

use App\Models\Influencer;
use Livewire\Component;

class Show extends Component
{
    public Influencer $influencer;

    public bool $enabled = false;

    public string $text = 'Join CollabConnect';

    public string $style = 'secondary';

    public string $referralUrl = '';

    public function mount(Influencer $influencer, array $settings = []): void
    {
        $this->influencer = $influencer;

        $this->enabled = $settings['enabled'] ?? false;
        $this->text = $settings['text'] ?? 'Join CollabConnect';
        $this->style = $settings['style'] ?? 'secondary';

        // Generate the referral URL from the influencer's user's enrollment
        $enrollment = $influencer->user?->referralEnrollment;

        if ($enrollment) {
            $this->referralUrl = url('/r/'.$enrollment->code);
        }
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.join-referral.show');
    }
}
