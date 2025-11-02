<?php

namespace App\Livewire\Profile;

use App\Models\ReferralEnrollment;
use Flux\Flux;
use Illuminate\Support\Str;
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

    public bool $showPayPalStep = false;

    public ?ReferralEnrollment $enrollment = null;

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

        // Check if enrolled in referral program from database
        $this->enrollment = $user->referralEnrollment;
        $this->isEnrolled = $this->enrollment !== null;

        // Generate referral link if enrolled
        if ($this->isEnrolled && $this->enrollment) {
            $this->referralLink = url('/register?ref='.$this->enrollment->code);
        }
    }

    public function enrollInProgram()
    {
        $user = auth()->user();

        // Check if already enrolled
        if ($user->referralEnrollment) {
            Flux::toast(
                heading: 'Already Enrolled',
                text: 'You are already enrolled in the referral program.',
                variant: 'error',
            );

            return;
        }

        // Create referral enrollment
        $enrollment = ReferralEnrollment::create([
            'user_id' => $user->id,
            'code' => strtoupper(Str::ulid()),
            'current_percentage' => 10, // Default 10% commission
        ]);

        // Refresh the user's relationship
        $user->load('referralEnrollment');

        $this->enrollment = $enrollment->fresh();
        $this->isEnrolled = true;
        $this->showPayPalStep = true;
        $this->referralLink = url('/register?ref='.$enrollment->code);

        Flux::toast(
            heading: 'Enrolled!',
            text: 'You have successfully enrolled in the referral program. Connect your PayPal account to receive payouts.',
            variant: 'success',
        );
    }

    public function skipPayPalSetup()
    {
        $this->showPayPalStep = false;
        Flux::toast(
            heading: 'PayPal Setup Skipped',
            text: 'You can connect your PayPal account anytime from your referral dashboard.',
            variant: 'info',
        );
    }

    public function copyReferralLink()
    {
        $this->copied = true;

        // Reset the copied state after 2 seconds
        $this->dispatch('link-copied');
    }
}
