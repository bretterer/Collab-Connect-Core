<?php

namespace App\Livewire\Admin\Users\Modals;

use App\Facades\SubscriptionLimits;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use App\Subscription\SubscriptionMetadataSchema;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class GrantCreditsModal extends Component
{
    public ?int $userId = null;

    public int $credits = 1;

    public string $reason = '';

    public bool $isProcessing = false;

    #[On('open-grant-credits-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        $this->credits = 1;
        $this->reason = '';
        Flux::modal('grant-credits-modal')->show();
    }

    #[Computed]
    public function user(): ?User
    {
        return $this->userId ? User::find($this->userId) : null;
    }

    #[Computed]
    public function billable(): Business|Influencer|null
    {
        $user = $this->user;

        if (! $user) {
            return null;
        }

        if ($user->isBusinessAccount()) {
            return $user->businesses()
                ->wherePivot('role', 'owner')
                ->first();
        }

        if ($user->isInfluencerAccount() && $user->influencer) {
            return $user->influencer;
        }

        return null;
    }

    #[Computed]
    public function currentCredits(): int
    {
        $billable = $this->billable;
        if (! $billable) {
            return 0;
        }

        return SubscriptionLimits::getRemainingCredits(
            $billable,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
    }

    public function grantCredits(): void
    {
        $this->validate([
            'credits' => 'required|integer|min:1|max:100',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->isProcessing = true;

            $billable = $this->billable;

            if (! $billable) {
                throw new \Exception('No billable profile found.');
            }

            $oldCredits = SubscriptionLimits::getRemainingCredits(
                $billable,
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
            );

            $creditsToGrant = $this->credits;
            $newCredits = SubscriptionLimits::addCredits(
                $billable,
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS,
                $creditsToGrant
            );

            AuditLog::log(
                action: 'credit.grant',
                auditable: $billable,
                oldValues: ['profile_promotion_credits' => $oldCredits],
                newValues: ['profile_promotion_credits' => $newCredits],
                metadata: [
                    'credits_added' => $creditsToGrant,
                    'reason' => $this->reason,
                    'user_id' => $this->userId,
                    'user_name' => $this->user?->name,
                ]
            );

            $this->credits = 1;
            $this->reason = '';
            Flux::modal('grant-credits-modal')->close();
            Toaster::success("Successfully granted {$creditsToGrant} promotion credits.");

            $this->dispatch('credits-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to grant credits: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.grant-credits-modal');
    }
}
