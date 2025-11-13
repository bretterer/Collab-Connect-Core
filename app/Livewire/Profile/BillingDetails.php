<?php

namespace App\Livewire\Profile;

use App\Models\StripeProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

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
        $subscription = $this->billableModel->subscription('default');

        // Don't return subscriptions that have ended (canceled and past ends_at date)
        if ($subscription && $subscription->ended()) {
            return null;
        }

        return $subscription;
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

            $customerInfo = [];

            match (get_class($this->billableModel)) {
                \App\Models\Business::class => $customerInfo = [
                    'email' => $this->billableModel->email,
                    'name' => $this->billableModel->name,
                    'business_name' => $this->billableModel->name,
                    'individual_name' => $user->name,
                ],
                \App\Models\Influencer::class => $customerInfo = [
                    'email' => $user->email,
                    'name' => $user->name,
                ],
                default => [],
            };

            // Capture Datafast cookies for attribution
            $datafastVisitorId = request()->cookie('datafast_visitor_id');
            $datafastSessionId = request()->cookie('datafast_session_id');

            $subscription = $this->billableModel->newSubscription('default', $price->stripe_id)
                ->trialUntil(Carbon::parse(config('collabconnect.stripe.subscriptions.start_date')));

            // Add Datafast IDs to subscription metadata if available
            if ($datafastVisitorId || $datafastSessionId) {
                $metadata = [];
                if ($datafastVisitorId) {
                    $metadata['datafast_visitor_id'] = $datafastVisitorId;
                }
                if ($datafastSessionId) {
                    $metadata['datafast_session_id'] = $datafastSessionId;
                }
                $subscription->withMetadata($metadata);
            }

            $subscription->create($paymentMethodId, $customerInfo);

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
            $subscription = $this->billableModel->subscription('default');

            // If subscription has ended, we can't resume - user needs to create a new subscription
            if (! $subscription || $subscription->ended()) {
                session()->flash('error', 'This subscription has ended. Please create a new subscription instead.');

                return;
            }

            $subscription->resume();

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
        try {
            if (! $this->billableModel->hasStripeId()) {
                Toaster::error('No billing account found.');

                return;
            }
        } catch (\Exception $e) {
            Toaster::error('Failed to access billing portal: '.$e->getMessage());

            return;
        }

        return redirect($this->billableModel->billingPortalUrl(route('billing')));
    }

    public function render()
    {
        return view('livewire.profile.billing-details');
    }
}
