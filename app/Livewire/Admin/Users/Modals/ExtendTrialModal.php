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

class ExtendTrialModal extends Component
{
    public ?int $userId = null;

    public int $trialDays = 14;

    public bool $isProcessing = false;

    #[On('open-extend-trial-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        $this->trialDays = 14;
        Flux::modal('extend-trial-modal')->show();
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
    public function currentTrialEnd(): ?string
    {
        $subscription = $this->billable?->subscription('default');

        if (! $subscription) {
            return null;
        }

        try {
            $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id);

            if ($stripeSubscription->trial_end) {
                return \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)->format('F j, Y');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function extendTrial(): void
    {
        $this->validate([
            'trialDays' => 'required|integer|min:1|max:365',
        ]);

        try {
            $this->isProcessing = true;
            $subscription = $this->billable?->subscription('default');

            if (! $subscription) {
                throw new \Exception('No subscription found.');
            }

            // Calculate the new trial end date
            $newTrialEnd = now()->addDays($this->trialDays)->timestamp;

            // Update the subscription in Stripe
            Cashier::stripe()->subscriptions->update($subscription->stripe_id, [
                'trial_end' => $newTrialEnd,
            ]);

            // Update the local record
            $subscription->update([
                'trial_ends_at' => now()->addDays($this->trialDays),
            ]);

            Flux::modal('extend-trial-modal')->close();
            Toaster::success("Trial extended by {$this->trialDays} days.");

            $this->dispatch('subscription-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to extend trial: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.extend-trial-modal');
    }
}
