<?php

namespace App\Livewire\Profile;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Referrals extends Component
{
    // State properties
    public bool $isEligible = false;
    public bool $isEnrolled = false;
    public bool $isBusinessOwner = false; // Is this user a business owner
    public bool $copied = false;

    public string $referralLink = 'https://collabconnect.test/register?ref=ABC123XYZ';

    public array $stats = [
        'pending_count' => 3,
        'active_count' => 12,
        'total_count' => 18,
        'pending_payout' => 47.50,
        'lifetime_earnings' => 342.75,
    ];

    public function mount()
    {
        $user = auth()->user();

        // Determine if user has a subscription (eligible)
        if ($user->isBusinessAccount()) {
            $business = $user->currentBusiness;

            if ($business && $business->subscribed()) {
                $this->isEligible = true;
            }

            // Check if user is the business owner
            if ($business && $business->owner->contains($user)) {
                $this->isBusinessOwner = true;
            }
        } elseif ($user->isInfluencerAccount()) {
            $influencer = $user->influencer;

            if ($influencer && $influencer->subscribed()) {
                $this->isEligible = true;
            }

            // Influencers are always "owners" of their account
            $this->isBusinessOwner = true;
        }

        // TODO: Check if enrolled in referral program from database
        // For now, this remains false until backend implementation
        $this->isEnrolled = false;
    }

    public function enrollInProgram()
    {
        $this->isEnrolled = true;
        session()->flash('success', 'Successfully enrolled in the referral program!');
    }

    public function copyReferralLink()
    {
        $this->copied = true;

        // Reset the copied state after 2 seconds
        $this->dispatch('link-copied');
    }
}
