<?php

namespace App\Livewire\Admin\Users\Modals;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripeProduct;
use App\Models\User;
use Flux\Flux;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class SwapPlanModal extends Component
{
    public ?int $userId = null;

    public string $selectedPlan = '';

    public string $swapTiming = 'immediately';

    public bool $isProcessing = false;

    #[On('open-swap-plan-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        $this->selectedPlan = '';
        $this->swapTiming = 'immediately';
        Flux::modal('swap-plan-modal')->show();
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
    public function currentPriceId(): ?string
    {
        return $this->billable?->subscription('default')?->stripe_price;
    }

    #[Computed]
    public function availablePlans(): \Illuminate\Support\Collection
    {
        $billable = $this->billable;
        $billableType = $billable ? get_class($billable) : null;

        if (! $billableType) {
            return collect();
        }

        return StripeProduct::query()
            ->where('billable_type', $billableType)
            ->where('active', true)
            ->whereNull('deleted_at')
            ->with(['prices' => function ($query) {
                $query->where('active', true)
                    ->whereNull('deleted_at')
                    ->whereNotNull('recurring')
                    ->orderBy('unit_amount');
            }])
            ->get();
    }

    #[Computed]
    public function pendingSchedule(): ?array
    {
        $subscription = $this->billable?->subscription('default');

        if (! $subscription) {
            return null;
        }

        try {
            $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id);

            if (! $stripeSubscription->schedule) {
                return null;
            }

            $schedule = Cashier::stripe()->subscriptionSchedules->retrieve($stripeSubscription->schedule);

            if ($schedule->status !== 'active' || count($schedule->phases) < 2) {
                return null;
            }

            // Get the upcoming phase
            $upcomingPhase = $schedule->phases[1] ?? null;
            if (! $upcomingPhase) {
                return null;
            }

            $upcomingPriceId = $upcomingPhase->items[0]->price ?? null;

            return [
                'schedule_id' => $schedule->id,
                'upcoming_price_id' => $upcomingPriceId,
                'starts_at' => \Carbon\Carbon::createFromTimestamp($upcomingPhase->start_date)->format('F j, Y'),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    public function swapPlan(): void
    {
        $this->validate([
            'selectedPlan' => 'required|string',
            'swapTiming' => 'required|in:immediately,end_of_period',
        ]);

        try {
            $this->isProcessing = true;
            $subscription = $this->billable?->subscription('default');

            if (! $subscription) {
                throw new \Exception('No subscription found.');
            }

            if ($this->selectedPlan === $this->currentPriceId) {
                throw new \Exception('Selected plan is the same as the current plan.');
            }

            // Cancel any existing schedule first
            $this->cancelPendingSchedule();

            if ($this->swapTiming === 'immediately') {
                // Immediate swap with proration
                $subscription->swap($this->selectedPlan);
                Toaster::success('Plan changed immediately with proration.');
            } else {
                // Schedule the swap for end of billing period
                $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id);

                // Create a subscription schedule
                $schedule = Cashier::stripe()->subscriptionSchedules->create([
                    'from_subscription' => $subscription->stripe_id,
                ]);

                // Update the schedule with two phases
                Cashier::stripe()->subscriptionSchedules->update($schedule->id, [
                    'phases' => [
                        [
                            'items' => [['price' => $stripeSubscription->items->data[0]->price->id]],
                            'start_date' => $stripeSubscription->current_period_start,
                            'end_date' => $stripeSubscription->current_period_end,
                        ],
                        [
                            'items' => [['price' => $this->selectedPlan]],
                            'start_date' => $stripeSubscription->current_period_end,
                        ],
                    ],
                ]);

                Toaster::success('Plan change scheduled for end of billing period.');
            }

            $this->selectedPlan = '';
            Flux::modal('swap-plan-modal')->close();

            $this->dispatch('subscription-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to swap plan: '.$e->getMessage());
        }
    }

    public function cancelPendingSchedule(): void
    {
        $schedule = $this->pendingSchedule;

        if (! $schedule) {
            return;
        }

        try {
            Cashier::stripe()->subscriptionSchedules->release($schedule['schedule_id']);
        } catch (\Exception $e) {
            // Ignore errors when canceling schedule
        }
    }

    public function cancelScheduledChange(): void
    {
        try {
            $this->isProcessing = true;

            $this->cancelPendingSchedule();

            Toaster::success('Scheduled plan change canceled.');

            unset($this->pendingSchedule);

            $this->dispatch('subscription-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to cancel scheduled change: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.swap-plan-modal');
    }
}
