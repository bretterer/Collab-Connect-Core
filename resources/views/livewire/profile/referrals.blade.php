<div>
{{-- Business Member - Owner Not Enrolled State --}}
@if(!$isBusinessOwner && $isEligible && !$isEnrolled)
<div class="max-w-4xl mx-auto px-4 py-8">
    <flux:heading size="xl" class="mb-6">Referral Program</flux:heading>

    <flux:card class="p-8">
        <div class="flex flex-col items-center text-center space-y-6">
            <div class="w-20 h-20 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                <flux:icon.lock-closed class="w-10 h-10 text-amber-600 dark:text-amber-500" />
            </div>

            <div class="space-y-3">
                <flux:heading size="lg">Referral Program Available</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400 max-w-2xl">
                    Your business is eligible for the referral program, but it hasn't been set up yet by the business owner.
                </flux:text>
            </div>

            <flux:separator class="w-full max-w-2xl" />

            <div class="w-full max-w-2xl space-y-4">
                <flux:heading size="sm">What is the Referral Program?</flux:heading>
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                    Our referral program allows your business to earn 10% recurring commission on subscription payments from referred users. Once enrolled, you and your team can share the referral link to start earning.
                </flux:text>
            </div>

            <flux:callout variant="warning" class="w-full max-w-2xl text-left">
                <flux:text class="text-sm">
                    <strong>Action Required:</strong> Please contact your business owner to enroll in the referral program. Once enrolled, you'll be able to share the referral link and track earnings together.
                </flux:text>
            </flux:callout>

            <div class="flex flex-col gap-3 w-full max-w-2xl">
                <div class="flex items-start gap-3 text-left">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500 flex-shrink-0 mt-0.5" />
                    <div>
                        <flux:text class="text-sm font-medium">Earn 10% Commission</flux:text>
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                            Recurring revenue from every referral
                        </flux:text>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-left">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500 flex-shrink-0 mt-0.5" />
                    <div>
                        <flux:text class="text-sm font-medium">No Limits</flux:text>
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                            Unlimited referrals and earning potential
                        </flux:text>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-left">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500 flex-shrink-0 mt-0.5" />
                    <div>
                        <flux:text class="text-sm font-medium">Monthly Payouts</flux:text>
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                            Automatic payments once balance reaches $25
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>
    </flux:card>
</div>

{{-- Business Member State (Non-Owner) --}}
@elseif(!$isBusinessOwner)
<div class="max-w-4xl mx-auto px-4 py-8">
    <flux:heading size="xl" class="mb-6">Referral Program</flux:heading>

    <flux:card class="p-8">
        <div class="flex flex-col items-center text-center space-y-6">
            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                <flux:icon.link class="w-10 h-10 text-blue-600 dark:text-blue-500" />
            </div>

            <div class="space-y-3">
                <flux:heading size="lg">Business Referral Code</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400 max-w-2xl">
                    Share your business's referral link to help grow the team. All referral earnings go to the business owner.
                </flux:text>
            </div>

            <flux:separator class="w-full max-w-2xl" />

            <div class="w-full max-w-2xl space-y-4">
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                    Business Referral Link
                </flux:text>

                <div class="flex gap-3" x-data="{ copied: false }">
                    <flux:input
                        readonly
                        value="{{ $referralLink }}"
                        class="flex-1 font-mono text-sm"
                        x-ref="referralInput"
                    />
                    <flux:button
                        variant="primary"
                        @click="
                            if (navigator.clipboard) {
                                navigator.clipboard.writeText('{{ $referralLink }}').then(() => {
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                });
                            } else {
                                $refs.referralInput.select();
                                document.execCommand('copy');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            }
                        "
                        class="flex-shrink-0"
                    >
                        <flux:icon.clipboard class="w-4 h-4" />
                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                    </flux:button>
                </div>
            </div>

            <flux:callout variant="info" class="w-full max-w-2xl text-left">
                <flux:text class="text-sm">
                    <strong>Note:</strong> As a business team member, you can share this referral link to help your business grow. All referral rewards and earnings are credited to the business owner's account. Contact your business owner for more details about the referral program benefits.
                </flux:text>
            </flux:callout>
        </div>
    </flux:card>
