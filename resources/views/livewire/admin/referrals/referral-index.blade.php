<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Referral Program Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Manage enrolled users, track referrals, and process payouts.
        </p>
    </div>

    <!-- Draft Payout Items Alert -->
    @if($hasDraftPayoutItems)
        <div class="mb-6 rounded-lg border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-950/30 p-4">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="font-semibold text-yellow-900 dark:text-yellow-100">
                            {{ $draftPayoutItemsCount }} {{ Str::plural('payout item', $draftPayoutItemsCount) }} pending review
                        </span>
                        <span class="text-sm text-yellow-800 dark:text-yellow-200">
                            — Review and approve payout items to process payments.
                        </span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <flux:button size="sm" href="{{ route('admin.referrals.review') }}" variant="filled">
                        Review Payouts
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <flux:card size="sm">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Total Enrolled</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ number_format($stats['total_enrolled']) }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 245.25 203.25">

    <circle cx="72.88" cy="31.9" r="31.9" transform="translate(15.52 87.03) rotate(-67.5)"/>
    <path d="M151.82,106.25h-7.71c-6.49,0-12.49,2.15-17.33,5.76,6.19,6.86,9.97,15.93,9.97,25.88v2.02c0,8.96-3.08,17.22-8.22,23.78-1.45,1.85-3.06,3.56-4.81,5.12-6.39,5.67-14.67,9.26-23.76,9.7-.61.03-1.23.05-1.86.05H23.7v-44.27c0-19.32,15.72-35.03,35.03-35.03h29.27c5.92-9.2,14.03-16.85,23.61-22.22-4.35-.96-8.86-1.48-13.5-1.48h-39.38C26.3,75.55,0,101.85,0,134.28v60.98c0,3.86,3.13,6.98,6.98,6.98h91.13c5.88,0,11.56-.83,16.95-2.35,3.1-.87,6.1-1.98,8.99-3.3,9.09-4.16,17-10.45,23.1-18.22,1.7-2.17,3.27-4.44,4.67-6.83,5.48-9.28,8.63-20.09,8.63-31.64v-2.02c0-11.55-3.15-22.36-8.63-31.64Z"/>

    <circle cx="170.36" cy="31.9" r="31.9" transform="translate(-2.93 28.76) rotate(-9.57)"/>
    <path d="M185.48,75.55h-41.37c-4.44,0-8.75.5-12.91,1.42-3.4.75-6.68,1.78-9.83,3.08-9.91,4.09-18.48,10.77-24.87,19.2-1.68,2.22-3.2,4.56-4.56,7-4.78,8.59-7.51,18.47-7.51,28.99v.57c0,13.16,3.84,25.42,10.45,35.74h3.25c7.86,0,15.05-2.88,20.59-7.64-6.58-7.52-10.58-17.35-10.58-28.1v-.57c0-8.77,3.16-16.82,8.39-23.07,1.52-1.81,3.21-3.48,5.06-4.96,6.17-4.98,14.01-7.96,22.54-7.96h41.37c19.68,0,35.68,16.01,35.68,35.68v43.62h-65.51c-5.85,8.68-13.61,15.95-22.67,21.25,5.69,1.58,11.67,2.45,17.87,2.45h87.03c3.86,0,6.98-3.13,6.98-6.98v-60.33c0-32.8-26.59-59.38-59.38-59.38Z"/>

</svg>
                </div>
            </div>
        </flux:card>

        <flux:card size="sm">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Active Referrals</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ number_format($stats['total_active_referrals']) }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </flux:card>

        <flux:card size="sm">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Pending Payouts</flux:text>
                    <flux:heading size="lg" class="mt-1">${{ number_format($stats['pending_payouts_this_month'], 2) }}</flux:heading>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </flux:card>

        <flux:card size="sm">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Total Paid Out</flux:text>
                    <flux:heading size="lg" class="mt-1">${{ number_format($stats['total_paid_out'], 2) }}</flux:heading>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Filters and Search -->
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Search -->
            <flux:field>
                <flux:label>Search Users</flux:label>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name or email..."
                />
            </flux:field>

            <!-- PayPal Status Filter -->
            <flux:field>
                <flux:label>PayPal Status</flux:label>
                <flux:select wire:model.live="paypalStatusFilter">
                    @foreach($this->getPaypalStatusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <!-- Results Count -->
            <div class="flex items-end pb-2">
                <flux:text class="text-sm">
                    Showing {{ $enrollments->count() }} of {{ $enrollments->total() }} enrollments
                </flux:text>
            </div>
        </div>
    </flux:card>

    <!-- Enrollments Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            wire:click="sortBy('user_id')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center space-x-1">
                                <span>User</span>
                                @if($sortBy === 'user_id')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col"
                            wire:click="sortBy('created_at')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center space-x-1">
                                <span>Enrolled</span>
                                @if($sortBy === 'created_at')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Current Percentage
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Active Referrals
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Lifetime Earnings
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            PayPal Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ $enrollment->user->initials() }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $enrollment->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $enrollment->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $enrollment->created_at->format('M j, Y') }}
                                <div class="text-xs">{{ $enrollment->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    {{ $enrollment->percentageHistory()->latest()->first()->new_percentage ?? 0 }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $enrollment->getActiveReferralCount() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                ${{ number_format($enrollment->getLifetimeEarnings(), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($enrollment->hasPayPalConnected())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Connected
                                    </span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $enrollment->paypal_email }}
                                    </div>
                                @elseif($enrollment->needsPayPalVerification())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                        Needs Verification
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400">
                                        Not Connected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.referrals.show', $enrollment->user) }}"
                                   class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <flux:icon name="collabconnect" class="w-12 h-12 mb-4 mx-auto" />
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No enrollments found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($enrollments->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>
