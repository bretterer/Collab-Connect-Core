<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Beta Feedback Management
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Manage feedback submitted by beta users
        </p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by subject, message, or user..."
                    class="block w-full px-3 py-2 text-base rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white dark:placeholder-gray-400"
                >
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                <select wire:model.live="selectedType" class="block w-full px-3 py-2 text-base rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white">
                    <option value="">All Types</option>
                    @foreach(\App\Enums\FeedbackType::cases() as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select wire:model.live="selectedStatus" class="block w-full px-3 py-2 text-base rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white">
                    <option value="">All Status</option>
                    <option value="unresolved">Unresolved</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Feedback Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($this->feedbacks as $feedback)
                    <tr class="{{ $feedback->resolved ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($feedback->resolved)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">Resolved</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400">Open</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feedback->type->icon() }}" />
                                </svg>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $feedback->type->label() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <div class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $feedback->subject }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ Str::limit($feedback->message, 50) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($feedback->user)
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $feedback->user->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $feedback->user->email }}
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">Anonymous</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $feedback->created_at->format('M j, Y') }}
                                <div class="text-xs">
                                    {{ $feedback->created_at->format('g:i A') }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button
                                wire:click="viewFeedback({{ $feedback->id }})"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No feedback found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $this->feedbacks->links() }}
        </div>
    </div>

    <!-- Feedback Detail Modal -->
    @if($selectedFeedback)
        <div
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" x-on:click="show = false"></div>

            <!-- Modal content -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl sm:p-6"
                >
                    <!-- Modal Header -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $selectedFeedback->type->label() }}: {{ $selectedFeedback->subject }}
                                </h3>
                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>Submitted {{ $selectedFeedback->created_at->format('M j, Y \a\t g:i A') }}</span>
                                    @if($selectedFeedback->user)
                                        <span>by {{ $selectedFeedback->user->name }}</span>
                                    @endif
                                    @if($selectedFeedback->resolved)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">Resolved</span>
                                    @endif
                                </div>
                            </div>
                            <button
                                wire:click="closeModal"
                                class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="space-y-6 max-h-96 overflow-y-auto">
                        <!-- Feedback Message -->
                        <div>
                            <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">Message</h4>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                <p class="text-gray-700 dark:text-gray-300">{{ $selectedFeedback->message }}</p>
                            </div>
                        </div>

                        <!-- Screenshot -->
                        @if($selectedFeedback->screenshot_path)
                            <div>
                                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">Screenshot</h4>
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <img
                                        src="{{ $this->getScreenshotUrl($selectedFeedback->screenshot_path) }}"
                                        alt="Feedback Screenshot"
                                        class="max-w-full h-auto rounded-lg"
                                    >
                                </div>
                            </div>
                        @endif

                        <!-- Technical Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Browser Info -->
                            @if($selectedFeedback->browser_info)
                                <div>
                                    <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">Browser Info</h4>
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-sm text-gray-700 dark:text-gray-300">
                                        <div><strong>URL:</strong> {{ $selectedFeedback->url }}</div>
                                        @if($selectedFeedback->browser_info['user_agent'] ?? null)
                                            <div class="mt-1"><strong>User Agent:</strong> {{ Str::limit($selectedFeedback->browser_info['user_agent'], 80) }}</div>
                                        @endif
                                        @if($selectedFeedback->browser_info['ip_address'] ?? null)
                                            <div class="mt-1"><strong>IP:</strong> {{ $selectedFeedback->browser_info['ip_address'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Session Data -->
                            @if($selectedFeedback->session_data)
                                <div>
                                    <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">Session Info</h4>
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-sm text-gray-700 dark:text-gray-300">
                                        @if($selectedFeedback->session_data['timestamp'] ?? null)
                                            <div><strong>Timestamp:</strong> {{ $selectedFeedback->session_data['timestamp'] }}</div>
                                        @endif
                                        @if($selectedFeedback->session_data['previous_url'] ?? null)
                                            <div class="mt-1"><strong>Previous URL:</strong> {{ Str::limit($selectedFeedback->session_data['previous_url'], 60) }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- GitHub Integration -->
                        @if($selectedFeedback->github_issue_url)
                            <div>
                                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">GitHub Issue</h4>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                    <a
                                        href="{{ $selectedFeedback->github_issue_url }}"
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center space-x-2"
                                    >
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0C5.374 0 0 5.373 0 12 0 17.302 3.438 21.8 8.207 23.387c.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>
                                        </svg>
                                        <span>Issue #{{ $selectedFeedback->github_issue_number }}</span>
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Admin Notes -->
                        <div>
                            <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">Admin Notes</h4>
                            <textarea
                                wire:model="adminNotes"
                                rows="4"
                                placeholder="Add internal notes about this feedback..."
                                class="block w-full px-3 py-3 text-base rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white dark:placeholder-gray-400 resize-vertical"
                            ></textarea>
                            <div class="mt-2">
                                <button
                                    wire:click="saveNotes"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Save Notes
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        @if($selectedFeedback->github_issue_url)
                            <button
                                onclick="window.open('{{ $selectedFeedback->github_issue_url }}', '_blank')"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                View on GitHub
                            </button>
                        @else
                            @if($this->isGitHubConfigured())
                                <button
                                    wire:click="createGitHubIssue"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <span wire:loading.remove>Create GitHub Issue</span>
                                    <span wire:loading>Creating...</span>
                                </button>
                            @else
                                <button disabled class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-100 dark:bg-gray-800 cursor-not-allowed">
                                    GitHub Not Configured
                                </button>
                            @endif
                        @endif

                        @if($selectedFeedback->resolved)
                            <button
                                wire:click="markAsUnresolved"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Mark as Unresolved
                            </button>
                        @else
                            <button
                                wire:click="markAsResolved"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Mark as Resolved
                            </button>
                        @endif

                        <button
                            wire:click="closeModal"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>