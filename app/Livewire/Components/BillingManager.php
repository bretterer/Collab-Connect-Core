<?php

namespace App\Livewire\Components;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use Flux;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class BillingManager extends Component
{
    public string $billableType;

    public int $billableId;

    public string $activeSection = 'overview';

    public bool $showCancelModal = false;

    public bool $showAddPaymentMethodModal = false;

    public bool $showDeletePaymentMethodModal = false;

    public ?string $paymentMethodToDelete = null;

    public bool $isProcessing = false;

    public bool $stripeCustomerInvalid = false;

    public bool $stripeSubscriptionInvalid = false;

    public ?string $stripeError = null;

    public function mount(Model $billable, ?string $initialSection = null): void
    {
        $this->billableType = get_class($billable);
        $this->billableId = $billable->id;

        // Set initial section if provided
        if ($initialSection && in_array($initialSection, ['overview', 'plans', 'payment-methods', 'invoices'])) {
            $this->activeSection = $initialSection;
        }

        // Check if the Stripe customer and subscription are valid
        $this->validateStripeCustomer();
        $this->validateStripeSubscription();
    }

    protected function validateStripeCustomer(): void
    {
        $billable = $this->billable;

        if (! $billable->hasStripeId()) {
            return;
        }

        try {
            // Try to fetch the customer from Stripe
            $billable->asStripeCustomer();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Customer doesn't exist in Stripe - mark as invalid
            $this->stripeCustomerInvalid = true;
            $this->stripeError = 'Your billing account needs to be resynced. Please add a new payment method to continue.';

            // Clear the invalid Stripe ID from the database
            $billable->update(['stripe_id' => null]);
        } catch (\Exception $e) {
            // Other Stripe errors
            $this->stripeError = 'Unable to connect to billing service. Please try again later.';
        }
    }

    protected function validateStripeSubscription(): void
    {
        // Skip if customer is already invalid
        if ($this->stripeCustomerInvalid) {
            return;
        }

        $billable = $this->billable;
        $subscription = $billable->subscription('default');

        if (! $subscription) {
            return;
        }

        // Check if this looks like a fake/seeded subscription
        $stripeId = $subscription->stripe_id;
        if (str_starts_with($stripeId, 'sub_test_') || str_starts_with($stripeId, 'sub_seeder_')) {
            $this->stripeSubscriptionInvalid = true;
            $this->stripeError = 'Your subscription data is outdated. Please subscribe to a plan to continue or contact support.';

            // Delete the invalid subscription record
            $subscription->delete();

            return;
        }

        // Check if local subscription is already marked as canceled (via webhook)
        if ($subscription->stripe_status === 'canceled' || ($subscription->ends_at && $subscription->ends_at->isPast())) {
            $this->stripeSubscriptionInvalid = true;

            return;
        }

        // Verify the subscription exists and is active in Stripe
        try {
            // Use Cashier's stripe client which has the API key configured
            $stripeSubscription = \Laravel\Cashier\Cashier::stripe()->subscriptions->retrieve($stripeId);

            // Check if subscription is canceled or deleted in Stripe
            if (in_array($stripeSubscription->status, ['canceled', 'incomplete_expired'])) {
                $this->stripeSubscriptionInvalid = true;

                // Sync the local subscription state
                $subscription->update([
                    'stripe_status' => $stripeSubscription->status,
                    'ends_at' => $subscription->ends_at ?? now(),
                ]);

                // Don't show error for canceled subscriptions - just show as not subscribed
                $this->stripeError = null;
            }
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Subscription doesn't exist in Stripe
            $this->stripeSubscriptionInvalid = true;
            $this->stripeError = 'Your subscription could not be found. Please subscribe to a plan to continue.';

            // Delete the invalid subscription record
            $subscription->delete();
        } catch (\Exception $e) {
            // Other Stripe errors - don't delete, just warn
            if (! $this->stripeError) {
                $this->stripeError = 'Unable to verify subscription status. Please try again later.';
            }
        }
    }

    #[Computed]
    public function billable(): Business|Influencer
    {
        return $this->billableType::find($this->billableId);
    }

    #[Computed]
    public function subscription(): ?Subscription
    {
        return $this->billable->subscription('default');
    }

    #[Computed]
    public function isSubscribed(): bool
    {
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return false;
        }

