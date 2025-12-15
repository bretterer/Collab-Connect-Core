<?php

namespace App\Livewire\Components;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripePrice;
use Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckoutModal extends Component
{
    public bool $show = false;

    public string $billableType = '';

    public int $billableId = 0;

    public ?string $priceId = null;

    public string $checkoutType = 'subscription'; // subscription, addon, plan_change

    public ?string $selectedPaymentMethodId = null;

    public bool $showNewPaymentForm = false;

    public bool $isProcessing = false;

    public ?string $errorMessage = null;

    #[Computed]
    public function billable(): Business|Influencer|null
    {
        if (! $this->billableType || ! $this->billableId) {
            return null;
        }

        return $this->billableType::find($this->billableId);
    }

    #[Computed]
    public function price(): ?StripePrice
    {
        if (! $this->priceId) {
            return null;
        }

        return StripePrice::where('stripe_id', $this->priceId)
            ->with('product')
            ->first();
    }

    #[Computed]
    public function paymentMethods(): array
    {
        $billable = $this->billable;

        if (! $billable || ! $billable->hasStripeId()) {
            return [];
        }

        try {
            return $billable->paymentMethods()->map(function ($method) use ($billable) {
                return [
                    'id' => $method->id,
                    'brand' => ucfirst($method->card->brand),
                    'last4' => $method->card->last4,
                    'exp_month' => str_pad($method->card->exp_month, 2, '0', STR_PAD_LEFT),
                    'exp_year' => $method->card->exp_year,
                    'is_default' => $method->id === $billable->defaultPaymentMethod()?->id,
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    #[Computed]
    public function formattedPrice(): string
    {
        $price = $this->price;

        if (! $price) {
            return '$0.00';
        }

        return '$'.number_format($price->unit_amount / 100, 2);
    }

    #[Computed]
    public function intervalLabel(): string
    {
        $price = $this->price;

        if (! $price || ! $price->recurring) {
            return '';
        }

        $interval = $price->recurring['interval'] ?? 'month';
        $count = $price->recurring['interval_count'] ?? 1;

        if ($count > 1) {
            return "every {$count} {$interval}s";
        }

        return "/{$interval}";
    }

    #[Computed]
    public function checkoutTypeLabel(): string
    {
        return match ($this->checkoutType) {
            'subscription' => 'Start Subscription',
            'addon' => 'Add to Subscription',
            'plan_change' => 'Change Plan',
            default => 'Continue',
        };
    }

    #[Computed]
    public function summaryTitle(): string
    {
        return match ($this->checkoutType) {
            'subscription' => 'Order summary',
            'addon' => 'Add-on summary',
            'plan_change' => 'Plan change summary',
            default => 'Summary',
        };
    }

    #[Computed]
    public function taxCalculation(): ?array
    {
        // Tax calculations disabled for now
        return null;
    }

    #[Computed]
    public function hasTax(): bool
    {
        $tax = $this->taxCalculation;

        return $tax !== null && $tax['tax_amount'] > 0;
    }

    #[Computed]
    public function formattedTax(): string
    {
        $tax = $this->taxCalculation;

        if (! $tax || $tax['tax_amount'] === 0) {
            return '$0.00';
        }

        return '$'.number_format($tax['tax_amount'] / 100, 2);
    }

    #[Computed]
    public function formattedTotal(): string
    {
        $price = $this->price;
        $tax = $this->taxCalculation;

        if (! $price) {
            return '$0.00';
        }

        $total = $price->unit_amount;

        if ($tax) {
            $total = $tax['total_amount'];
        }

        return '$'.number_format($total / 100, 2);
    }

    #[Computed]
    public function canCalculateTax(): bool
    {
        $billable = $this->billable;

        if (! $billable) {
            return false;
        }

        return ! empty($billable->postal_code) && ! empty($billable->state);
    }

    #[On('openCheckout')]
    public function openCheckout(string $billableType, int $billableId, string $priceId, string $type = 'subscription'): void
    {
        $this->reset(['errorMessage', 'isProcessing', 'showNewPaymentForm', 'selectedPaymentMethodId']);

        $this->billableType = $billableType;
        $this->billableId = $billableId;
        $this->priceId = $priceId;
        $this->checkoutType = $type;

        // Pre-select default payment method if available
        $paymentMethods = $this->paymentMethods;
        if (count($paymentMethods) > 0) {
            $default = collect($paymentMethods)->firstWhere('is_default', true);
            $this->selectedPaymentMethodId = $default ? $default['id'] : $paymentMethods[0]['id'];
            $this->showNewPaymentForm = false;
        } else {
            $this->showNewPaymentForm = true;
        }

        $this->show = true;

        // Trigger Stripe form reload if showing new payment form
        if ($this->showNewPaymentForm) {
            $this->dispatch('reloadStripeFromLivewire');
        }
    }

    public function selectPaymentMethod(string $paymentMethodId): void
    {
        $this->selectedPaymentMethodId = $paymentMethodId;
        $this->showNewPaymentForm = false;
    }

    public function showAddNewCard(): void
    {
        $this->selectedPaymentMethodId = null;
        $this->showNewPaymentForm = true;
        $this->dispatch('reloadStripeFromLivewire');
    }

    public function processCheckout(): void
    {
        $this->errorMessage = null;

        if ($this->showNewPaymentForm) {
            // Set processing state before dispatching to show overlay during async Stripe operation
            $this->isProcessing = true;

            // Trigger Stripe to create payment method first (uses unique event to avoid conflict with billing-manager's form)
            $this->dispatch('createCheckoutPaymentMethod');

            return;
        }

        if (! $this->selectedPaymentMethodId) {
            $this->errorMessage = 'Please select a payment method.';

            return;
        }

        $this->completeCheckout($this->selectedPaymentMethodId);
    }

    #[On('stripePaymentMethodCreated')]
    public function handlePaymentMethodCreated(string $paymentMethodId): void
    {
        try {
            $this->isProcessing = true;
            $billable = $this->billable;

            // Create Stripe customer if needed
            if (! $billable->hasStripeId()) {
                $billable->createAsStripeCustomer([
                    'name' => $this->getCustomerName(),
                    'email' => $this->getCustomerEmail(),
                ]);
            }

            // Add and set as default payment method
            $billable->addPaymentMethod($paymentMethodId);
            $billable->updateDefaultPaymentMethod($paymentMethodId);

            // Now complete the checkout
            $this->completeCheckout($paymentMethodId);
        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->errorMessage = 'Failed to add payment method: '.$e->getMessage();
        }
    }

    #[On('stripePaymentMethodError')]
    public function handlePaymentMethodError(string $message): void
    {
        $this->isProcessing = false;
        $this->errorMessage = $message;
    }

    protected function completeCheckout(string $paymentMethodId): void
    {
        try {
            $this->isProcessing = true;
            $billable = $this->billable;

            if ($this->checkoutType === 'subscription') {
                $billable->newSubscription('default', $this->priceId)
                    ->create($paymentMethodId);

                Flux::toast('Subscription created successfully!', variant: 'success', position: 'bottom right');
            } elseif ($this->checkoutType === 'plan_change') {
                $subscription = $billable->subscription('default');

                if ($subscription) {
                    // Check if subscription has an attached schedule and release it first
                    $stripeSubscription = $subscription->asStripeSubscription();
                    if ($stripeSubscription->schedule) {
                        // Fetch the full schedule details before releasing
                        $schedule = \Laravel\Cashier\Cashier::stripe()->subscriptionSchedules->retrieve(
                            $stripeSubscription->schedule
                        );

                        // Release the schedule
                        \Laravel\Cashier\Cashier::stripe()->subscriptionSchedules->release(
                            $stripeSubscription->schedule
                        );

                        // Notify admins about the released schedule
                        $this->notifyAdminOfReleasedSchedule($schedule, 'change their plan');
                    }

                    $subscription->swap($this->priceId);
                    Flux::toast('Plan changed successfully!', variant: 'success', position: 'bottom right');
                } else {
                    throw new \Exception('No active subscription found.');
                }
            } elseif ($this->checkoutType === 'addon') {
                // Handle addon logic here when needed
                Flux::toast('Add-on added successfully!', variant: 'success', position: 'bottom right');
            }

            $this->isProcessing = false;
            $this->show = false;

            // Dispatch event so parent component can refresh
            $this->dispatch('checkoutCompleted', type: $this->checkoutType, priceId: $this->priceId);

            // Reload the page to reflect the new subscription status
            $this->js('window.location.reload()');
        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->errorMessage = 'Checkout failed: '.$e->getMessage();
        }
    }

    public function closeModal(): void
    {
        $this->show = false;
        $this->reset(['priceId', 'errorMessage', 'isProcessing']);
    }

    protected function getCustomerName(): string
    {
        $billable = $this->billable;

        if ($billable instanceof Business) {
            return $billable->name ?? 'Business';
        }

        return $billable->user?->name ?? 'Customer';
    }

    protected function getCustomerEmail(): string
    {
        $billable = $this->billable;

        if ($billable instanceof Business) {
            return $billable->email ?? $billable->users()->first()?->email ?? '';
        }

        return $billable->user?->email ?? '';
    }

    protected function notifyAdminOfReleasedSchedule(\Stripe\SubscriptionSchedule $schedule, string $action): void
    {
        $adminEmails = config('collabconnect.admin_emails', []);

        if (empty($adminEmails)) {
            return;
        }

        \Illuminate\Support\Facades\Notification::route('mail', $adminEmails)
            ->notify(new \App\Notifications\SubscriptionScheduleReleasedNotification(
                schedule: $schedule,
                billableType: $this->billableType,
                billableId: $this->billableId,
                customerName: $this->getCustomerName(),
                customerEmail: $this->getCustomerEmail(),
                action: $action,
            ));
    }

    public function render()
    {
        return view('livewire.components.checkout-modal');
    }
}
