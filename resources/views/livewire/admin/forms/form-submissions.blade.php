<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.marketing.forms.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <flux:icon.arrow-left class="w-6 h-6" />
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Form Submissions</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $form->title }}</p>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <flux:button variant="ghost" :href="route('admin.marketing.forms.edit', $form)" wire:navigate icon="pencil">
                Edit Form
            </flux:button>
            @if($submissions->count() > 0)
                <flux:button wire:click="export" icon="arrow-down-tray">
                    Export to CSV
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6">
        <flux:card>
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Submissions</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $form->submissions()->count() }}</div>
            </div>
        </flux:card>
        <flux:card>
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Form Fields</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ count($form->fields) }}</div>
            </div>
        </flux:card>
        <flux:card>
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Latest Submission</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    @if($form->submissions()->latest()->first())
                        {{ $form->submissions()->latest()->first()->created_at->diffForHumans() }}
                    @else
                        Never
                    @endif
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Search -->
    <flux:card class="mb-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search submissions..."
                    icon="magnifying-glass"
                />
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Showing {{ $submissions->count() }} of {{ $submissions->total() }} submissions
            </div>
        </div>
    </flux:card>

    <!-- Submissions Table -->
    @if($submissions->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                ID
                            </th>
                            @foreach($form->fields as $field)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ $field['label'] }}
                                </th>
                            @endforeach
                            <th scope="col"
                                wire:click="sortBy('created_at')"
                                class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex items-center gap-1">
                                    <span>Submitted</span>
                                    @if($sortBy === 'created_at')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                IP Address
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($submissions as $submission)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ $submission->id }}
                                </td>
                                @foreach($form->fields as $field)
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        @php
                                            $value = $submission->data[$field['name']] ?? '-';
                                            if (is_array($value)) {
                                                $value = implode(', ', $value);
                                            }
                                        @endphp
                                        <div class="max-w-xs truncate" title="{{ $value }}">
                                            {{ $value }}
                                        </div>
                                    </td>
                                @endforeach
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $submission->created_at->format('M d, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $submission->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $submissions->links() }}
        </div>
    @else
        <flux:card>
            <div class="text-center py-12">
                <flux:icon.inbox class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No submissions yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($search)
                        No submissions match your search criteria.
                    @else
                        This form hasn't received any submissions yet.
                    @endif
                </p>
            </div>
        </flux:card>
    @endif
</div>
