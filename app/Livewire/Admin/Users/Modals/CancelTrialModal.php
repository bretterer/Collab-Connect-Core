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

class CancelTrialModal extends Component
{
    public ?int $userId = null;

    public string $cancelTrialAction = 'no_subscription';

    public bool $isProcessing = false;

    #[On('open-cancel-trial-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        $this->cancelTrialAction = 'no_subscription';
        Flux::modal('cancel-trial-modal')->show();
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

    public function cancelTrial(): void
    {
        try {
            $this->isProcessing = true;
            $subscription = $this->billable?->subscription('default');

            if (! $subscription) {
                throw new \Exception('No subscription found.');
            }

            if ($this->cancelTrialAction === 'convert_to_paid') {
                // End trial immediately in Stripe by setting trial_end to 'now'
                Cashier::stripe()->subscriptions->update($subscription->stripe_id, [
                    'trial_end' => 'now',
                ]);

                // Update local record
                $subscription->update(['trial_ends_at' => now()]);

                Toaster::success('Trial ended. User is now on paid subscription.');
            } else {
                $subscription->cancelNow();
                Toaster::success('Trial canceled and subscription ended.');
            }

            $this->cancelTrialAction = 'no_subscription';
            Flux::modal('cancel-trial-modal')->close();

            $this->dispatch('subscription-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to cancel trial: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.cancel-trial-modal');
    }
}