</div>

{{-- Not Eligible State --}}
@elseif(!$isEligible)
<div class="max-w-4xl mx-auto px-4 py-8">
    <flux:heading size="xl" class="mb-6">Referral Program</flux:heading>

    <flux:card class="p-8">
        <div class="flex flex-col items-center text-center space-y-6">
            <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                <flux:icon.link class="w-10 h-10 text-zinc-400" />
            </div>

            <div class="space-y-3">
                <flux:heading size="lg">Join Our Referral Program</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400 max-w-2xl">
                    You're not quite eligible yet. To join our referral program, you need to be an active subscriber with at least one completed payment.
                </flux:text>
            </div>

            <flux:separator class="w-full max-w-md" />

            <div class="space-y-4 w-full max-w-md">
                <flux:heading size="sm" class="text-left">Requirements:</flux:heading>
                <ul class="space-y-3 text-left">
                    <li class="flex items-start gap-3">
                        <flux:icon.check-circle class="w-5 h-5 text-zinc-400 flex-shrink-0 mt-0.5" />
                        <flux:text class="text-sm">Active subscription (Business or Influencer plan)</flux:text>
                    </li>
                    <li class="flex items-start gap-3">
                        <flux:icon.check-circle class="w-5 h-5 text-zinc-400 flex-shrink-0 mt-0.5" />
                        <flux:text class="text-sm">At least one completed subscription payment</flux:text>
                    </li>
                    <li class="flex items-start gap-3">
                        <flux:icon.check-circle class="w-5 h-5 text-zinc-400 flex-shrink-0 mt-0.5" />
                        <flux:text class="text-sm">Account in good standing</flux:text>
                    </li>
                </ul>
            </div>

            <flux:button variant="primary" href="{{ route('billing') }}" wire:navigate class="mt-4">
                View Subscription Options
            </flux:button>
        </div>
    </flux:card>
</div>

{{-- Enrollment State --}}
@elseif(!$isEnrolled)
<div class="max-w-4xl mx-auto px-4 py-8">
    <flux:heading size="xl" class="mb-6">Referral Program</flux:heading>

    <flux:card class="p-8">
        <div class="flex flex-col items-center text-center space-y-6">
            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                <flux:icon.link class="w-10 h-10 text-green-600 dark:text-green-500" />
            </div>

            <div class="space-y-3">
                <flux:heading size="lg">You're Eligible!</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400 max-w-2xl">
                    Great news! You qualify for our referral program. Earn 10% of every subscription payment from people you refer.
                </flux:text>
            </div>

            <flux:separator class="w-full max-w-2xl" />

            <div class="grid md:grid-cols-3 gap-6 w-full max-w-3xl">
                <div class="space-y-3">
                    <div class="w-16 h-16 mx-auto bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                        <flux:icon.link class="w-8 h-8 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <flux:heading size="sm">Share Your Link</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                        Share your unique referral link with friends and colleagues
                    </flux:text>
                </div>

                <div class="space-y-3">
                    <div class="w-16 h-16 mx-auto bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                        <flux:icon.user-plus class="w-8 h-8 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <flux:heading size="sm">They Subscribe</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                        When they sign up and complete their first payment
                    </flux:text>
                </div>

                <div class="space-y-3">
                    <div class="w-16 h-16 mx-auto bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                        <flux:icon.currency-dollar class="w-8 h-8 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <flux:heading size="sm">You Earn 10%</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                        Earn 10% of their subscription payments for as long as you both remain active subscribers
                    </flux:text>
                </div>
            </div>

            <flux:callout variant="info" class="w-full max-w-2xl text-left">
                <flux:text class="text-sm">
                    <strong>How it works:</strong> You'll receive 10% of each monthly or annual subscription payment made by anyone who signs up using your referral link. Payouts begin after their first completed payment and continue as long as you both remain active subscribers.
                </flux:text>
            </flux:callout>

            <flux:button variant="primary" wire:click="enrollInProgram" class="mt-4">
                Enroll in Referral Program
            </flux:button>
        </div>
    </flux:card>
