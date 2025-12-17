<?php

namespace App\Livewire\Admin\Businesses\Tabs;

use App\Models\Business;
use App\Models\StripePrice;
use App\Models\User;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class BillingTab extends Component
{
    public Business $business;

    // Processing state
    public bool $isProcessing = false;

    // Error tracking
    public bool $stripeCustomerInvalid = false;

    public bool $stripeSubscriptionInvalid = false;

    public ?string $stripeError = null;

    public function mount(Business $business): void
    {
        $this->business = $business->load(['owner', 'subscriptions']);

        // Set the correct Cashier model
        Cashier::useCustomerModel(Business::class);

        // Validate Stripe data
        $this->validateStripeCustomer();
        $this->validateStripeSubscription();
    }

    /**
     * Get the owner user for modal dispatches.
     */
    #[Computed]
    public function ownerUser(): ?User
    {
        return $this->business->owner()->first();
    }

    #[On('subscription-updated')]
    #[On('coupon-applied')]
    #[On('credits-updated')]
    public function refreshData(): void
    {
        // Refresh the business from the database
        $this->business->refresh();
        $this->business->load(['owner', 'subscriptions']);

        // Reset error states
        $this->stripeCustomerInvalid = false;
        $this->stripeSubscriptionInvalid = false;
        $this->stripeError = null;

        // Re-validate Stripe data
        Cashier::useCustomerModel(Business::class);
        $this->validateStripeCustomer();
        $this->validateStripeSubscription();

        // Clear all computed property caches
        unset(
            $this->subscription,
            $this->isSubscribed,
            $this->onTrial,
            $this->onGracePeriod,
            $this->trialEndsAt,
            $this->trialDaysRemaining,
            $this->nextBillingDate,
            $this->pendingSchedule,
            $this->activeDiscounts,
            $this->currentPlan,
            $this->subscriptionStatus,
            $this->subscriptionStatusColor,
            $this->stripeCustomer,
            $this->paymentMethods,
            $this->invoices,
            $this->customerSince,
            $this->promotionCredits,
            $this->isPromoted,
            $this->promotedUntil,
            $this->ownerUser
        );
    }

    #[Computed]
    public function subscription(): ?Subscription
    {
        return $this->business->subscription('default');
    }

    #[Computed]
    public function isSubscribed(): bool
    {
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return false;
        }

        try {
            return $this->business->subscribed('default');
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
            return $this->business->onTrial('default');
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
    public function trialDaysRemaining(): ?int
    {
        $subscription = $this->subscription;

        if (! $subscription?->trial_ends_at) {
            return null;
        }

        return (int) now()->diffInDays($subscription->trial_ends_at, false);
    }

    #[Computed]
    public function nextBillingDate(): ?string
    {
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return null;
        }

        $subscription = $this->subscription;

        if (! $subscription || $this->onTrial || $this->onGracePeriod) {
            return null;
        }

        try {
            $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id);

            if ($stripeSubscription->current_period_end) {
                return \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)->format('F j, Y');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function pendingSchedule(): ?array
    {
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return null;
        }

        $subscription = $this->subscription;

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

            $upcomingPhase = $schedule->phases[1] ?? null;
            if (! $upcomingPhase) {
                return null;
            }

            $upcomingPriceId = $upcomingPhase->items[0]->price ?? null;
            $upcomingPrice = $upcomingPriceId ? StripePrice::where('stripe_id', $upcomingPriceId)->first() : null;
            $planName = $upcomingPrice?->lookup_key
                ? \Illuminate\Support\Str::headline($upcomingPrice->lookup_key)
                : ($upcomingPrice?->product_name ?? 'Unknown Plan');

            return [
                'schedule_id' => $schedule->id,
                'upcoming_price_id' => $upcomingPriceId,
                'plan_name' => $planName,
                'starts_at' => \Carbon\Carbon::createFromTimestamp($upcomingPhase->start_date)->format('F j, Y'),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function activeDiscounts(): array
    {
        if ($this->stripeCustomerInvalid) {
            return [];
        }

        $discounts = [];

        try {
            $customer = $this->stripeCustomer;
            if ($customer?->discount) {
                $discounts[] = $this->formatDiscount($customer->discount, 'Customer');
            }

            $subscription = $this->subscription;
            if ($subscription) {
                $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($subscription->stripe_id);
                if ($stripeSubscription->discount && $stripeSubscription->discount->coupon->id !== ($customer?->discount?->coupon?->id ?? null)) {
                    $discounts[] = $this->formatDiscount($stripeSubscription->discount, 'Subscription');
                }
            }

            return $discounts;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function formatDiscount(object $discount, string $level): array
    {
        $coupon = $discount->coupon;

        $discountAmount = $coupon->percent_off
            ? "{$coupon->percent_off}% off"
            : '$'.number_format($coupon->amount_off / 100, 2).' off';

        $duration = match ($coupon->duration) {
            'forever' => 'Forever',
            'once' => 'One-time',
            'repeating' => $coupon->duration_in_months.' months',
            default => $coupon->duration,
        };

        $endsAt = null;
        if ($discount->end) {
            $endsAt = \Carbon\Carbon::createFromTimestamp($discount->end)->format('F j, Y');
        }

        return [
            'id' => $coupon->id,
            'name' => $coupon->name ?? $coupon->id,
            'discount' => $discountAmount,
            'duration' => $duration,
            'level' => $level,
            'ends_at' => $endsAt,
        ];
    }

    #[Computed]
    public function currentPlan(): ?StripePrice
    {
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return null;
        }

        $subscription = $this->subscription;

        if (! $subscription) {
            return null;
        }

        return StripePrice::withTrashed()
            ->where('stripe_id', $subscription->stripe_price)
            ->first();
    }

    #[Computed]
    public function subscriptionStatus(): string
    {
        $subscription = $this->subscription;

        if (! $subscription) {
            return 'No Subscription';
        }

        if ($this->stripeSubscriptionInvalid) {
            return 'Invalid';
        }

        if ($this->onGracePeriod) {
            return 'Canceled (Grace Period)';
        }

        if ($this->onTrial) {
            return 'Trialing';
        }

        return match ($subscription->stripe_status) {
            'active' => 'Active',
            'past_due' => 'Past Due',
            'unpaid' => 'Unpaid',
            'canceled' => 'Canceled',
            'incomplete' => 'Incomplete',
            'incomplete_expired' => 'Expired',
            'trialing' => 'Trialing',
            'paused' => 'Paused',
            default => ucfirst($subscription->stripe_status ?? 'Unknown'),
        };
    }

    #[Computed]
    public function subscriptionStatusColor(): string
    {
        $subscription = $this->subscription;

        if (! $subscription || $this->stripeSubscriptionInvalid) {
            return 'zinc';
        }

        if ($this->onGracePeriod) {
            return 'yellow';
        }

        if ($this->onTrial) {
            return 'blue';
        }

        return match ($subscription->stripe_status) {
            'active' => 'green',
            'past_due', 'unpaid' => 'red',
            'canceled', 'incomplete_expired' => 'zinc',
            'incomplete' => 'yellow',
            'trialing' => 'blue',
            'paused' => 'yellow',
            default => 'zinc',
        };
    }

    #[Computed]
    public function stripeCustomer(): ?\Stripe\Customer
    {
        if (! $this->business->hasStripeId() || $this->stripeCustomerInvalid) {
            return null;
        }

        try {
            return $this->business->asStripeCustomer();
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function paymentMethods(): array
    {
        if (! $this->business->hasStripeId() || $this->stripeCustomerInvalid) {
            return [];
        }

        try {
            return $this->business->paymentMethods()->map(function ($method) {
                return [
                    'id' => $method->id,
                    'brand' => ucfirst($method->card->brand),
                    'last4' => $method->card->last4,
                    'exp_month' => str_pad($method->card->exp_month, 2, '0', STR_PAD_LEFT),
                    'exp_year' => $method->card->exp_year,
                    'is_default' => $method->id === $this->business->defaultPaymentMethod()?->id,
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    #[Computed]
    public function billingHistory(): array
    {
        if (! $this->business->hasStripeId() || $this->stripeCustomerInvalid) {
            return [];
        }

        $items = collect();

        // Get invoices (subscription payments)
        try {
            $invoiceItems = $this->business->invoices()->map(function ($invoice) {
                $stripeInvoice = $invoice->asStripeInvoice();

                $receiptUrl = null;
                if ($stripeInvoice->status === 'paid' && $stripeInvoice->charge) {
                    try {
                        $charge = Cashier::stripe()->charges->retrieve($stripeInvoice->charge);
                        $receiptUrl = $charge->receipt_url;
                    } catch (\Exception) {
                        // Ignore if we can't fetch the charge
                    }
                }

                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->format('M j, Y'),
                    'timestamp' => $invoice->date()->timestamp,
                    'total' => $invoice->total(),
                    'status' => $stripeInvoice->status,
                    'invoice_pdf' => $stripeInvoice->invoice_pdf,
                    'receipt_url' => $receiptUrl,
                    'description' => $invoice->lines->first()?->description ?? 'Invoice',
                    'type' => 'invoice',
                ];
            });
            $items = $items->merge($invoiceItems);
        } catch (\Exception) {
            // Ignore invoice fetch errors
        }

        // Get standalone charges (one-time payments like credit purchases)
        try {
            $charges = Cashier::stripe()->charges->all([
                'customer' => $this->business->stripe_id,
                'limit' => 100,
            ]);

            // Get charge IDs that are already in invoices
            $invoiceChargeIds = $items->pluck('id')
                ->filter(fn ($id) => str_starts_with($id, 'in_'))
                ->map(function ($invoiceId) {
                    try {
                        $invoice = Cashier::stripe()->invoices->retrieve($invoiceId);

                        return $invoice->charge;
                    } catch (\Exception) {
                        return null;
                    }
                })
                ->filter()
                ->toArray();

            foreach ($charges->data as $charge) {
                // Skip charges that are part of an invoice (already shown above)
                if ($charge->invoice || in_array($charge->id, $invoiceChargeIds)) {
                    continue;
                }

                // Only show successful charges
                if ($charge->status !== 'succeeded') {
                    continue;
                }

                $items->push([
                    'id' => $charge->id,
                    'date' => \Carbon\Carbon::createFromTimestamp($charge->created)->format('M j, Y'),
                    'timestamp' => $charge->created,
                    'total' => '$'.number_format($charge->amount / 100, 2),
                    'status' => 'paid',
                    'invoice_pdf' => null,
                    'receipt_url' => $charge->receipt_url,
                    'description' => $charge->description ?? 'One-time payment',
                    'type' => 'charge',
                ]);
            }
        } catch (\Exception) {
            // Ignore charge fetch errors
        }

        // Sort by date descending
        return $items->sortByDesc('timestamp')->values()->toArray();
    }

    #[Computed]
    public function customerSince(): ?string
    {
        $customer = $this->stripeCustomer;

        if (! $customer?->created) {
            return null;
        }

        return \Carbon\Carbon::createFromTimestamp($customer->created)->format('M j, Y');
    }

    #[Computed]
    public function promotionCredits(): int
    {
        return $this->business->promotion_credits ?? 0;
    }

    #[Computed]
    public function isPromoted(): bool
    {
        return $this->business->is_promoted ?? false;
    }

    #[Computed]
    public function promotedUntil(): ?string
    {
        return $this->business->promoted_until?->format('F j, Y');
    }

    protected function validateStripeCustomer(): void
    {
        if (! $this->business->hasStripeId()) {
            return;
        }

        try {
            $this->business->asStripeCustomer();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->stripeCustomerInvalid = true;
            $this->stripeError = 'Stripe customer not found. The customer ID may have been deleted from Stripe.';
        } catch (\Exception $e) {
            $this->stripeError = 'Unable to connect to Stripe. Please try again later.';
        }
    }

    protected function validateStripeSubscription(): void
    {
        if ($this->stripeCustomerInvalid) {
            return;
        }

        $subscription = $this->business->subscription('default');

        if (! $subscription) {
            return;
        }

        $stripeId = $subscription->stripe_id;
        if (str_starts_with($stripeId, 'sub_test_') || str_starts_with($stripeId, 'sub_seeder_')) {
            $this->stripeSubscriptionInvalid = true;
            $this->stripeError = 'This subscription has invalid/seeded data.';

            return;
        }

        if ($subscription->stripe_status === 'canceled' || ($subscription->ends_at && $subscription->ends_at->isPast())) {
            return;
        }

        try {
            $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($stripeId);

            if (in_array($stripeSubscription->status, ['canceled', 'incomplete_expired'])) {
                $subscription->update([
                    'stripe_status' => $stripeSubscription->status,
                    'ends_at' => $subscription->ends_at ?? now(),
                ]);
            }
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->stripeSubscriptionInvalid = true;
            $this->stripeError = 'Subscription not found in Stripe.';
        } catch (\Exception $e) {
            // Don't block on connection errors
        }
    }

    public function resumeSubscription(): void
    {
        try {
            $this->isProcessing = true;
            $subscription = $this->subscription;

            if (! $subscription || ! $subscription->onGracePeriod()) {
                throw new \Exception('Cannot resume this subscription.');
            }

            $subscription->resume();

            Toaster::success('Subscription resumed successfully.');

            unset($this->subscription, $this->isSubscribed, $this->onGracePeriod);

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to resume subscription: '.$e->getMessage());
        }
    }

    public function syncStripeCustomer(): void
    {
        try {
            $this->isProcessing = true;

            if ($this->stripeCustomerInvalid) {
                $this->business->update(['stripe_id' => null]);
            }

            $owner = $this->ownerUser;

            $this->business->createAsStripeCustomer([
                'name' => $this->business->name ?? 'Business',
                'email' => $this->business->email ?? $owner?->email ?? '',
            ]);

            $this->stripeCustomerInvalid = false;
            $this->stripeError = null;

            Toaster::success('Stripe customer synced successfully.');

            unset($this->stripeCustomer, $this->paymentMethods, $this->billingHistory);

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to sync Stripe customer: '.$e->getMessage());
        }
    }

    public function getStripeCustomerUrl(): ?string
    {
        $customer = $this->stripeCustomer;

        if (! $customer) {
            return null;
        }

        $mode = $customer->livemode ? '' : 'test/';

        return "https://dashboard.stripe.com/{$mode}customers/{$customer->id}";
    }

    public function render()
    {
        return view('livewire.admin.businesses.tabs.billing-tab');
    }
}
