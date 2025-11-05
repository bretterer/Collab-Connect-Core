<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Referral Program Details</p>
            </div>
            <flux:button href="{{ route('admin.referrals.index') }}" variant="ghost">
                Back to List
            </flux:button>
        </div>
    </div>

    @if($user->referralEnrollment)
        <div class="grid grid-cols-1 gap-6">
            <flux:card>
                <flux:heading size="lg">Enrollment Information</flux:heading>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">Referral Code</flux:text>
                        <flux:text class="font-mono">{{ $user->referralEnrollment->code }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">Current Percentage</flux:text>
                        <flux:text>{{ $user->referralEnrollment->current_percentage }}%</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">Active Referrals</flux:text>
                        <flux:text>{{ $user->referralEnrollment->getActiveReferralCount() }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">Lifetime Earnings</flux:text>
                        <flux:text>${{ number_format($user->referralEnrollment->getLifetimeEarnings(), 2) }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Placeholder: Full Detail View Coming Soon</flux:heading>
                <flux:text class="mt-2">
                    This page will include:
                    - Custom percentage management modal
                    - Percentage history table
                    - Referred users table with status
                    - Payout history
                </flux:text>
            </flux:card>
        </div>
    @else
        <flux:callout variant="warning">
            This user is not enrolled in the referral program.
        </flux:callout>
    @endif
</div>