</div>

{{-- Active Referral Dashboard --}}
@else
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">Referral Program</flux:heading>
        <flux:badge variant="success">Active</flux:badge>
    </div>

    {{-- PayPal Payout Account Card --}}
    <flux:card class="p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">Payout Account</flux:heading>
            @if($enrollment && $enrollment->hasPayPalConnected())
                <flux:badge variant="success">Connected</flux:badge>
            @else
                <flux:badge variant="warning">Not Connected</flux:badge>
            @endif
        </div>

        @if($enrollment && $enrollment->hasPayPalConnected())
            {{-- Connected PayPal Account --}}
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-4 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <div class="flex items-start gap-3 flex-1">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <flux:icon.paypal class="w-6 h-6 text-blue-600 dark:text-blue-500" />
                        </div>
                        <div class="flex-1">
                            <flux:text class="font-medium">PayPal Account</flux:text>
                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                {{ $enrollment->paypal_email }}
                            </flux:text>
                            @if($enrollment->paypal_connected_at)
                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">
                                    Connected {{ $enrollment->paypal_connected_at->diffForHumans() }}
                                </flux:text>
                            @endif
                        </div>
                    </div>
                    <flux:button variant="ghost" size="sm" wire:click="$set('showPayPalStep', true)">
                        Change
                    </flux:button>
                </div>

                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                    All referral commission payouts will be sent to this PayPal account. Paypal fees may apply.
                </flux:text>
            </div>
        @else
            {{-- Not Connected State --}}
            <div class="space-y-4">
                <flux:callout variant="warning">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <flux:text class="font-semibold mb-1">Connect Your PayPal Account</flux:text>
                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                                You need to connect a PayPal account to receive your referral commission payouts. Set this up now to ensure you don't miss any payments.
                            </flux:text>
                        </div>
                    </div>
                </flux:callout>

                <flux:button variant="primary" wire:click="$set('showPayPalStep', true)">
                    Connect PayPal Account
                </flux:button>
            </div>
        @endif
    </flux:card>

    {{-- Referral Link Card --}}
    <flux:card class="p-6 mb-6">
        <flux:heading size="lg" class="mb-4">Your Referral Link</flux:heading>
        <flux:text class="text-zinc-600 dark:text-zinc-400 mb-4">
            Share this link with friends and colleagues to start earning. You'll receive 10% of their subscription payments.
        </flux:text>

        <div class="flex gap-3" x-data="{ copied: false }">
            <flux:input
                readonly
                value="{{ $referralLink }}"
                class="flex-1 font-mono text-sm"
                x-ref="referralInput"
            />
            <flux:button
                variant="primary"
                @click="
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText('{{ $referralLink }}').then(() => {
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        });
                    } else {
                        $refs.referralInput.select();
                        document.execCommand('copy');
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    }
                "
                class="flex-shrink-0"
                icon="clipboard"
            >
                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
            </flux:button>
        </div>
    </flux:card>

    {{-- Stats Overview --}}
    <div class="grid md:grid-cols-3 gap-6 mb-6">
        {{-- Pending Referrals --}}
        <flux:card class="p-6">
            <div class="flex items-start justify-between mb-3">
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Pending Referrals</flux:text>
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.clock class="w-5 h-5 text-amber-600 dark:text-amber-500" />
                </div>
            </div>
            <flux:heading size="xl" class="mb-1">{{ $stats['pending_count'] }}</flux:heading>
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                Awaiting first payment
            </flux:text>
        </flux:card>

        {{-- Active Referrals --}}
        <flux:card class="p-6">
            <div class="flex items-start justify-between mb-3">
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Active Referrals</flux:text>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500" />
                </div>
            </div>
            <flux:heading size="xl" class="mb-1">{{ $stats['active_count'] }}</flux:heading>
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                Currently earning from
            </flux:text>
        </flux:card>

        {{-- Lifetime Referrals --}}
        <flux:card class="p-6">
            <div class="flex items-start justify-between mb-3">
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Total Referrals</flux:text>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.users class="w-5 h-5 text-blue-600 dark:text-blue-500" />
                </div>
            </div>
            <flux:heading size="xl" class="mb-1">{{ $stats['total_count'] }}</flux:heading>
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                All time
            </flux:text>
        </flux:card>
    </div>

    {{-- Earnings Overview --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Pending Payouts --}}
        <flux:card class="p-6">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Pending Payout</flux:text>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                        Processing or awaiting threshold
                    </flux:text>
                </div>
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-500" />
                </div>
            </div>
            <flux:heading size="2xl" class="mb-1">${{ number_format($stats['pending_payout'], 2) }}</flux:heading>
            @if($stats['pending_payout'] > 0)
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                    Payouts are processed monthly for balances over $25
                </flux:text>
            @endif
        </flux:card>

        {{-- Lifetime Earnings --}}
        <flux:card class="p-6">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Lifetime Earnings</flux:text>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                        Total earnings from referrals
                    </flux:text>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <flux:icon.currency-dollar class="w-6 h-6 text-green-600 dark:text-green-500" />
                </div>
            </div>
            <flux:heading size="2xl" class="mb-1">${{ number_format($stats['lifetime_earnings'], 2) }}</flux:heading>
            @if($stats['lifetime_earnings'] > 0)
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-500">
                    Thank you for spreading the word!
                </flux:text>
            @endif
        </flux:card>
    </div>

    {{-- Program Details --}}
    <flux:card class="p-6">
        <flux:heading size="lg" class="mb-4">Program Details</flux:heading>

        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500 flex-shrink-0 mt-0.5" />
                <div>
                    <flux:text class="font-medium mb-1">Earn 10% Commission</flux:text>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                        Receive 10% of every subscription payment made by your referrals
                    </flux:text>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500 flex-shrink-0 mt-0.5" />
                <div>
                    <flux:text class="font-medium mb-1">Recurring Earnings</flux:text>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                        Continue earning as long as your referrals remain active subscribers
                    </flux:text>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500 flex-shrink-0 mt-0.5" />
                <div>
                    <flux:text class="font-medium mb-1">Monthly Payouts</flux:text>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                        Payouts are processed monthly once your balance reaches $25 or more
                    </flux:text>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-500 flex-shrink-0 mt-0.5" />
                <div>
                    <flux:text class="font-medium mb-1">No Limits</flux:text>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                        There's no cap on how many people you can refer or how much you can earn
                    </flux:text>
                </div>
            </div>
        </div>

        <flux:separator class="my-6" />

        <flux:callout variant="info">
            <flux:text class="text-sm">
                <strong>Note:</strong> Earnings begin after your referral's first successful subscription payment. They must remain an active subscriber in good standing for you to continue receiving commission payments.
            </flux:text>
        </flux:callout>
    </flux:card>
</div>
@endif

{{-- PayPal Connection Modal --}}
<flux:modal name="paypal-setup" variant="flyout" wire:model="showPayPalStep" class="space-y-6">
    <div>
        <flux:heading size="lg">Connect Your PayPal Account</flux:heading>
        <flux:text class="text-zinc-600 dark:text-zinc-400 mt-2">
            Connect your PayPal account to receive commission payouts from your referrals.
        </flux:text>
    </div>

    <flux:separator />

    {{-- Include the PayPal connection component --}}
    @if($enrollment)
        @livewire('profile.connect-paypal', key('paypal-connect-'.$enrollment->id))
    @else
        <flux:callout variant="warning">
            Unable to load PayPal connection. Please refresh the page.
        </flux:callout>
    @endif

    <flux:separator />

    <div class="flex justify-between gap-3">
        <flux:button variant="ghost" wire:click="skipPayPalSetup">
            Skip for Now
        </flux:button>
        <flux:button variant="primary" wire:click="$set('showPayPalStep', false)">
            Done
        </flux:button>
    </div>
</flux:modal>
</div>
