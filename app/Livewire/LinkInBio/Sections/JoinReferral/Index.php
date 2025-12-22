<?php

namespace App\Livewire\LinkInBio\Sections\JoinReferral;

use App\Livewire\LinkInBio\Contracts\SectionContract;
use App\Livewire\LinkInBio\Traits\HasSectionSettings;
use App\Livewire\Traits\EnforcesTierAccess;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component implements SectionContract
{
    use EnforcesTierAccess;
    use HasSectionSettings;

    public bool $enabled = false;

    public string $text = 'Join CollabConnect';

    public string $style = 'secondary';

    public string $buttonColor = '#000000';

    public static function sectionKey(): string
    {
        return 'joinReferral';
    }

    public static function defaultSettings(): array
    {
        return [
            'enabled' => false,
            'text' => 'Join CollabConnect',
            'style' => 'secondary',
            'buttonColor' => '#000000',
        ];
    }

    public function loadSettings(array $settings): void
    {
        $this->enabled = $settings['enabled'] ?? false;
        $this->text = $settings['text'] ?? 'Join CollabConnect';
        $this->style = $settings['style'] ?? 'secondary';
        $this->buttonColor = $settings['buttonColor'] ?? '#000000';
    }

    public function toSettingsArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'text' => $this->text,
            'style' => $this->style,
            'buttonColor' => $this->buttonColor,
        ];
    }

    public function mount(array $settings = []): void
    {
        $merged = array_merge(static::defaultSettings(), $settings);
        $this->loadSettings($merged);
    }

    #[Computed]
    public function influencer()
    {
        return auth()->user()?->influencer;
    }

    #[Computed]
    public function isEnrolledInReferralProgram(): bool
    {
        return auth()->user()?->referralEnrollment !== null;
    }

    #[Computed]
    public function referralUrl(): string
    {
        $enrollment = auth()->user()?->referralEnrollment;

        if (! $enrollment) {
            return '';
        }

        return url('/r/'.$enrollment->code);
    }

    #[Computed]
    public function hasEliteAccess(): bool
    {
        $influencer = $this->influencer;

        if (! $influencer) {
            return false;
        }

        return $influencer->hasFeatureAccess('link_in_bio_customization');
    }

    #[Computed]
    public function requiredTierForAccess(): ?string
    {
        return $this->influencer?->getTierRequiredFor('link_in_bio_customization');
    }

    #[Computed]
    public function canShowSection(): bool
    {
        // Show section if user is enrolled in referral program (Elite access is handled via paywall)
        return $this->isEnrolledInReferralProgram;
    }

    public function render()
    {
        return view('livewire.link-in-bio.sections.join-referral.index');
    }
}
