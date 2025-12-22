<?php

namespace App\Livewire\Admin\Referrals;

use App\Enums\PercentageChangeType;
use App\Enums\ReferralStatus;
use App\Models\ReferralPercentageHistory;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReferralShow extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public string $paypal_email = '';

    public int $currentPercentage = 0;

    public bool $showPercentageModal = false;

    public string $percentageChangeType = '';

    public int $newPercentage = 0;

    public ?string $expiresAt = null;

    public ?int $months = null;

    public string $reason = '';

    public function mount(User $user)
    {
        $this->user = $user->load([
            'referralEnrollment.referrals.referred',
            'referralEnrollment.payouts',
            'referralEnrollment.percentageHistory.changedBy',
        ]);

        // Initialize form fields
        $this->name = $this->user->name;
        $this->email = $this->user->email;

        if ($this->user->referralEnrollment) {
            $this->paypal_email = $this->user->referralEnrollment->paypal_email ?? '';

            // Get the current percentage from latest history
            $latestPercentage = $this->user->referralEnrollment->percentageHistory()
                ->latest()
                ->first();

            $this->currentPercentage = $latestPercentage?->new_percentage ?? 0;
        }
    }

    public function openPercentageModal()
    {
        $this->showPercentageModal = true;
        $this->percentageChangeType = PercentageChangeType::PERMANENT->value;
        $this->newPercentage = $this->currentPercentage;
        $this->expiresAt = null;
        $this->months = null;
        $this->reason = '';
    }

    public function closePercentageModal()
    {
        $this->showPercentageModal = false;
        $this->resetPercentageForm();
    }

    public function savePercentageChange()
    {
        $this->validate([
            'newPercentage' => 'required|integer|min:0|max:100',
            'percentageChangeType' => 'required|in:'.PercentageChangeType::PERMANENT->value.','.PercentageChangeType::TEMPORARY_DATE->value,
            'expiresAt' => 'nullable|required_if:percentageChangeType,'.PercentageChangeType::TEMPORARY_DATE->value.'|date|after:today',
            'reason' => 'required|string|max:500',
        ]);

        $oldPercentage = $this->user->referralEnrollment->percentageHistory()->latest()->first()?->new_percentage ?? 0;

        ReferralPercentageHistory::create([
            'referral_enrollment_id' => $this->user->referralEnrollment->id,
            'old_percentage' => $oldPercentage,
            'new_percentage' => $this->newPercentage,
            'change_type' => $this->percentageChangeType,
            'expires_at' => $this->percentageChangeType === PercentageChangeType::TEMPORARY_DATE->value ? $this->expiresAt : null,
            'months_remaining' => $this->percentageChangeType === PercentageChangeType::TEMPORARY_MONTHS->value ? $this->months : null,
            'reason' => $this->reason,
            'changed_by_user_id' => auth()->id(),
        ]);

        // Update current percentage
        $this->currentPercentage = $this->newPercentage;

        // Reload the user with fresh data
        $this->user->load('referralEnrollment.percentageHistory.changedBy');

        $this->closePercentageModal();

        Flux::toast('Percentage updated successfully.', variant: 'success');
    }

    public function save()
    {
        $this->validate([
            'paypal_email' => 'nullable|email|max:255',
        ]);

        // Update PayPal email if enrollment exists
        if ($this->user->referralEnrollment) {
            $this->user->referralEnrollment->update([
                'paypal_email' => $this->paypal_email,
            ]);
        }

        Flux::toast('PayPal email updated successfully.', variant: 'success');
    }

    public function disconnectPayPal()
    {
        if ($this->user->referralEnrollment) {
            $this->user->referralEnrollment->disconnectPayPal();
            $this->paypal_email = '';
            Flux::toast('PayPal disconnected successfully.', variant: 'success');
        }
    }

    public function copyReferralLink()
    {
        Flux::toast('Referral link copied to clipboard.', variant: 'success');
    }

    public function getChangeTypeOptions()
    {
        // Only return PERMANENT and TEMPORARY_DATE options
        return collect(PercentageChangeType::toOptions())
            ->filter(fn ($option) => in_array($option['value'], [
                PercentageChangeType::PERMANENT->value,
                PercentageChangeType::TEMPORARY_DATE->value,
            ]))
            ->values()
            ->toArray();
    }

    private function resetPercentageForm()
    {
        $this->percentageChangeType = '';
        $this->newPercentage = 0;
        $this->expiresAt = null;
        $this->months = null;
        $this->reason = '';
    }

    public function render()
    {
        // dd($this->user->referralEnrollment);
        $stats = [
            'total_referrals' => $this->user->referralEnrollment?->referrals()->count() ?? 0,
            'active_referrals' => $this->user->referralEnrollment?->getActiveReferralCount() ?? 0,
            'pending_referrals' => $this->user->referralEnrollment?->referrals()->where('status', ReferralStatus::PENDING)->count() ?? 0,
            'conversion_rate' => $this->calculateConversionRate(),
            'lifetime_earnings' => $this->user->referralEnrollment?->getLifetimeEarnings() ?? 0,
            'pending_payout' => $this->user->referralEnrollment?->getPendingPayout()?->amount ?? 0,
        ];

        $referralLink = route('affiliate.redirect', ['code' => $this->user->referralEnrollment?->code ?? '']);

        return view('livewire.admin.referrals.referral-show', [
            'stats' => $stats,
            'referralLink' => $referralLink,
        ]);
    }

    private function calculateConversionRate(): float
    {
        $enrollment = $this->user->referralEnrollment;

        if (! $enrollment) {
            return 0;
        }

        $totalReferrals = $enrollment->referrals()->count();

        if ($totalReferrals === 0) {
            return 0;
        }

        $activeReferrals = $enrollment->getActiveReferralCount();

        return round(($activeReferrals / $totalReferrals) * 100, 2);
    }
}
