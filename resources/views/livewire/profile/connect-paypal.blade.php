<div class="space-y-6">
    <flux:card>
        <flux:heading size="lg" class="mb-4">PayPal Payout Account</flux:heading>

        @if (! $enrollment)
            <flux:callout variant="warning">
                You must enroll in the referral program before connecting a PayPal account.
            </flux:callout>
        @elseif ($enrollment->hasPayPalConnected())
            {{-- Connected State --}}
            <div class="space-y-4">
                <flux:callout variant="success">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold">PayPal Account Connected</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $enrollment->paypal_email }}
                            </div>
                            @if ($enrollment->paypal_connected_at)
                                <div class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">
                                    Connected {{ $enrollment->paypal_connected_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                        @if ($enrollment->verified)
                            <flux:badge variant="success">Verified</flux:badge>
                        @endif
                    </div>
                </flux:callout>

                <div class="flex gap-3">
                    <flux:button variant="ghost" wire:click="toggleConnectForm">
                        Change Account
                    </flux:button>
                    <flux:button variant="danger" wire:click="disconnectPayPal"
                        wire:confirm="Are you sure you want to disconnect your PayPal account? You will not be able to receive payouts until you connect another account.">
                        Disconnect
                    </flux:button>
                </div>
            </div>
        @else
            {{-- Not Connected State --}}
            <div class="space-y-4">
                <flux:callout>
                    Connect your PayPal account to receive referral commission payouts directly.
                </flux:callout>

                @if (! $showConnectForm)
                    <flux:button variant="primary" wire:click="toggleConnectForm">
                        Connect PayPal Account
                    </flux:button>
                @endif
            </div>
        @endif

        @if ($showConnectForm)
            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <form wire:submit="connectPayPal" class="space-y-4">
                    <flux:field>
                        <flux:label>PayPal Email Address</flux:label>
                        <flux:input wire:model="paypalEmail" type="email" placeholder="your.email@example.com"
                            required />
                        <flux:error name="paypalEmail" />
                        <flux:description>
                            Enter the email address associated with your PayPal account. This is where you'll receive
                            payouts.
                        </flux:description>
                    </flux:field>

                    @error('enrollment')
                        <flux:callout variant="danger">
                            {{ $message }}
                        </flux:callout>
                    @enderror

                    <div class="flex gap-3">
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="connectPayPal">Connect Account</span>
                            <span wire:loading wire:target="connectPayPal">Connecting...</span>
                        </flux:button>
                        <flux:button type="button" variant="ghost" wire:click="toggleConnectForm">
                            Cancel
                        </flux:button>
                    </div>
                </form>
            </div>
        @endif
    </flux:card>

    <flux:card>
        <flux:heading size="lg" class="mb-4">About PayPal Payouts</flux:heading>
        <div class="prose dark:prose-invert max-w-none">
            <ul class="text-sm space-y-2">
                <li>Payouts are processed directly to your PayPal account</li>
                <li>Standard PayPal fees may apply to received payments</li>
                <li>Payouts are typically processed on the 15th of the month</li>
                <li>You can change your PayPal account at any time</li>
                <li>Failed payouts may result in lost referrals for the month</li>
            </ul>
        </div>
    </flux:card>
</div>
