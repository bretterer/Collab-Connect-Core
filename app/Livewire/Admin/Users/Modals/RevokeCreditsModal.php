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

class RevokeCreditsModal extends Component
{
    public ?int $userId = null;

    public int $credits = 1;

    public string $reason = '';

    public bool $isProcessing = false;

    #[On('open-revoke-credits-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        $this->credits = 1;
        $this->reason = '';
        Flux::modal('revoke-credits-modal')->show();
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

    #[Computed]
    public function maxRevokable(): int
    {
        return $this->currentCredits;
    }

    public function revokeCredits(): void
    {
        $this->validate([
            'credits' => ['required', 'integer', 'min:1', 'max:'.$this->maxRevokable],
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

            $creditsToRevoke = $this->credits;
            SubscriptionLimits::deductCredit(
                $billable,
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS,
                $creditsToRevoke
            );

            $newCredits = SubscriptionLimits::getRemainingCredits(
                $billable,
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
            );

            AuditLog::log(
                action: 'credit.revoke',
                auditable: $billable,
                oldValues: ['profile_promotion_credits' => $oldCredits],
                newValues: ['profile_promotion_credits' => $newCredits],
                metadata: [
                    'credits_removed' => $creditsToRevoke,
                    'reason' => $this->reason,
                    'user_id' => $this->userId,
                    'user_name' => $this->user?->name,
                ]
            );

            $this->credits = 1;
            $this->reason = '';
            Flux::modal('revoke-credits-modal')->close();
            Toaster::success("Successfully revoked {$creditsToRevoke} promotion credits.");

            $this->dispatch('credits-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to revoke credits: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.revoke-credits-modal');
    }
}
