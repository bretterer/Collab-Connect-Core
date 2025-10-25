<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <flux:heading size="xl" class="mb-6">Billing & Subscription</flux:heading>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <flux:callout variant="success" class="mb-6 dark:text-white">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger" class="mb-6 dark:text-white">
            {{ session('error') }}
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Current Plan Section -->
            <flux:card>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <flux:heading size="lg" class="mb-1">Your Plan</flux:heading>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                            Manage your subscription plan
                        </flux:text>
                    </div>
                </div>

                @if($this->currentSubscription)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <flux:heading size="base">
                                        {{ $this->currentSubscription->stripe_price }}
                                    </flux:heading>
                                    @if($this->currentSubscription->onTrial())
                                        <flux:badge variant="info">Trial</flux:badge>
                                    @endif
                                    @if($this->currentSubscription->canceled())
                                        <flux:badge variant="warning">Cancels on {{ $this->currentSubscription->ends_at->format('M d, Y') }}</flux:badge>
                                    @elseif($this->currentSubscription->active())
                                        <flux:badge variant="success">Active</flux:badge>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    @if($this->currentSubscription->onTrial())
                                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                            Trial ends {{ $this->currentSubscription->trial_ends_at->format('M d, Y') }}
                                        </flux:text>
                                    @endif
                                    @if(!$this->currentSubscription->canceled())
                                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                            Renews {{ $this->currentSubscription->asStripeSubscription()->current_period_end ? \Carbon\Carbon::createFromTimestamp($this->currentSubscription->asStripeSubscription()->current_period_end)->format('M d, Y') : 'N/A' }}
                                        </flux:text>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <flux:button wire:click="openChangePlanModal" variant="ghost" size="sm">
                                    Change Plan
                                </flux:button>

                                @if($this->currentSubscription->canceled())
                                    <flux:button wire:click="resumeSubscription" variant="primary" size="sm">
                                        Resume
                                    </flux:button>
                                @else
                                    <flux:button wire:click="openCancelModal" variant="ghost" size="sm">
                                        Cancel
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <flux:heading size="base" class="mt-4 mb-2">No Active Subscription</flux:heading>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Subscribe to a plan to access all features
                            </flux:text>
                            <flux:button wire:click="openChangePlanModal" variant="primary">
                                Choose a Plan
                            </flux:button>
                        </div>
                    </div>
                @endif
            </flux:card>

            <!-- Payment Method Section -->
            <flux:card>
                <div class="mb-6">
                    <flux:heading size="lg" class="mb-1">Payment Method</flux:heading>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                        Manage your payment information
                    </flux:text>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    @if($billableModel->hasDefaultPaymentMethod())
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <flux:text class="font-medium">
                                        {{ ucfirst($billableModel->defaultPaymentMethod()->card->brand) }} ending in {{ $billableModel->defaultPaymentMethod()->card->last4 }}
                                    </flux:text>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                        Expires {{ $billableModel->defaultPaymentMethod()->card->exp_month }}/{{ $billableModel->defaultPaymentMethod()->card->exp_year }}
                                    </flux:text>
                                </div>
                            </div>
                            <flux:button wire:click="goToStripePortal" variant="ghost" size="sm" icon="pencil">
                                Update
                            </flux:button>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                No payment method on file
                            </flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Invoices Section -->
            <flux:card>
                <div class="mb-6">
                    <flux:heading size="lg" class="mb-1">Billing History</flux:heading>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                        View and download your invoices
                    </flux:text>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700">
                    @if($this->invoices->count() > 0)
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->invoices as $invoice)
                                <div class="flex items-center justify-between py-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <flux:text class="font-medium">
                                                {{ $invoice->date()->format('M d, Y') }}
                                            </flux:text>
                                            @if($invoice->paid)
                                                <flux:badge variant="success">Paid</flux:badge>
                                            @else
                                                <flux:badge variant="warning">Unpaid</flux:badge>
                                            @endif
                                        </div>
                                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $invoice->total() }}
                                        </flux:text>
                                    </div>
                                    <flux:button
                                        wire:click="downloadInvoice('{{ $invoice->id }}')"
                                        variant="ghost"
                                        size="sm"
                                        icon="arrow-down-tray"
                                    >
                                        Download
                                    </flux:button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                                No invoices yet
                            </flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Billing Info Card -->
            <flux:card>
                <flux:heading size="base" class="mb-4">Billing Information</flux:heading>

                <div class="space-y-4">
                    @if($billableModel instanceof \App\Models\Business)
                        <div>
                            <flux:text class="text-xs text-gray-500 dark:text-gray-400 uppercase">Business Name</flux:text>
                            <flux:text class="font-medium">{{ $billableModel->name }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-xs text-gray-500 dark:text-gray-400 uppercase">Email</flux:text>
                            <flux:text class="font-medium">{{ $billableModel->email }}</flux:text>
                        </div>
                    @else
                        <div>
                            <flux:text class="text-xs text-gray-500 dark:text-gray-400 uppercase">Name</flux:text>
                            <flux:text class="font-medium">{{ $billableModel->user->name }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-xs text-gray-500 dark:text-gray-400 uppercase">Email</flux:text>
                            <flux:text class="font-medium">{{ $billableModel->user->email }}</flux:text>
                        </div>
                    @endif

                    @if($billableModel->city || $billableModel->state)
                        <div>
                            <flux:text class="text-xs text-gray-500 dark:text-gray-400 uppercase">Location</flux:text>
                            <flux:text class="font-medium">
                                {{ collect([$billableModel->city, $billableModel->state, $billableModel->postal_code])->filter()->implode(', ') }}
                            </flux:text>
                        </div>
                    @endif
                </div>

                <flux:separator class="my-4" />

                <flux:button wire:click="goToStripePortal" variant="ghost" size="sm" class="w-full">
                    Manage via Stripe Portal
                </flux:button>
            </flux:card>

            <!-- Help Card -->
            <flux:card>
                <flux:heading size="base" class="mb-2">Need Help?</flux:heading>
                <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Have questions about your subscription or billing? We're here to help.
                </flux:text>
                <flux:button variant="ghost" size="sm" class="w-full">
                    Contact Support
                </flux:button>
            </flux:card>
        </div>
    </div>

    <!-- Change Plan Modal -->
    <flux:modal wire:model="showChangePlanModal" class="max-w-5xl">
        <flux:heading size="lg" class="mb-6">
            {{ $this->currentSubscription ? 'Change Your Plan' : 'Choose a Plan' }}
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($this->availablePlans as $product)
                @foreach($product->prices as $price)
                    <div
                        wire:click="selectPrice({{ $price->id }})"
                        class="relative border-2 rounded-lg p-6 cursor-pointer transition-all
                            {{ $selectedPriceId === $price->id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    >
                        @if($this->currentSubscription && $this->currentSubscription->stripe_price === $price->stripe_id)
                            <flux:badge variant="info" class="absolute top-4 right-4">Current</flux:badge>
                        @endif

                        <flux:heading size="base" class="mb-2">{{ $product->name }}</flux:heading>

                        <div class="mb-4">
                            <span class="text-3xl font-bold dark:text-white">${{ number_format($price->unit_amount / 100, 2) }}</span>
                            <span class="text-gray-600 dark:text-gray-400">
                                /{{ $price->recurring['interval'] ?? 'month' }}
                            </span>
                        </div>

                        @if($product->description)
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ $product->description }}
                            </flux:text>
                        @endif

                        @if($selectedPriceId === $price->id)
                            <div class="absolute inset-0 border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>

        @if(!$this->currentSubscription)
            <div class="mb-6">
                <flux:separator class="mb-6" />
                <livewire:components.stripe-payment-form />
            </div>
        @endif

        <div class="flex justify-end gap-3">
            <flux:button wire:click="closeChangePlanModal" variant="ghost">
                Cancel
            </flux:button>
            <flux:button
                wire:click="changePlan"
                variant="primary"
                :disabled="!$selectedPriceId || $isProcessing"
            >
                {{ $this->currentSubscription ? 'Change Plan' : 'Subscribe' }}
            </flux:button>
        </div>
    </flux:modal>

    <!-- Cancel Subscription Modal -->
    <flux:modal wire:model="showCancelModal" class="max-w-md">
        <flux:heading size="lg" class="mb-4">Cancel Subscription</flux:heading>

        <flux:text class="mb-6">
            Are you sure you want to cancel your subscription? You'll continue to have access until the end of your current billing period.
        </flux:text>

        <div class="flex justify-end gap-3">
            <flux:button wire:click="closeCancelModal" variant="ghost">
                Keep Subscription
            </flux:button>
            <flux:button wire:click="cancelSubscription" variant="danger">
                Cancel Subscription
            </flux:button>
        </div>
    </flux:modal>

</div>

