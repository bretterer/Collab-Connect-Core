<div>
    <div class="space-y-6" wire:loading.class="opacity-50">
        {{-- Stripe Error Alert --}}
        @if($stripeError)
            <flux:callout variant="danger" icon="exclamation-triangle">
                <flux:callout.heading>Stripe Error</flux:callout.heading>
                <flux:callout.text>{{ $stripeError }}</flux:callout.text>
                @if($stripeCustomerInvalid)
                    <x-slot:actions>
                        <flux:button wire:click="syncStripeCustomer" variant="danger" size="sm" wire:loading.attr="disabled">
                            Resync Customer
                        </flux:button>
                    </x-slot:actions>
                @endif
            </flux:callout>
        @endif

        <!-- Subscription Status Overview -->
        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading>Subscription Status</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                        Manage subscription and billing for this influencer account.
                    </flux:text>
                </div>
                <flux:badge color="{{ $this->subscriptionStatusColor }}" size="sm">{{ $this->subscriptionStatus }}</flux:badge>
            </div>

            {{-- Subscription Details --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Current Plan</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-white">
                            @if($this->currentPlan)
                                {{ $this->currentPlan->lookup_key ? Str::headline($this->currentPlan->lookup_key) : $this->currentPlan->product_name }}
                            @else
                                No Plan
                            @endif
                        </flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Status</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->subscriptionStatus }}
                        </flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                            @if($this->onTrial)
                                Trial Ends
                            @elseif($this->onGracePeriod)
                                Access Until
                            @else
                                Next Billing
                            @endif
                        </flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-white">
                            @if($this->onTrial && $this->trialEndsAt)
                                {{ $this->trialEndsAt }}
                            @elseif($this->onGracePeriod && $this->subscription?->ends_at)
                                {{ $this->subscription->ends_at->format('F j, Y') }}
                            @elseif($this->nextBillingDate)
                                {{ $this->nextBillingDate }}
                            @elseif($this->subscription)
                                --
                            @else
                                N/A
                            @endif
                        </flux:text>
                    </div>
                </div>
            </div>

            {{-- Active Discounts --}}
            @if(count($this->activeDiscounts) > 0)
                <div class="border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <flux:icon name="ticket" class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" />
                        <div class="flex-1">
                            <flux:heading size="sm">Active Discounts</flux:heading>
                            <div class="mt-2 space-y-2">
                                @foreach($this->activeDiscounts as $discount)
                                    <div class="flex items-center justify-between text-sm">
                                        <div>
                                            <span class="font-medium text-green-700 dark:text-green-300">{{ $discount['name'] }}</span>
                                            <span class="text-green-600 dark:text-green-400">({{ $discount['discount'] }})</span>
                                            <flux:badge color="zinc" size="sm" class="ml-1">{{ $discount['level'] }}</flux:badge>
                                        </div>
                                        <div class="text-green-600 dark:text-green-400">
                                            @if($discount['ends_at'])
                                                Ends {{ $discount['ends_at'] }}
                                            @else
                                                {{ $discount['duration'] }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Scheduled Plan Change --}}
            @if($this->pendingSchedule && $this->influencerUser)
                <div class="border border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <flux:icon name="calendar" class="w-5 h-5 text-purple-600 dark:text-purple-400 mt-0.5" />
                            <div>
                                <flux:heading size="sm">Scheduled Plan Change</flux:heading>
                                <flux:text class="text-sm text-purple-700 dark:text-purple-300 mt-1">
                                    Changing to <strong>{{ $this->pendingSchedule['plan_name'] }}</strong> on {{ $this->pendingSchedule['starts_at'] }}.
                                </flux:text>
                            </div>
                        </div>
                        <flux:button wire:click="$dispatch('open-swap-plan-modal', { userId: {{ $this->influencerUser->id }} })" variant="ghost" size="sm">
                            Manage
                        </flux:button>
                    </div>
                </div>
            @endif

            {{-- Trial Alert --}}
            @if($this->onTrial && $this->influencerUser)
                <div class="border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <flux:icon name="clock" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                            <div>
                                <flux:heading size="sm">Trial Period Active</flux:heading>
                                <flux:text class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                    This influencer is on a trial that ends on {{ $this->trialEndsAt }}.
                                    @if($this->trialDaysRemaining !== null)
                                        ({{ $this->trialDaysRemaining }} {{ Str::plural('day', $this->trialDaysRemaining) }} remaining)
                                    @endif
                                </flux:text>
                            </div>
                        </div>
                        <flux:button wire:click="$dispatch('open-cancel-trial-modal', { userId: {{ $this->influencerUser->id }} })" variant="ghost" size="sm">
                            End Trial
                        </flux:button>
                    </div>
                </div>
            @endif

            {{-- Grace Period Alert --}}
            @if($this->onGracePeriod)
                <div class="border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <flux:icon name="exclamation-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                            <div>
                                <flux:heading size="sm">Subscription Canceled</flux:heading>
                                <flux:text class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    This subscription is canceled but the influencer still has access until {{ $this->subscription?->ends_at?->format('F j, Y') }}.
                                </flux:text>
                            </div>
                        </div>
                        <flux:button wire:click="resumeSubscription" variant="primary" size="sm" wire:loading.attr="disabled">
                            Resume Subscription
                        </flux:button>
                    </div>
                </div>
            @endif

            <flux:separator class="my-6" />

            <!-- Quick Actions -->
            @if($this->influencerUser)
                <div>
                    <flux:heading size="sm" class="mb-4">Quick Actions</flux:heading>
                    <div class="flex flex-wrap gap-3">
                        @if(!$this->isSubscribed)
                            <flux:button wire:click="$dispatch('open-start-trial-modal', { userId: {{ $this->influencerUser->id }} })" variant="primary" icon="play">
                                Start Trial
                            </flux:button>
                        @endif

                        @if($this->onTrial)
                            <flux:button wire:click="$dispatch('open-cancel-trial-modal', { userId: {{ $this->influencerUser->id }} })" variant="filled" icon="stop">
                                End Trial
                            </flux:button>
                        @endif

                        @if($this->isSubscribed && !$this->onGracePeriod)
                            <flux:button wire:click="$dispatch('open-swap-plan-modal', { userId: {{ $this->influencerUser->id }} })" variant="filled" icon="arrows-right-left">
                                Change Plan
                            </flux:button>
                            <flux:button wire:click="$dispatch('open-extend-trial-modal', { userId: {{ $this->influencerUser->id }} })" variant="filled" icon="clock">
                                Add Trial Days
                            </flux:button>
                            <flux:button wire:click="$dispatch('open-cancel-subscription-modal', { userId: {{ $this->influencerUser->id }} })" variant="danger" icon="x-mark">
                                Cancel Subscription
                            </flux:button>
                        @endif

                        @if($influencer->hasStripeId())
                            <flux:button wire:click="$dispatch('open-apply-coupon-modal', { userId: {{ $this->influencerUser->id }} })" variant="filled" icon="ticket">
                                Apply Coupon
                            </flux:button>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <flux:text class="text-gray-500 dark:text-gray-400">
                        No user found for this influencer. Subscription actions are unavailable.
                    </flux:text>
                </div>
            @endif
        </flux:card>

        <!-- Promotion Credits Section -->
        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading>Promotion Credits</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                        Manage profile promotion credits for this influencer.
                    </flux:text>
                </div>
                <flux:badge color="{{ $this->promotionCredits > 0 ? 'green' : 'zinc' }}" size="sm">
                    {{ $this->promotionCredits }} {{ Str::plural('credit', $this->promotionCredits) }}
                </flux:badge>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Available Credits</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-white text-2xl">
                            {{ $this->promotionCredits }}
                        </flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Promotion Status</flux:text>
                        <div class="flex items-center gap-2">
                            @if($this->isPromoted)
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Inactive</flux:badge>
                            @endif
                        </div>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Promoted Until</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->promotedUntil ?? 'N/A' }}
                        </flux:text>
                    </div>
                </div>
            </div>

            @if($this->isPromoted)
                <div class="border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <flux:icon name="sparkles" class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" />
                        <div>
                            <flux:heading size="sm">Profile is Currently Promoted</flux:heading>
                            <flux:text class="text-sm text-green-700 dark:text-green-300 mt-1">
                                This profile is highlighted in search results until {{ $this->promotedUntil }}.
                            </flux:text>
                        </div>
                    </div>
                </div>
            @endif

            <flux:separator class="my-6" />

            @if($this->influencerUser)
                <div>
                    <flux:heading size="sm" class="mb-4">Credit Actions</flux:heading>
                    <div class="flex flex-wrap gap-3">
                        <flux:button wire:click="$dispatch('open-grant-credits-modal', { userId: {{ $this->influencerUser->id }} })" variant="primary" icon="plus">
                            Grant Credits
                        </flux:button>
                        @if($this->promotionCredits > 0)
                            <flux:button wire:click="$dispatch('open-revoke-credits-modal', { userId: {{ $this->influencerUser->id }} })" variant="danger" icon="minus">
                                Revoke Credits
                            </flux:button>
                        @endif
                    </div>
                </div>
            @endif
        </flux:card>

        <!-- Payment Method Section -->
        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <flux:heading>Payment Methods</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                        View the influencer's saved payment methods.
                    </flux:text>
                </div>
            </div>

            @if(count($this->paymentMethods) > 0)
                <div class="space-y-3">
                    @foreach($this->paymentMethods as $method)
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-10 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-sm">{{ strtoupper($method['brand']) }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <flux:text class="font-medium text-gray-900 dark:text-white">
                                            {{ $method['brand'] }} **** {{ $method['last4'] }}
                                        </flux:text>
                                        @if($method['is_default'])
                                            <flux:badge color="green" size="sm">Default</flux:badge>
                                        @endif
                                    </div>
                                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                                        Expires {{ $method['exp_month'] }}/{{ $method['exp_year'] }}
                                    </flux:text>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon name="credit-card" class="w-10 h-10 mx-auto text-gray-400 mb-3" />
                    <flux:text class="text-gray-500 dark:text-gray-400">No payment methods on file.</flux:text>
                </div>
            @endif
        </flux:card>

        <!-- Invoices & Receipts Section -->
        <flux:card>
            <div class="mb-6">
                <flux:heading>Invoices & Receipts</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                    View billing history and download invoices.
                </flux:text>
            </div>

            @if(count($this->billingHistory) > 0)
                <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Description</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->billingHistory as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                    <td class="px-4 py-3">
                                        <flux:text class="text-sm">{{ $item['date'] }}</flux:text>
                                    </td>
                                    <td class="px-4 py-3">
                                        <flux:text class="text-sm font-medium">{{ $item['description'] }}</flux:text>
                                    </td>
                                    <td class="px-4 py-3">
                                        <flux:text class="text-sm font-medium">{{ $item['total'] }}</flux:text>
                                    </td>
                                    <td class="px-4 py-3">
                                        <flux:badge
                                            color="{{ $item['status'] === 'paid' ? 'green' : ($item['status'] === 'open' ? 'yellow' : 'zinc') }}"
                                            size="sm"
                                        >
                                            {{ ucfirst($item['status']) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            @if($item['invoice_pdf'])
                                                <flux:button href="{{ $item['invoice_pdf'] }}" target="_blank" variant="ghost" size="sm" icon="document-text" title="Download Invoice">
                                                    Invoice
                                                </flux:button>
                                            @endif
                                            @if($item['status'] === 'paid' && $item['receipt_url'])
                                                <flux:button href="{{ $item['receipt_url'] }}" target="_blank" variant="ghost" size="sm" icon="receipt-percent" title="View Receipt">
                                                    Receipt
                                                </flux:button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon name="document-text" class="w-10 h-10 mx-auto text-gray-400 mb-3" />
                    <flux:text class="text-gray-500 dark:text-gray-400">No billing history found.</flux:text>
                </div>
            @endif
        </flux:card>

        <!-- Stripe Customer Info -->
        <flux:card>
            <div class="mb-4">
                <flux:heading size="sm">Stripe Customer Information</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Customer ID</flux:text>
                    @if($this->stripeCustomer)
                        <flux:text class="font-mono text-sm">{{ $this->stripeCustomer->id }}</flux:text>
                    @else
                        <flux:text class="text-sm text-gray-400">Not created</flux:text>
                    @endif
                </div>
                <div>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Customer Since</flux:text>
                    <flux:text class="text-sm">{{ $this->customerSince ?? 'N/A' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">View in Stripe</flux:text>
                    @if($this->getStripeCustomerUrl())
                        <flux:button
                            href="{{ $this->getStripeCustomerUrl() }}"
                            target="_blank"
                            variant="ghost"
                            size="sm"
                            icon="arrow-top-right-on-square"
                        >
                            Open Stripe Dashboard
                        </flux:button>
                    @else
                        <flux:text class="text-sm text-gray-400">Not available</flux:text>
                    @endif
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Modal Components -->
    <livewire:admin.users.modals.start-trial-modal />
    <livewire:admin.users.modals.cancel-trial-modal />
    <livewire:admin.users.modals.cancel-subscription-modal />
    <livewire:admin.users.modals.extend-trial-modal />
    <livewire:admin.users.modals.apply-coupon-modal />
    <livewire:admin.users.modals.swap-plan-modal />
    <livewire:admin.users.modals.grant-credits-modal />
    <livewire:admin.users.modals.revoke-credits-modal />
</div>
