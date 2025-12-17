<?php

namespace App\Livewire\Admin\Users\Tabs;

use App\Models\Business;
use App\Models\Influencer;
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
    public User $user;

    // Processing state
    public bool $isProcessing = false;

    // Error tracking
    public bool $stripeCustomerInvalid = false;

    public bool $stripeSubscriptionInvalid = false;

    public ?string $stripeError = null;

    public function mount(User $user): void
    {
        $this->user = $user->load(['businesses', 'influencer']);

        // Set the correct Cashier model based on billable type
        $this->setCashierModel();

        // Validate Stripe data
        if ($this->billable) {
            $this->validateStripeCustomer();
            $this->validateStripeSubscription();
        }
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

    #[On('subscription-updated')]
    #[On('coupon-applied')]
    public function refreshData(): void
    {
        // Clear computed property caches
        unset(
            $this->subscription,
            $this->isSubscribed,
            $this->onTrial,
            $this->onGracePeriod,
            $this->nextBillingDate,
            $this->pendingSchedule,
            $this->activeDiscounts,
            $this->currentPlan,
            $this->stripeCustomer,
            $this->paymentMethods,
            $this->invoices
        );
    }

    #[Computed]
    public function billable(): Business|Influencer|null
    {
        if ($this->user->isBusinessAccount()) {
            return $this->user->businesses()
                ->wherePivot('role', 'owner')
                ->first();
        }

        if ($this->user->isInfluencerAccount() && $this->user->influencer) {
            return $this->user->influencer;
        }

        return null;
    }

    #[Computed]
    public function subscription(): ?Subscription
    {
        return $this->billable?->subscription('default');
    }

    #[Computed]
    public function isSubscribed(): bool
    {
        if ($this->stripeCustomerInvalid || $this->stripeSubscriptionInvalid) {
            return false;
        }

        try {
            return $this->billable?->subscribed('default') ?? false;
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
            return $this->billable?->onTrial('default') ?? false;
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

            // Get the upcoming phase
            $upcomingPhase = $schedule->phases[1] ?? null;
            if (! $upcomingPhase) {
                return null;
            }

            $upcomingPriceId = $upcomingPhase->items[0]->price ?? null;

            // Look up the price to get the lookup_key
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
            // Check customer-level discount
            $customer = $this->stripeCustomer;
            if ($customer?->discount) {
                $discounts[] = $this->formatDiscount($customer->discount, 'Customer');
            }

            // Check subscription-level discount
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
        if (! $this->billable?->hasStripeId() || $this->stripeCustomerInvalid) {
            return null;
        }

        try {
            return $this->billable->asStripeCustomer();
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Computed]
    public function paymentMethods(): array
    {
        if (! $this->billable?->hasStripeId() || $this->stripeCustomerInvalid) {
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
        if (! $this->billable?->hasStripeId() || $this->stripeCustomerInvalid) {
            return [];
        }

        try {
            return $this->billable->invoices()->map(function ($invoice) {
                $stripeInvoice = $invoice->asStripeInvoice();

                // Get receipt PDF URL from the charge if invoice is paid
                $receiptPdfUrl = null;
                if ($stripeInvoice->status === 'paid' && $stripeInvoice->charge) {
                    try {
                        $charge = Cashier::stripe()->charges->retrieve($stripeInvoice->charge);
                        if ($charge->receipt_url) {
                            // Convert receipt URL to direct PDF download by adding /pdf before query params
                            $receiptPdfUrl = preg_replace('/(\?|$)/', '/pdf$1', $charge->receipt_url, 1);
                        }
                    } catch (\Exception $e) {
                        // Ignore if we can't fetch the charge
                    }
                }

                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->format('M j, Y'),
                    'total' => $invoice->total(),
                    'status' => $stripeInvoice->status,
                    'invoice_pdf' => $stripeInvoice->invoice_pdf,
                    'receipt_pdf' => $receiptPdfUrl,
                    'description' => $invoice->lines->first()?->description ?? 'Invoice',
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
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

    protected function validateStripeCustomer(): void
    {
        $billable = $this->billable;

        if (! $billable?->hasStripeId()) {
            return;
        }

        try {
            $billable->asStripeCustomer();
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

        $subscription = $this->billable?->subscription('default');

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

            // Reset computed properties
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
            $billable = $this->billable;

            if (! $billable) {
                throw new \Exception('No billable profile found.');
            }

            if ($this->stripeCustomerInvalid) {
                $billable->update(['stripe_id' => null]);
            }

            $billable->createAsStripeCustomer([
                'name' => $this->getCustomerName(),
                'email' => $this->getCustomerEmail(),
            ]);

            $this->stripeCustomerInvalid = false;
            $this->stripeError = null;

            Toaster::success('Stripe customer synced successfully.');

            unset($this->stripeCustomer, $this->paymentMethods, $this->invoices);

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

    protected function getCustomerName(): string
    {
        $billable = $this->billable;

        if ($billable instanceof Business) {
            return $billable->name ?? 'Business';
        }

        return $this->user->name ?? 'Customer';
    }

    protected function getCustomerEmail(): string
    {
        $billable = $this->billable;

        if ($billable instanceof Business) {
            return $billable->email ?? $this->user->email ?? '';
        }

        return $this->user->email ?? '';
    }

    public function render()
    {
        return view('livewire.admin.users.tabs.billing-tab');
    }
}
