<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <flux:heading size="xl">Audit Log</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
            Track all administrative actions performed on the platform.
        </flux:text>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <flux:card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <flux:icon name="document-text" class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Total Logs</flux:text>
                    <flux:text class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->stats['total']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <flux:icon name="calendar" class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Today</flux:text>
                    <flux:text class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->stats['today']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                        <flux:icon name="chart-bar" class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">This Week</flux:text>
                    <flux:text class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->stats['this_week']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                        <flux:icon name="sparkles" class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-4">
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Credit Grants</flux:text>
                    <flux:text class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($this->stats['credit_grants']) }}</flux:text>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Filters -->
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <flux:field>
                <flux:label>Search</flux:label>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search logs..." icon="magnifying-glass" />
            </flux:field>

            <flux:field>
                <flux:label>Action Type</flux:label>
                <flux:select wire:model.live="action">
                    <option value="">All Actions</option>
                    @foreach($this->actionOptions as $actionOption)
                        <option value="{{ $actionOption }}">{{ Str::headline(str_replace('.', ' ', $actionOption)) }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Date Range</flux:label>
                <flux:select wire:model.live="dateRange">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">Last 7 Days</option>
                    <option value="month">Last 30 Days</option>
                    <option value="quarter">Last 90 Days</option>
                </flux:select>
            </flux:field>

            <div class="flex items-end">
                <flux:button wire:click="$set('search', ''); $set('action', ''); $set('dateRange', 'all')" variant="ghost">
                    Clear Filters
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- Logs Table -->
    <flux:card>
        @if($this->logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Timestamp</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Admin</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Action</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Target</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div>
                                        <flux:text class="text-sm font-medium">{{ $log->created_at->format('M j, Y') }}</flux:text>
                                        <flux:text class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('g:i:s A') }}</flux:text>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-red-400 to-orange-400 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                            {{ $log->admin?->initials() ?? '?' }}
                                        </div>
                                        <div>
                                            <flux:text class="text-sm font-medium">{{ $log->admin?->name ?? 'Unknown' }}</flux:text>
                                            <flux:text class="text-xs text-gray-500 dark:text-gray-400">{{ $log->admin?->email ?? '' }}</flux:text>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <flux:badge color="{{ $this->getActionColor($log->action) }}" size="sm">
                                        {{ Str::headline(str_replace('.', ' ', $log->action)) }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-4">
                                    <div>
                                        <flux:text class="text-sm">{{ $log->getTargetName() ?? 'N/A' }}</flux:text>
                                        @if($log->metadata['user_name'] ?? null)
                                            <flux:text class="text-xs text-gray-500 dark:text-gray-400">
                                                User: {{ $log->metadata['user_name'] }}
                                            </flux:text>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="max-w-xs">
                                        @if($log->metadata['reason'] ?? null)
                                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 truncate" title="{{ $log->metadata['reason'] }}">
                                                {{ Str::limit($log->metadata['reason'], 50) }}
                                            </flux:text>
                                        @endif
                                        @if($log->old_values || $log->new_values)
                                            <div class="flex gap-2 mt-1">
                                                @if($log->old_values)
                                                    <flux:text class="text-xs text-gray-500">
                                                        From: {{ json_encode($log->old_values) }}
                                                    </flux:text>
                                                @endif
                                                @if($log->new_values)
                                                    <flux:text class="text-xs text-gray-500">
                                                        To: {{ json_encode($log->new_values) }}
                                                    </flux:text>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $this->logs->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <flux:icon name="document-text" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <flux:heading size="base">No Audit Logs Found</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mt-2">
                    @if($search || $action || $dateRange !== 'all')
                        No logs match your current filters. Try adjusting your search criteria.
                    @else
                        Audit logs will appear here when administrative actions are performed.
                    @endif
                </flux:text>
            </div>
        @endif
    </flux:card>
</div>
