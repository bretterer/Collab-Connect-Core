<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Campaign Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Monitor and manage all campaigns across the platform. Track performance and moderate content.
        </p>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Search Campaigns
                    </label>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search by goal, description, or business..."
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Campaign Status
                    </label>
                    <select wire:model.live="statusFilter"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @foreach($this->getStatusOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Results Count -->
                <div class="flex items-end">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Showing {{ $campaigns->count() }} of {{ $campaigns->total() }} campaigns
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            wire:click="sortBy('campaign_goal')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center space-x-1">
                                <span>Campaign Goal</span>
                                @if($sortBy === 'campaign_goal')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Business
                        </th>
                        <th scope="col"
                            wire:click="sortBy('status')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center space-x-1">
                                <span>Status</span>
                                @if($sortBy === 'status')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Applications
                        </th>
                        <th scope="col"
                            wire:click="sortBy('created_at')"
                            class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center space-x-1">
                                <span>Created</span>
                                @if($sortBy === 'created_at')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($campaigns as $campaign)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ Str::limit($campaign->campaign_goal, 60) }}
                                </div>
                                @if($campaign->campaign_description)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($campaign->campaign_description, 80) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                        {{ $campaign->business->owner->first()->initials() }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $campaign->business->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $campaign->business->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $campaign->status === \App\Enums\CampaignStatus::PUBLISHED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                                       ($campaign->status === \App\Enums\CampaignStatus::DRAFT ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                                        ($campaign->status === \App\Enums\CampaignStatus::SCHEDULED ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
                                         ($campaign->status === \App\Enums\CampaignStatus::ARCHIVED ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' :
                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'))) }}">
                                    {{ $campaign->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $campaign->applications_count ?? $campaign->applications->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $campaign->created_at->format('M j, Y') }}
                                <div class="text-xs">{{ $campaign->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.campaigns.show', $campaign) }}"
                                       class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        View
                                    </a>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <a href="{{ route('admin.campaigns.edit', $campaign) }}"
                                       class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No campaigns found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($campaigns->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
