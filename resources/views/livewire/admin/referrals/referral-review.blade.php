<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Review Monthly Payouts</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Approve, edit, and process referral payouts for {{ now()->month($selectedMonth)->format('F Y') }}.
        </p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Items -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Items</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_items']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Amount</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($stats['total_amount'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending Review</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['pending_count']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approved</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($stats['approved_count']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <!-- Month Selector -->
                <div>
                    <label for="selectedMonth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Month
                    </label>
                    <select wire:model.live="selectedMonth"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @foreach($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Selector -->
                <div>
                    <label for="selectedYear" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Year
                    </label>
                    <select wire:model.live="selectedYear"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @foreach($this->getYearOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status
                    </label>
                    <select wire:model.live="statusFilter"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @foreach($this->getStatusOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Search
                    </label>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search by name or email..."
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
            </div>

            <!-- Bulk Actions -->
            @if(count($selectedItems) > 0)
                <div class="mt-4 flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-4 py-3">
                    <div class="text-sm text-blue-700 dark:text-blue-300">
                        {{ count($selectedItems) }} item(s) selected
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="confirmBulkApprove"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Approve Selected
                        </button>
                        <button wire:click="confirmBulkDeny"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Deny Selected
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Referrer Groups Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 w-12"></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Referrer
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Referrals
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Total Payout
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status Summary
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($groupedEnrollments as $enrollmentId => $group)
                        @php
                            $isExpanded = in_array($enrollmentId, $expandedEnrollments);
                        @endphp

                        <!-- Referrer Summary Row -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                            wire:key="enrollment-{{ $enrollmentId }}"
                            wire:click="toggleEnrollment({{ $enrollmentId }})">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <svg class="w-5 h-5 text-gray-400 transition-transform {{ $isExpanded ? 'transform rotate-90' : '' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ $group['enrollment']->user->initials() }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $group['enrollment']->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $group['enrollment']->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $group['total_items'] }} {{ Str::plural('referral', $group['total_items']) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    ${{ number_format($group['total_amount'], 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    @if($group['pending_items'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                            {{ $group['pending_items'] }} Pending
                                        </span>
                                    @endif
                                    @if($group['approved_items'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            {{ $group['approved_items'] }} Approved
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4" wire:click.stop>
                                @if($group['pending_items'] > 0)
                                    <div class="flex gap-2">
                                        <button wire:click="confirmApproveAllForEnrollment({{ $enrollmentId }})"
                                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Approve All
                                        </button>
                                        <button wire:click="confirmDenyAllForEnrollment({{ $enrollmentId }})"
                                                class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Deny All
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>

                        <!-- Expanded Individual Payout Items -->
                        @if($isExpanded)
                            @foreach($group['items'] as $item)
                                <tr class="bg-gray-50 dark:bg-gray-900"
                                    wire:key="payout-item-{{ $item->id }}">
                                    <td class="px-6 py-3"></td>
                                    <td class="px-6 py-3" colspan="5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-6 flex-1">
                                                <!-- Checkbox -->
                                                <input type="checkbox"
                                                       wire:model.live="selectedItems"
                                                       value="{{ $item->id }}"
                                                       class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                                                       wire:click.stop>

                                                <!-- Referred User -->
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $item->referral->referred->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $item->referral->referred->email }}
                                                    </div>
                                                </div>

                                                <!-- Subscription -->
                                                <div class="text-right">
                                                    <div class="text-sm text-gray-900 dark:text-white">
                                                        ${{ number_format($item->subscription_amount, 2) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Subscription
                                                    </div>
                                                </div>

                                                <!-- Percentage -->
                                                <div class="text-right">
                                                    <div class="text-sm text-gray-900 dark:text-white">
                                                        {{ $item->referral_percentage }}%
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Commission
                                                    </div>
                                                </div>

                                                <!-- Payout Amount -->
                                                <div class="text-right">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        ${{ number_format($item->amount, 2) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Payout
                                                    </div>
                                                </div>

                                                <!-- Status -->
                                                <div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        bg-{{ $item->status->color() }}-100 text-{{ $item->status->color() }}-800
                                                        dark:bg-{{ $item->status->color() }}-900/20 dark:text-{{ $item->status->color() }}-400">
                                                        {{ $item->status->label() }}
                                                    </span>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center space-x-2">
                                                    @if($item->status === \App\Enums\PayoutStatus::DRAFT || $item->status === \App\Enums\PayoutStatus::PENDING)
                                                        <button wire:click.stop="confirmApproveItem({{ $item->id }})"
                                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 text-sm">
                                                            Approve
                                                        </button>
                                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                                        <button wire:click.stop="confirmDenyItem({{ $item->id }})"
                                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                            Deny
                                                        </button>
                                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                                    @endif
                                                    <button wire:click.stop="openNotesModal({{ $item->id }})"
                                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                        </svg>
                                                        Notes @if($item->notes->count() > 0)({{ $item->notes->count() }})@endif
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No payout items found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($search || $statusFilter)
                                            Try adjusting your search criteria or filters.
                                        @else
                                            No payout items for {{ now()->month($selectedMonth)->format('F Y') }}.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notes Modal -->
    @if($currentPayoutItem)
        <flux:modal name="notes-modal" wire:model="notesModalItemId" variant="slideout" class="space-y-6">
            <div>
                <flux:heading size="lg">Payout Item Notes</flux:heading>
                <flux:subheading>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $currentPayoutItem->referral->referred->name }}</div>
                        <div class="text-xs">${{ number_format($currentPayoutItem->amount, 2) }} payout</div>
                    </div>
                </flux:subheading>
            </div>

            <!-- Existing Notes -->
            @if($currentPayoutItem->notes->count() > 0)
                <div class="space-y-3">
                    <flux:subheading>Previous Notes ({{ $currentPayoutItem->notes->count() }})</flux:subheading>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($currentPayoutItem->notes->sortByDesc('created_at') as $note)
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $note->note }}</p>
                                <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">{{ $note->user->name }}</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $note->created_at->format('M j, Y \a\t g:i A') }}</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No notes yet</p>
                </div>
            @endif

            <!-- Add New Note Form -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <flux:subheading>Add New Note</flux:subheading>
                <div class="mt-3 space-y-4">
                    <flux:textarea
                        wire:model="noteText"
                        rows="4"
                        placeholder="Enter your note here..."
                        label="Note"
                    />

                    <div class="flex justify-end gap-3">
                        <flux:button variant="ghost" wire:click="closeNotesModal">
                            Cancel
                        </flux:button>
                        <flux:button variant="primary" wire:click="addNote">
                            Add Note
                        </flux:button>
                    </div>
                </div>
            </div>
        </flux:modal>
    @endif

    <!-- Confirmation Modal -->
    <flux:modal wire:model="showConfirmModal" class="space-y-6 max-w-md">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                        {{ $confirmButtonVariant === 'danger' ? 'bg-red-100 dark:bg-red-900/20' : 'bg-blue-100 dark:bg-blue-900/20' }}">
                        <svg class="w-6 h-6 {{ $confirmButtonVariant === 'danger' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <flux:heading size="lg">{{ $confirmTitle }}</flux:heading>
            </div>
            <flux:text class="text-gray-600 dark:text-gray-400">
                {{ $confirmMessage }}
            </flux:text>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <flux:button variant="ghost" wire:click="resetConfirmModal">
                Cancel
            </flux:button>
            <flux:button variant="{{ $confirmButtonVariant }}" wire:click="executeConfirmedAction">
                {{ $confirmButtonText }}
            </flux:button>
        </div>
    </flux:modal>
</div>