        try {
            return $this->billable->subscribed('default');
        } catch (\Exception $e) {
            return false;
        }
    }

    #[Computed]
    public function onTrial(): bool
    {
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return false;
        }

        try {
            return $this->billable->onTrial('default');
        } catch (\Exception $e) {
            return false;
        }
    }

    #[Computed]
    public function onGracePeriod(): bool
    {
        $subscription = $this->subscription;

        return $subscription && $subscription->onGracePeriod();
    }

    #[Computed]
    public function trialEndsAt(): ?string
    {
        $subscription = $this->subscription;

        return $subscription?->trial_ends_at?->format('F j, Y');
    }

    #[Computed]
    public function currentPlan(): ?StripePrice
    {
        // Don't return a current plan if subscription is invalid
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return null;
        }

        $subscription = $this->subscription;

        if (! $subscription) {
            return null;
        }

        // Include inactive prices so we can show legacy plans
        return StripePrice::withTrashed()
            ->where('stripe_id', $subscription->stripe_price)
            ->first();
    }

    #[Computed]
    public function currentPlanIsInactive(): bool
    {
        $currentPlan = $this->currentPlan;

        if (! $currentPlan) {
            return false;
        }

        return ! $currentPlan->active || $currentPlan->deleted_at !== null;
    }

    #[Computed]
    public function availablePlans(): \Illuminate\Database\Eloquent\Collection
    {
        return StripeProduct::query()
            ->where('billable_type', $this->billableType)
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
    public function paymentMethods(): array
    {
        if (! $this->billable->hasStripeId() || $this->stripeCustomerInvalid) {
            return [];
        }

        try {
            return $this->billable->paymentMethods()->map(function ($method) {
                return [
                    'id' => $method->id,
                    'brand' => ucfirst($method->card->brand),
                    'last4' => $method->card->last4,
                    'exp_month' => str_pad($method->card->exp_month, 2, '0', STR_PAD_LEFT),
                    'exp_year' => $method->card->exp_year,
                    'is_default' => $method->id === $this->billable->defaultPaymentMethod()?->id,
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    #[Computed]
    public function invoices(): array
    {
        if (! $this->billable->hasStripeId() || $this->stripeCustomerInvalid) {
            return [];
        }

        try {
            return $this->billable->invoices()->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->format('F j, Y'),
                    'total' => $invoice->total(),
                    'status' => $invoice->status,
                    'pdf_url' => $invoice->asStripeInvoice()->invoice_pdf,
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    #[Computed]
    public function upcomingInvoice(): ?array
    {
        if (! $this->isSubscribed || ! $this->billable->hasStripeId() || $this->stripeCustomerInvalid) {
            return null;
        }

        try {
            $upcoming = $this->billable->upcomingInvoice();

            if (! $upcoming) {
                return null;
            }

            return [
                'date' => $upcoming->date()->format('F j, Y'),
                'total' => $upcoming->total(),
                'lines' => collect($upcoming->invoiceLineItems())->map(function ($line) {
                    return [
                        'description' => $line->description,
                        'amount' => '$'.number_format($line->amount / 100, 2),
                    ];
                })->toArray(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function customerSince(): string
    {
        if (! $this->billable->hasStripeId() || $this->stripeCustomerInvalid) {
            return 'N/A';
        }

        try {
            $customer = $this->billable->asStripeCustomer();

            return $customer?->created
                ? \Carbon\Carbon::createFromTimestamp($customer->created)->format('M Y')
                : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    public function setActiveSection(string $section): void
    {
        $this->activeSection = $section;
        $this->dispatch('section-changed', section: $section);
    }

    public function subscribe(string $priceId): void
    {
        $this->dispatch(
            'openCheckout',
            billableType: $this->billableType,
            billableId: $this->billableId,
            priceId: $priceId,
            type: 'subscription'
        )->to(CheckoutModal::class);
    }

    public function changePlan(string $priceId): void
    {
        $this->dispatch(
            'openCheckout',
            billableType: $this->billableType,
            billableId: $this->billableId,
            priceId: $priceId,
            type: 'plan_change'
        )->to(CheckoutModal::class);
    }

    #[On('checkoutCompleted')]
    public function handleCheckoutCompleted(): void
    {
        // Refresh computed properties by clearing cache
        unset($this->subscription, $this->isSubscribed, $this->currentPlan, $this->paymentMethods);
    }

    #[On('stripePaymentMethodCreated')]
    public function handlePaymentMethodCreated(string $paymentMethodId): void
    {
        try {
            $this->isProcessing = true;

            // Create Stripe customer if needed
            if (! $this->billable->hasStripeId()) {
                $this->billable->createAsStripeCustomer([
                    'name' => $this->getCustomerName(),
                    'email' => $this->getCustomerEmail(),
                ]);
            }

            // Add and set as default payment method
            $this->billable->addPaymentMethod($paymentMethodId);
            $this->billable->updateDefaultPaymentMethod($paymentMethodId);

            $this->showAddPaymentMethodModal = false;

            Flux::toast('Payment method added successfully!', variant: 'success', position: 'bottom right');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Flux::toast('Failed to add payment method: '.$e->getMessage(), variant: 'danger', position: 'bottom right');
        }
    }

    #[On('stripePaymentMethodError')]
    public function handlePaymentMethodError(string $message): void
    {
        $this->isProcessing = false;
        Flux::toast('Payment error: '.$message, variant: 'danger', position: 'bottom right');
    }

    public function cancelSubscription(): void
    {
        try {
            $this->isProcessing = true;

            $subscription = $this->subscription;

            if (! $subscription) {
                Flux::toast('No active subscription to cancel.', variant: 'danger', position: 'bottom right');
                $this->isProcessing = false;

                return;
            }

            $subscription->cancel();

            $this->showCancelModal = false;
            Flux::toast('Subscription canceled. You will have access until the end of your billing period.', variant: 'success', position: 'bottom right');
            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Flux::toast('Failed to cancel subscription: '.$e->getMessage(), variant: 'danger', position: 'bottom right');
        }
    }

    public function cancelImmediately(): void
    {
        try {
            $this->isProcessing = true;

            $subscription = $this->subscription;

            if (! $subscription) {
                Flux::toast('No active subscription to cancel.', variant: 'danger', position: 'bottom right');
                $this->isProcessing = false;

                return;
            }

            $subscription->cancelNow();

            $this->showCancelModal = false;
            Flux::toast('Subscription canceled immediately.', variant: 'success', position: 'bottom right');
            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Flux::toast('Failed to cancel subscription: '.$e->getMessage(), variant: 'danger', position: 'bottom right');
        }
    }

    public function resumeSubscription(): void
    {
        try {
            $this->isProcessing = true;

            $subscription = $this->subscription;

            if (! $subscription || ! $subscription->onGracePeriod()) {
                Flux::toast('Cannot resume this subscription.', variant: 'danger', position: 'bottom right');
                $this->isProcessing = false;

                return;
            }

            $subscription->resume();

            Flux::toast('Subscription resumed successfully!', variant: 'success', position: 'bottom right');
            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Flux::toast('Failed to resume subscription: '.$e->getMessage(), variant: 'danger', position: 'bottom right');
        }
    }

    public function setDefaultPaymentMethod(string $paymentMethodId): void
    {
        try {
            $this->isProcessing = true;

            $this->billable->updateDefaultPaymentMethod($paymentMethodId);

            Flux::toast('Default payment method updated.', variant: 'success', position: 'bottom right');
            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Flux::toast('Failed to update payment method: '.$e->getMessage(), variant: 'danger', position: 'bottom right');
        }
    }

    public function confirmDeletePaymentMethod(string $paymentMethodId): void
    {
        $this->paymentMethodToDelete = $paymentMethodId;
        $this->showDeletePaymentMethodModal = true;
    }

    public function deletePaymentMethod(): void
    {
        if (! $this->paymentMethodToDelete) {
            return;
        }

        try {
            $this->isProcessing = true;

            $paymentMethod = $this->billable->findPaymentMethod($this->paymentMethodToDelete);

            if ($paymentMethod) {
                $paymentMethod->delete();
            }

            $this->showDeletePaymentMethodModal = false;
            $this->paymentMethodToDelete = null;

            Flux::toast('Payment method removed.', variant: 'success', position: 'bottom right');
            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Flux::toast('Failed to remove payment method: '.$e->getMessage(), variant: 'danger', position: 'bottom right');
        }
    }

    public function openAddPaymentMethodModal(): void
    {
        $this->showAddPaymentMethodModal = true;
        $this->dispatch('reloadStripeFromLivewire');
    }

    public function downloadInvoice(string $invoiceId): mixed
    {
        return $this->billable->downloadInvoice($invoiceId, [
            'vendor' => config('app.name'),
        ]);
    }

    protected function getCustomerName(): string
    {
        if ($this->billable instanceof Business) {
            return $this->billable->name ?? 'Business';
        }

        return $this->billable->user?->name ?? 'Customer';
    }

    protected function getCustomerEmail(): string
    {
        if ($this->billable instanceof Business) {
            return $this->billable->email ?? $this->billable->users()->first()?->email ?? '';
        }

        return $this->billable->user?->email ?? '';
    }

    public function render()
    {
        return view('livewire.components.billing-manager');
    }
}
