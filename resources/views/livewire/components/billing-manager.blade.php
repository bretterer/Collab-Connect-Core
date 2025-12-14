<div class="space-y-6">
    <!-- Include Checkout Modal -->
    <livewire:components.checkout-modal />

    <!-- Stripe Error Alert -->
    @if($stripeError)
        <flux:callout icon="exclamation-triangle" variant="warning" class="dark:bg-yellow-900/20">
            <flux:callout.heading>Billing Account Issue</flux:callout.heading>
            <flux:callout.text>{{ $stripeError }}</flux:callout.text>
        </flux:callout>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <nav class="flex space-x-8" aria-label="Billing sections">
            <button
                type="button"
                wire:click="setActiveSection('overview')"
                class="py-3 px-1 border-b-2 text-sm font-medium transition-colors {{ $activeSection === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                Overview
            </button>
            <button
                type="button"
                wire:click="setActiveSection('plans')"
                class="py-3 px-1 border-b-2 text-sm font-medium transition-colors {{ $activeSection === 'plans' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                Plans
            </button>
            <button
                type="button"
                wire:click="setActiveSection('payment-methods')"
                class="py-3 px-1 border-b-2 text-sm font-medium transition-colors {{ $activeSection === 'payment-methods' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                Payment Methods
            </button>
            <button
                type="button"
                wire:click="setActiveSection('invoices')"
                class="py-3 px-1 border-b-2 text-sm font-medium transition-colors {{ $activeSection === 'invoices' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                Invoices
            </button>
        </nav>
    </div>

    <!-- Loading Overlay -->
    @if($isProcessing)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 flex items-center gap-4">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-zinc-700 dark:text-zinc-300">Processing...</span>
            </div>
        </div>
    @endif

    <!-- Overview Section -->
    @if($activeSection === 'overview')
        <div class="space-y-6">
            <!-- Current Subscription Status -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading>Current Subscription</flux:heading>
                    @if($this->isSubscribed)
                        @if($this->onGracePeriod)
                            <flux:badge color="yellow">Canceling</flux:badge>
                        @elseif($this->onTrial)
                            <flux:badge color="blue">Trial</flux:badge>
                        @else
                            <flux:badge color="green">Active</flux:badge>
                        @endif
                    @else
                        <flux:badge color="zinc">No Active Subscription</flux:badge>
                    @endif
                </div>

                @if($this->isSubscribed)
                    @php $currentPlan = $this->currentPlan; @endphp
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $currentPlan?->product?->name ?? 'Subscription' }}</p>
                                <p class="text-zinc-600 dark:text-zinc-300">
                                    ${{ number_format(($currentPlan?->unit_amount ?? 0) / 100, 2) }}/{{ $currentPlan?->recurring['interval'] ?? 'month' }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                @if($this->onGracePeriod)
                                    <flux:button type="button" wire:click="resumeSubscription" variant="primary" size="sm">
                                        Resume Subscription
                                    </flux:button>
                                @else
                                    <flux:button type="button" wire:click="setActiveSection('plans')" variant="ghost" size="sm">
                                        Change Plan
                                    </flux:button>
                                    <flux:button type="button" wire:click="$set('showCancelModal', true)" variant="danger" size="sm">
                                        Cancel
                                    </flux:button>
                                @endif
                            </div>
                        </div>

                        @if($this->onTrial)
                            <flux:callout icon="information-circle" class="dark:bg-blue-900/20">
                                Your trial ends on {{ $this->trialEndsAt }}. You will be charged after the trial period.
                            </flux:callout>
                        @endif

                        @if($this->onGracePeriod)
                            <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700">
                                <div class="flex items-center gap-3">
                                    <flux:icon.exclamation-triangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 shrink-0" />
                                    <p class="text-yellow-800 dark:text-yellow-200">
                                        Your subscription has been canceled and will end on <span class="font-semibold">{{ $this->subscription?->ends_at?->format('F j, Y') }}</span>.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6">
                        <flux:icon.credit-card class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                        <flux:text class="text-zinc-500 mb-4">You don't have an active subscription.</flux:text>
                        <flux:button type="button" wire:click="setActiveSection('plans')" variant="primary">
                            View Plans
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Upcoming Invoice -->
            @if($this->isSubscribed && $this->upcomingInvoice && !$this->onGracePeriod)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:heading class="mb-4">Upcoming Invoice</flux:heading>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Next billing date</p>
                            <p class="font-medium text-zinc-900 dark:text-white">{{ $this->upcomingInvoice['date'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Amount due</p>
                            <p class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $this->upcomingInvoice['total'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            @if($this->billable->hasStripeId() && !$stripeCustomerInvalid)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Payment Methods</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ count($this->paymentMethods) }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Invoices</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ count($this->invoices) }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Customer Since</p>
                        <p class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ $this->customerSince }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Plans Section -->
    @if($activeSection === 'plans')
        <div class="space-y-6">
            <!-- Current Plan (if on inactive/legacy plan) -->
            @if($this->isSubscribed && $this->currentPlanIsInactive && $this->currentPlan)
                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <flux:heading>Your Current Plan</flux:heading>
                                <flux:badge color="yellow">Legacy Plan</flux:badge>
                            </div>
                            <flux:text class="text-amber-700 dark:text-amber-300 text-sm mb-3">
                                This plan is no longer available for new subscriptions.
                            </flux:text>
                            <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                                ${{ number_format($this->currentPlan->unit_amount / 100, 2) }}
                                <span class="text-base font-normal text-zinc-500">/{{ $this->currentPlan->recurring['interval'] ?? 'month' }}</span>
                            </div>
                        </div>
                        <flux:icon.exclamation-triangle class="w-8 h-8 text-amber-500" />
                    </div>
                </div>
            @endif

            <!-- Available Plans -->
            @foreach($this->availablePlans as $product)
                <div>
                    <div class="mb-6">
                        <flux:heading>{{ $product->name }}</flux:heading>
                        @if($product->description)
                            <flux:text class="text-zinc-500">{{ $product->description }}</flux:text>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($product->prices->sortBy('unit_amount') as $price)
                            @php
                                $isCurrentPlan = $this->isSubscribed && $this->currentPlan?->stripe_id === $price->stripe_id;
                                $recurring = $price->recurring;
                                $interval = $recurring['interval'] ?? 'month';
                                $intervalCount = $recurring['interval_count'] ?? 1;
                                $intervalLabel = $intervalCount > 1 ? "$intervalCount {$interval}s" : $interval;
                            @endphp
                            <div class="relative flex flex-col rounded-xl border-2 transition-all {{ $isCurrentPlan ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500/20' : 'border-zinc-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700' }} bg-white dark:bg-zinc-800">
                                @if($isCurrentPlan)
                                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-white dark:bg-zinc-800 px-1 rounded">
                                        <flux:badge color="blue">Current Plan</flux:badge>
                                    </div>
                                @endif

                                <div class="p-6 flex-1">
                                    <div class="text-center mb-2">
                                        <flux:heading class="text-lg">{{ $price->product_name ?? $product->name }}</flux:heading>
                                    </div>

                                    <div class="text-center mb-6">
                                        <div class="flex items-baseline justify-center gap-1">
                                            <span class="text-4xl font-bold text-zinc-900 dark:text-white">${{ number_format($price->unit_amount / 100, 0) }}</span>
                                            @if(fmod($price->unit_amount / 100, 1) > 0)
                                                <span class="text-xl font-bold text-zinc-900 dark:text-white">.{{ substr(number_format($price->unit_amount / 100, 2), -2) }}</span>
                                            @endif
                                        </div>
                                        <flux:text class="text-zinc-500 text-sm">per {{ $intervalLabel }}</flux:text>
                                        @if($interval === 'year')
                                            <flux:text class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                Save with annual billing
                                            </flux:text>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-6 pt-0">
                                    @if($isCurrentPlan)
                                        <div class="w-full py-2.5 text-center text-sm font-medium text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 rounded-lg bg-blue-50 dark:bg-blue-900/30">
                                            Current plan
                                        </div>
                                    @elseif($this->isSubscribed)
                                        <flux:button type="button" wire:click="changePlan('{{ $price->stripe_id }}')" variant="filled" class="w-full">
                                            Switch plan
                                        </flux:button>
                                    @else
                                        <flux:button type="button" wire:click="subscribe('{{ $price->stripe_id }}')" variant="primary" class="w-full">
                                            Get started
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if($this->availablePlans->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <flux:icon.cube class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                    <flux:text class="text-zinc-500">No plans available at this time.</flux:text>
                </div>
            @endif
        </div>
    @endif

    <!-- Payment Methods Section -->
    @if($activeSection === 'payment-methods')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <flux:heading>Payment Methods</flux:heading>
                <flux:button type="button" wire:click="openAddPaymentMethodModal" variant="primary" size="sm" icon="plus">
                    Add Payment Method
                </flux:button>
            </div>

            @if(count($this->paymentMethods) > 0)
                <div class="space-y-3">
                    @foreach($this->paymentMethods as $method)
                        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-8 bg-zinc-100 dark:bg-zinc-700 rounded flex items-center justify-center">
                                    @if(strtolower($method['brand']) === 'visa')
                                        <span class="text-blue-600 font-bold text-sm">VISA</span>
                                    @elseif(strtolower($method['brand']) === 'mastercard')
                                        <span class="text-red-600 font-bold text-sm">MC</span>
                                    @elseif(strtolower($method['brand']) === 'amex')
                                        <span class="text-blue-500 font-bold text-sm">AMEX</span>
                                    @else
                                        <flux:icon.credit-card class="w-5 h-5 text-zinc-400" />
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <flux:text class="font-medium">{{ $method['brand'] }} **** {{ $method['last4'] }}</flux:text>
                                        @if($method['is_default'])
                                            <flux:badge color="green" size="sm">Default</flux:badge>
                                        @endif
                                    </div>
                                    <flux:text class="text-zinc-500 text-sm">Expires {{ $method['exp_month'] }}/{{ $method['exp_year'] }}</flux:text>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!$method['is_default'])
                                    <flux:button type="button" wire:click="setDefaultPaymentMethod('{{ $method['id'] }}')" variant="ghost" size="sm">
                                        Set Default
                                    </flux:button>
                                @endif
                                <flux:button type="button" wire:click="confirmDeletePaymentMethod('{{ $method['id'] }}')" variant="ghost" size="sm" icon="trash" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:icon.credit-card class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                    <flux:text class="text-zinc-500 mb-4">No payment methods on file.</flux:text>
                    <flux:button type="button" wire:click="openAddPaymentMethodModal" variant="primary">
                        Add Payment Method
                    </flux:button>
                </div>
            @endif
        </div>
    @endif

    <!-- Invoices Section -->
    @if($activeSection === 'invoices')
        <div class="space-y-6">
            <flux:heading>Billing History</flux:heading>

            @if(count($this->invoices) > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">Amount</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-zinc-500 dark:text-zinc-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($this->invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-3">
                                        <flux:text class="font-medium">{{ $invoice['date'] }}</flux:text>
                                    </td>
                                    <td class="px-4 py-3">
                                        <flux:text>{{ $invoice['total'] }}</flux:text>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($invoice['status'] === 'paid')
                                            <flux:badge color="green">Paid</flux:badge>
                                        @elseif($invoice['status'] === 'open')
                                            <flux:badge color="yellow">Open</flux:badge>
                                        @else
                                            <flux:badge color="zinc">{{ ucfirst($invoice['status']) }}</flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($invoice['pdf_url'])
                                            <flux:button href="{{ $invoice['pdf_url'] }}" target="_blank" variant="ghost" size="sm" icon="arrow-down-tray">
                                                Download
                                            </flux:button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:icon.document-text class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                    <flux:text class="text-zinc-500">No invoices yet.</flux:text>
                </div>
            @endif
        </div>
    @endif

    <!-- Cancel Subscription Modal -->
    <flux:modal wire:model="showCancelModal" class="max-w-lg">
        <div class="p-6">
            <flux:heading size="xl" class="mb-4">Cancel Subscription</flux:heading>
            <flux:text class="text-zinc-500 mb-6">
                Are you sure you want to cancel your subscription? You'll retain access until the end of your current billing period and can resume anytime before then.
            </flux:text>

            <div class="flex justify-end gap-3">
                <flux:button type="button" wire:click="$set('showCancelModal', false)" variant="ghost">
                    Keep Subscription
                </flux:button>
                <flux:button type="button" wire:click="cancelSubscription" variant="danger">
                    Cancel Subscription
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Delete Payment Method Modal -->
    <flux:modal wire:model="showDeletePaymentMethodModal" class="max-w-md">
        <div class="p-6">
            <flux:heading size="xl" class="mb-4">Remove Payment Method</flux:heading>
            <flux:text class="text-zinc-500 mb-6">
                Are you sure you want to remove this payment method? This action cannot be undone.
            </flux:text>

            <div class="flex justify-end gap-3">
                <flux:button type="button" wire:click="$set('showDeletePaymentMethodModal', false)" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="button" wire:click="deletePaymentMethod" variant="danger">
                    Remove
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Add Payment Method Modal -->
    <flux:modal wire:model="showAddPaymentMethodModal" class="max-w-lg">
        <div class="p-6">
            <flux:heading size="xl" class="mb-4">Add Payment Method</flux:heading>

            @include('livewire.components.stripe-payment-form')

            <div class="flex justify-end gap-3 mt-6">
                <flux:button type="button" wire:click="$set('showAddPaymentMethodModal', false)" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="button" onclick="Livewire.dispatch('createStripePaymentMethod')" variant="primary">
                    Add Payment Method
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
