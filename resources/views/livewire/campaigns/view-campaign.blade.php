<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('discover') }}" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Back to Discover
                </a>
            </div>
        </div>
    </div>

    <!-- Campaign Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $campaignGoal }}
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">
                        {{ $campaignDescription }}
                    </p>

                    <!-- Campaign Badges -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ ucwords(str_replace('_', ' ', $campaignType)) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ ucwords(str_replace('_', ' ', $compensationType)) }}
                        </span>
                        @if($targetZipCode)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                📍 {{ $targetZipCode }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Apply Button / Owner Tools -->
                <div class="ml-6">
                    @if($isOwner)
                        <!-- Owner Tools -->
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('campaigns.edit', $campaignId) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                                Edit Campaign
                            </a>
                            <button class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Unpublish
                            </button>
                            <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                Archive
                            </button>
                        </div>
                    @else
                        <!-- Apply Button for Non-Owners -->
                        <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                            Apply Now
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Campaign Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Campaign Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Campaign Details</h2>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Campaign Type</h3>
                            <p class="text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $campaignType)) }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Compensation</h3>
                            <p class="text-gray-900 dark:text-white">
                                @if($compensationType === 'monetary')
                                    ${{ number_format($compensationAmount) }}
                                @else
                                    {{ ucwords(str_replace('_', ' ', $compensationType)) }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Influencers Needed</h3>
                            <p class="text-gray-900 dark:text-white">{{ $influencerCount }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Target Location</h3>
                            <p class="text-gray-900 dark:text-white">{{ $targetZipCode ?? 'Anywhere' }}</p>
                        </div>

                        @if($applicationDeadline)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Application Deadline</h3>
                                <p class="text-gray-900 dark:text-white {{ \Carbon\Carbon::parse($applicationDeadline)->isPast() ? 'text-red-600' : '' }}">
                                    {{ \Carbon\Carbon::parse($applicationDeadline)->format('M j, Y') }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Campaign Duration</h3>
                            <p class="text-gray-900 dark:text-white">{{ $campaignCompletionDate ? \Carbon\Carbon::parse($campaignCompletionDate)->format('M j, Y') : 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Requirements -->
            @if($additionalRequirements)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Requirements</h2>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($additionalRequirements)) !!}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Social Requirements -->
            @if(!empty($socialRequirements))
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Social Media Requirements</h2>
                        <div class="flex flex-wrap gap-2">
                            @if(is_array($socialRequirements))
                                @foreach($socialRequirements as $requirement)
                                    @if(is_string($requirement))
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucwords(str_replace('_', ' ', $requirement)) }}
                                        </span>
                                    @elseif(is_array($requirement))
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ implode(', ', $requirement) }}
                                        </span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-gray-500 dark:text-gray-400">No specific requirements</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

                <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        @if($isOwner)
                            Campaign Management
                        @else
                            Quick Actions
                        @endif
                    </h2>

                    <div class="space-y-3">
                        @if($isOwner)
                            <!-- Owner Actions -->
                            <a href="{{ route('campaigns.edit', $campaignId) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 text-center block">
                                Edit Campaign
                            </a>
                            <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Unpublish Campaign
                            </button>
                            <button class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Archive Campaign
                            </button>
                            <a href="{{ route('dashboard') }}" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-200 text-center block">
                                Back to Dashboard
                            </a>
                        @else
                            <!-- Non-Owner Actions -->
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Apply to Campaign
                            </button>
                            <a href="{{ route('discover') }}" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-200 text-center block">
                                Back to Discover
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Campaign Info -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Campaign Info</h2>

                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                            <p class="text-gray-900 dark:text-white">Published</p>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Created:</span>
                            <p class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::now()->format('M j, Y') }}</p>
                        </div>

                        @if($compensationDescription)
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Compensation Details:</span>
                                <p class="text-gray-900 dark:text-white">{{ $compensationDescription }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>