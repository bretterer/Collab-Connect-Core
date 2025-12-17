<?php

namespace App\Livewire\Admin\Users\Modals;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Flux\Flux;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class CancelSubscriptionModal extends Component
{
    public ?int $userId = null;

    public bool $isProcessing = false;

    #[On('open-cancel-subscription-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        Flux::modal('cancel-subscription-modal')->show();
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

    protected function setCashierModel(): void
    {
        $billable = $this->billable;

        if ($billable instanceof Business) {
            Cashier::useCustomerModel(Business::class);
        } elseif ($billable instanceof Influencer) {
            Cashier::useCustomerModel(Influencer::class);
        }
    }

    public function cancelSubscription(): void
    {
        try {
            $this->isProcessing = true;
            $this->setCashierModel();

            $subscription = $this->billable?->subscription('default');

            if (! $subscription) {
                throw new \Exception('No subscription found.');
            }

            $subscription->cancel();
            Flux::modal('cancel-subscription-modal')->close();
            Toaster::success('Subscription will be canceled at end of billing period.');

            $this->dispatch('subscription-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to cancel subscription: '.$e->getMessage());
        }
    }

    public function cancelSubscriptionImmediately(): void
    {
        try {
            $this->isProcessing = true;
            $this->setCashierModel();

            $subscription = $this->billable?->subscription('default');

            if (! $subscription) {
                throw new \Exception('No subscription found.');
            }

            $subscription->cancelNow();
            Flux::modal('cancel-subscription-modal')->close();
            Toaster::success('Subscription canceled immediately.');

            $this->dispatch('subscription-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to cancel subscription: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.cancel-subscription-modal');
    }
}
