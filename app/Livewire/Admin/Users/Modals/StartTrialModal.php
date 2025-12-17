<?php

namespace App\Livewire\Admin\Users\Modals;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripeProduct;
use App\Models\User;
use App\Settings\SubscriptionSettings;
use Flux\Flux;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class StartTrialModal extends Component
{
    public ?int $userId = null;

    public string $selectedPlan = '';

    public int $trialDays = 14;

    public bool $isProcessing = false;

    public function mount(): void
    {
        $this->trialDays = app(SubscriptionSettings::class)->trialPeriodDays ?? 14;
    }

    #[On('open-start-trial-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        $this->selectedPlan = '';
        $this->trialDays = app(SubscriptionSettings::class)->trialPeriodDays ?? 14;
        Flux::modal('start-trial-modal')->show();
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

    public function startTrial(): void
    {
        $this->validate([
            'selectedPlan' => 'required|string',
            'trialDays' => 'required|integer|min:1|max:90',
        ]);

        try {
            $this->isProcessing = true;
            $this->setCashierModel();

            $billable = $this->billable;
            $user = $this->user;

            if (! $billable) {
                throw new \Exception('No billable profile found.');
            }

            if (! $billable->hasStripeId()) {
                $billable->createAsStripeCustomer([
                    'name' => $billable instanceof Business ? ($billable->name ?? 'Business') : ($user?->name ?? 'Customer'),
                    'email' => $billable instanceof Business ? ($billable->email ?? $user?->email ?? '') : ($user?->email ?? ''),
                ]);
            }

            $billable->newSubscription('default', $this->selectedPlan)
                ->trialDays($this->trialDays)
                ->create();

            $this->selectedPlan = '';
            Flux::modal('start-trial-modal')->close();
            Toaster::success('Trial subscription started successfully.');

            $this->dispatch('subscription-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to start trial: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.start-trial-modal');
    }
}
