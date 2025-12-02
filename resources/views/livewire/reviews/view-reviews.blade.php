<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                Collaboration Reviews
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Campaign: {{ $collaboration->campaign->project_name ?? $collaboration->campaign->campaign_goal }}
            </p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Review Status Badge -->
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($collaboration->review_status->value === 'closed')
                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                    @else
                        bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300
                    @endif
                ">
                    {{ $collaboration->review_status->label() }}
                </span>
                <a href="{{ route('dashboard') }}" wire:navigate class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    &larr; Back to Dashboard
                </a>
            </div>

            <!-- Business Review -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium">
                            {{ $businessOwner?->initials() ?? 'B' }}
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $businessOwner?->name ?? 'Business Owner' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Business Owner</p>
                        </div>
                    </div>
                    @if($businessReview)
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessReview->submitted_at->format('M j, Y') }}
                        </span>
                    @endif
                </div>

                @if($businessReview)
                    <!-- Star Rating -->
                    <div class="flex items-center mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-5 w-5 {{ $businessReview->rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $businessReview->rating }}/5</span>
                    </div>

                    @if($businessReview->comment)
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $businessReview->comment }}</p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No written review provided.</p>
                    @endif
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500 dark:text-gray-400 italic">No review submitted</p>
                    </div>
                @endif
            </div>

            <!-- Influencer Review -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-purple-600 flex items-center justify-center text-white font-medium">
                            {{ $collaboration->influencer->initials() }}
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $collaboration->influencer->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Influencer</p>
                        </div>
                    </div>
                    @if($influencerReview)
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $influencerReview->submitted_at->format('M j, Y') }}
                        </span>
                    @endif
                </div>

                @if($influencerReview)
                    <!-- Star Rating -->
                    <div class="flex items-center mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-5 w-5 {{ $influencerReview->rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $influencerReview->rating }}/5</span>
                    </div>

                    @if($influencerReview->comment)
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $influencerReview->comment }}</p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No written review provided.</p>
                    @endif
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500 dark:text-gray-400 italic">No review submitted</p>
                    </div>
                @endif
            </div>

            <!-- Info about expired review period -->
            @if($collaboration->review_status->value === 'expired')
                <div class="rounded-md bg-amber-50 dark:bg-amber-900/20 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700 dark:text-amber-300">
                                The 15-day review period has ended. Any missing reviews can no longer be submitted.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
