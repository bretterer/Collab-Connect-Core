<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Campaign Applications</h2>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $this->getApplicationsCount() }} total
            </span>
            @if($this->getPendingApplicationsCount() > 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                    {{ $this->getPendingApplicationsCount() }} pending
                </span>
            @endif
            <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="all">All Applications</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="contracted">Contracted</option>
                <option value="rejected">Rejected</option>
                <option value="withdrawn">Withdrawn</option>
            </select>
        </div>
    </div>

    @if($applications->count() > 0)
        <div class="space-y-4">
            @foreach($applications as $application)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium">
                                        {{ $application->user->initials() }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $application->user->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Applied {{ $application->submitted_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusBadgeClass($application->status) }}">
                                        {{ $this->getStatusLabel($application->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Influencer Profile Info -->
                            @if($application->user->influencerProfile)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Niche:</span>
                                        <span class="font-medium text-gray-900 dark:text-white ml-1">
                                            {{ $application->user->influencerProfile->primary_niche?->label() ?? 'Not specified' }}
                                        </span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Location:</span>
                                        <span class="font-medium text-gray-900 dark:text-white ml-1">
                                            {{ $application->user->influencerProfile->primary_zip_code ?? 'Not specified' }}
                                        </span>
                                    </div>
                                    <div class="text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Followers:</span>
                                        <span class="font-medium text-gray-900 dark:text-white ml-1">
                                            {{ number_format($application->user->influencerProfile->follower_count ?? 0) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Social Media Accounts -->
                            @if($application->user->socialMediaAccounts->count() > 0)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Social Media Accounts</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($application->user->socialMediaAccounts as $account)
                                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                <span class="capitalize">{{ $account->platform->value }}</span>
                                                <span class="ml-1 text-gray-500">@{{ $account->username }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Application Message -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Application Message</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $application->message }}</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            @if($application->status->value === 'pending')
                                <div class="flex items-center space-x-3">
                                    <button
                                        wire:click="updateStatus({{ $application->id }}, 'accepted')"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        Accept Application
                                    </button>
                                    <button
                                        wire:click="updateStatus({{ $application->id }}, 'rejected')"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        Reject Application
                                    </button>
                                </div>
                            @elseif($application->status->value === 'accepted')
                                <div class="flex items-center space-x-3">
                                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">
                                        ✓ Application accepted{{ $application->reviewed_at ? ' on ' . $application->reviewed_at->format('M j, Y') : '' }}
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        • Will be contracted when campaign starts
                                    </span>
                                </div>
                            @elseif($application->status->value === 'contracted')
                                <div class="flex items-center space-x-3">
                                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                                        ✓ Contracted - actively working on campaign
                                    </div>
                                </div>
                            @elseif($application->status->value === 'rejected')
                                <div class="text-sm text-red-600 dark:text-red-400 font-medium">
                                    ✗ Application rejected{{ $application->reviewed_at ? ' on ' . $application->reviewed_at->format('M j, Y') : '' }}
                                </div>
                            @elseif($application->status->value === 'withdrawn')
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    Application withdrawn by influencer
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No applications</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($statusFilter === 'all')
                    No one has applied to this campaign yet.
                @else
                    No {{ $statusFilter }} applications found.
                @endif
            </p>
        </div>
    @endif
</div>