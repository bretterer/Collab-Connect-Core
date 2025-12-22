<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payout Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Review, filter, and retrigger affiliate payouts.
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <flux:card size="sm">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Total Payouts</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ number_format($stats['total_payouts']) }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </flux:card>

        <flux:card size="sm">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Pending Amount</flux:text>
                    <flux:heading size="lg" class="mt-1">${{ number_format($stats['pending_amount'], 2) }}</flux:heading>
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
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Failed Payouts</flux:text>
                    <flux:heading size="lg" class="mt-1">{{ number_format($stats['failed_count']) }}</flux:heading>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </flux:card>

        <flux:card size="sm">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Total Paid</flux:text>
                    <flux:heading size="lg" class="mt-1">${{ number_format($stats['paid_amount'], 2) }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Filters and Search -->
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            <!-- Search -->
            <flux:field>
                <flux:label>Search Users</flux:label>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name or email..."
                />
            </flux:field>

            <!-- Status Filter -->
            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="statusFilter">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <!-- Month Filter -->
            <flux:field>
                <flux:label>Month</flux:label>
                <flux:select wire:model.live="monthFilter">
                    @foreach($monthOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <!-- Results Count -->
            <div class="flex items-end pb-2">
                <flux:text class="text-sm">
                    Showing {{ $payouts->count() }} of {{ $payouts->total() }} payouts
                </flux:text>
            </div>
        </div>
    </flux:card>

    <!-- Selected Actions -->
    @if(!empty($selectedPayouts))
        <div class="mb-6 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/30 p-4">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="font-semibold text-blue-900 dark:text-blue-100">
                            {{ count($selectedPayouts) }} {{ Str::plural('payout', count($selectedPayouts)) }} selected
                        </span>
                    </div>
                </div>
                <div class="flex-shrink-0 flex gap-2">
                    <flux:button
                        size="sm"
                        variant="ghost"
                        wire:click="$set('selectedPayouts', [])"
                    >
                        Clear Selection
                    </flux:button>
                    <flux:button
                        size="sm"
                        variant="filled"
                        wire:click="retriggerPayouts"
                        wire:confirm="Are you sure you want to retrigger {{ count($selectedPayouts) }} payout(s)? This will create a new batch payout via PayPal."
                    >
                        Retrigger Payouts
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    <!-- Payouts Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700"
                            />
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Period
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Referrals
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            PayPal Batch ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Processed
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payout->status !== App\Enums\PayoutStatus::PAID)
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedPayouts"
                                        value="{{ $payout->id }}"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700"
                                    />
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ $payout->enrollment->user->initials() }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $payout->enrollment->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $payout->enrollment->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $payout->month_year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                ${{ number_format($payout->amount, 2) }}
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payout->currency }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $payout->referral_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge :color="$payout->status->color()">
                                    {{ $payout->status->label() }}
                                </flux:badge>
                                @if($payout->failure_reason)
                                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                                        {{ Str::limit($payout->failure_reason, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($payout->paypal_batch_id)
                                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                        {{ Str::limit($payout->paypal_batch_id, 20) }}
                                    </code>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($payout->processed_at)
                                    {{ $payout->processed_at->format('M j, Y') }}
                                    <div class="text-xs">{{ $payout->processed_at->diffForHumans() }}</div>
                                @elseif($payout->paid_at)
                                    {{ $payout->paid_at->format('M j, Y') }}
                                    <div class="text-xs">{{ $payout->paid_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mb-4 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No payouts found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payouts->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $payouts->links() }}
            </div>
        @endif
    </div>
</div>
