<?php

namespace App\Livewire\Profile;

use App\Models\StripeProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class BillingDetails extends Component
{
    public $billableModel;

    public bool $showCancelModal = false;

    public bool $showChangePlanModal = false;

    public ?int $selectedPriceId = null;

    public bool $isProcessing = false;

    public function mount()
    {
        // Determine the billable model (Business or Influencer)
        $user = Auth::user();

        if ($user->currentBusiness) {
            $this->billableModel = $user->currentBusiness;
        } elseif ($user->influencer) {
            $this->billableModel = $user->influencer;
        }

        if (! $this->billableModel) {
            abort(404, 'No billable account found');
        }

        $this->selectedPriceId = $this->getAvailablePlansProperty()->first()?->prices->first()?->id ?? null;
    }

    public function getAvailablePlansProperty()
    {
        $billableType = get_class($this->billableModel);

        return StripeProduct::query()
            ->where('active', true)
            ->where('billable_type', $billableType)
            ->with(['prices' => function ($query) {
                $query->where('active', true)->orderBy('unit_amount');
            }])
            ->get();
    }

    public function getCurrentSubscriptionProperty()
    {
        return $this->billableModel->subscription('default');
    }

    public function getInvoicesProperty()
    {
        if (! $this->billableModel->hasStripeId()) {
            return collect([]);
        }

        return $this->billableModel->invoices();
    }

    public function openChangePlanModal()
    {
        $this->showChangePlanModal = true;
        $this->selectedPriceId = null;
    }

    public function closeChangePlanModal()
    {
        $this->showChangePlanModal = false;
        $this->selectedPriceId = null;
    }

    public function selectPrice(int $priceId)
    {
        $this->selectedPriceId = $priceId;
    }

    public function changePlan()
    {
        $this->validate([
            'selectedPriceId' => 'required|exists:stripe_prices,id',
        ]);

        try {
            $this->isProcessing = true;

            $price = \App\Models\StripePrice::find($this->selectedPriceId);

            if (! $price) {
                throw new \Exception('Invalid plan selected.');
            }

            // If user has a subscription, swap the plan
            if ($this->currentSubscription) {
                $this->billableModel->subscription('default')->swap($price->stripe_id);

                session()->flash('success', 'Your plan has been changed successfully!');
            } else {
                // If no subscription exists, dispatch event to create payment method
                $this->dispatch('createStripePaymentMethod');

                return;
            }

            $this->closeChangePlanModal();
            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            session()->flash('error', 'Failed to change plan: '.$e->getMessage());
        }
    }

    #[On('stripePaymentMethodCreated')]
    public function handleStripePaymentMethod($paymentMethodId)
    {
        try {
            $this->billableModel->deletePaymentMethods();

            $price = \App\Models\StripePrice::find($this->selectedPriceId);

            if (! $price) {
                $this->dispatch('stripePaymentMethodError', message: 'Invalid subscription plan selected.');

                return;
            }

            // Create subscription
            $user = Auth::user();
            $this->billableModel->newSubscription('default', $price->stripe_id)
                ->trialUntil(Carbon::parse(config('collabconnect.stripe.subscriptions.start_date')))
                ->create($paymentMethodId, [
                    'email' => $user->email,
                    'name' => $user->name,
                ]);

            session()->flash('success', 'Your subscription has been created successfully!');

            $this->closeChangePlanModal();
            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->dispatch('stripePaymentMethodError', message: $e->getMessage());
            $this->isProcessing = false;
        }
    }

    #[On('stripePaymentMethodError')]
    public function handleStripeError($message)
    {
        $this->isProcessing = false;
        session()->flash('error', 'Payment error: '.$message);
    }

    public function openCancelModal()
    {
        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
    }

    public function cancelSubscription()
    {
        try {
            $this->billableModel->subscription('default')->cancel();

            session()->flash('success', 'Your subscription has been canceled. You will have access until the end of your current billing period.');

            $this->closeCancelModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel subscription: '.$e->getMessage());
        }
    }

    public function resumeSubscription()
    {
        try {
            $this->billableModel->subscription('default')->resume();

            session()->flash('success', 'Your subscription has been resumed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to resume subscription: '.$e->getMessage());
        }
    }

    public function downloadInvoice($invoiceId)
    {
        return response()->streamDownload(function () use ($invoiceId) {
            echo $this->billableModel->findInvoice($invoiceId)->pdf([
                'vendor' => config('app.name'),
                'product' => 'Subscription',
            ]);
        }, 'invoice-'.$invoiceId.'.pdf');
    }

    public function goToStripePortal()
    {
        return redirect($this->billableModel->billingPortalUrl(route('billing')));
    }

    public function render()
    {
        return view('livewire.profile.billing-details');
    }
}
