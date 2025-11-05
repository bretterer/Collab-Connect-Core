<?php

namespace App\Livewire\Profile;

use App\Enums\PercentageChangeType;
use App\Enums\ReferralStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPercentageHistory;
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

    public bool $showPayPalStep = false;

    public ?ReferralEnrollment $enrollment = null;

    public string $referralLink = 'https://collabconnect.test/r/ABC123XYZ';

    public array $stats = [
        'pending_count' => 0,
        'active_count' => 0,
        'total_count' => 0,
        'pending_payout' => 0,
        'lifetime_earnings' => 0,
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
            $this->referralLink = url('/r/'.$this->enrollment->code);
            $this->stats = $this->refreshReferralStats();
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
        ]);

        ReferralPercentageHistory::create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::ENROLLMENT,
            'reason' => 'Initial enrollment percentage',
            'changed_by_user_id' => null,
        ]);

        // Refresh the user's relationship
        $user->load('referralEnrollment');

        $this->enrollment = $enrollment->fresh();
        $this->isEnrolled = true;
        $this->showPayPalStep = true;
        $this->referralLink = url('/r/'.$enrollment->code);

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

    public function refreshReferralStats()
    {
        $enrollment = auth()->user()->referralEnrollment;

        $referrals = $enrollment->referrals()->get();

        $stats = [
            'pending_count' => $referrals->where('status', ReferralStatus::PENDING)->count(),
            'active_count' => $referrals->where('status', ReferralStatus::ACTIVE)->count(),
            'total_count' => $referrals->whereNotIn(
                'status',
                [
                    ReferralStatus::CANCELLED,
                    ReferralStatus::CHURNED,
                ]
            )
                ->count(),
            'pending_payout' => 0,
            'lifetime_earnings' => 0,
        ];

        return $stats;

    }
}
