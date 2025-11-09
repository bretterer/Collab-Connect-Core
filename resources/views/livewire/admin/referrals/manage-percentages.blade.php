<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Referral Percentages</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Update and manage referral commission percentages for enrolled referrers.
        </p>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="max-w-md">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Search Referrers
                </label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by name or email..."
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            </div>
        </div>
    </div>

    <!-- Enrollments Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Referrer
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Current %
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Active Referrals
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Last Updated
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $enrollment->currentReferralPercentage() }}%
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $enrollment->getActiveReferralCount() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($enrollment->percentageHistory->first())
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $enrollment->percentageHistory->first()->created_at->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $enrollment->percentageHistory->first()->created_at->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Never</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <button wire:click="openEditModal({{ $enrollment->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Edit %
                                    </button>
                                    <button wire:click="openHistoryModal({{ $enrollment->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        History
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No referrers found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($search)
                                            Try adjusting your search criteria.
                                        @else
                                            No enrolled referrers yet.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($enrollments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>

    <!-- Edit Percentage Modal -->
    @if($currentEnrollment)
        <flux:modal name="edit-percentage-modal" wire:model="editingEnrollmentId" variant="slideout" class="space-y-6">
            <div>
                <flux:heading size="lg">Update Referral Percentage</flux:heading>
                <flux:subheading>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $currentEnrollment->user->name }}</div>
                        <div class="text-xs">Current: NaN%</div>
                    </div>
                </flux:subheading>
            </div>

            <form wire:submit="updatePercentage" class="space-y-6">
                <!-- New Percentage -->
                <div>
                    <flux:input
                        wire:model="newPercentage"
                        type="number"
                        min="0"
                        max="100"
                        label="New Percentage"
                        placeholder="Enter percentage (0-100)"
                    />
                    @error('newPercentage')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Change Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Change Type
                    </label>
                    <select wire:model.live="changeType"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @foreach($this->getChangeTypeOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('changeType')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conditional Fields Based on Change Type -->
                @if($changeType === \App\Enums\PercentageChangeType::TEMPORARY_DATE->value)
                    <div>
                        <flux:input
                            wire:model="expiresAt"
                            type="date"
                            label="Expires On"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                        />
                        @error('expiresAt')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif($changeType === \App\Enums\PercentageChangeType::TEMPORARY_MONTHS->value)
                    <div>
                        <flux:input
                            wire:model="monthsRemaining"
                            type="number"
                            min="1"
                            max="120"
                            label="Number of Months"
                            placeholder="Enter number of months (1-120)"
                        />
                        @error('monthsRemaining')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Reason -->
                <div>
                    <flux:textarea
                        wire:model="reason"
                        rows="3"
                        label="Reason for Change"
                        placeholder="Enter the reason for this percentage change..."
                    />
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <flux:button variant="ghost" wire:click="closeEditModal" type="button">
                        Cancel
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        Update Percentage
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif

    <!-- History Modal -->
    @if($historyEnrollment)
        <flux:modal name="history-modal" wire:model="viewingHistoryEnrollmentId" variant="slideout" class="space-y-6">
            <div>
                <flux:heading size="lg">Percentage Change History</flux:heading>
                <flux:subheading>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $historyEnrollment->user->name }}</div>
                        <div class="text-xs">{{ $historyEnrollment->user->email }}</div>
                    </div>
                </flux:subheading>
            </div>

            @if($historyEnrollment->percentageHistory->count() > 0)
                <div class="space-y-4">
                    @foreach($historyEnrollment->percentageHistory->sortByDesc('created_at') as $history)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $history->old_percentage }}% → {{ $history->new_percentage }}%
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            {{ $history->change_type->label() }}
                                        </span>
                                    </div>

                                    @if($history->reason)
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $history->reason }}
                                        </p>
                                    @endif

                                    @if($history->change_type === \App\Enums\PercentageChangeType::TEMPORARY_DATE && $history->expires_at)
                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Expires: {{ $history->expires_at->format('M j, Y') }}
                                            @if($history->isExpired())
                                                <span class="text-red-600 dark:text-red-400">(Expired)</span>
                                            @endif
                                        </div>
                                    @elseif($history->change_type === \App\Enums\PercentageChangeType::TEMPORARY_MONTHS && $history->months_remaining)
                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Duration: {{ $history->months_remaining }} months
                                        </div>
                                    @endif

                                    <div class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        @if($history->changedBy)
                                            <span class="font-medium">{{ $history->changedBy->name }}</span>
                                            <span class="mx-1">•</span>
                                        @endif
                                        <span>{{ $history->created_at->format('M j, Y \a\t g:i A') }}</span>
                                        <span class="mx-1">•</span>
                                        <span>{{ $history->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No history available</p>
                </div>
            @endif

            <div class="flex justify-end">
                <flux:button variant="ghost" wire:click="closeHistoryModal">
                    Close
                </flux:button>
            </div>
        </flux:modal>
    @endif
</div>
