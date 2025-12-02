<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                Review Your Collaboration
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Campaign: {{ $collaboration->campaign->project_name ?? $collaboration->campaign->campaign_goal }}
            </p>
        </div>

        <div class="p-6">
            @if($hasSubmitted)
                <!-- Already submitted state -->
                <div class="text-center py-8">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Review Submitted</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Thank you for submitting your review. It will be visible once {{ $reviewee->name }} submits their review, or when the review period ends.
                    </p>

                    @if($daysRemaining !== null)
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">{{ $daysRemaining }}</span> day{{ $daysRemaining !== 1 ? 's' : '' }} remaining in review period
                        </p>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            &larr; Back to Dashboard
                        </a>
                    </div>
                </div>
            @else
                <!-- Review form -->
                <div class="space-y-6">
                    <!-- Reviewee info -->
                    <div class="flex items-center space-x-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium text-lg">
                                {{ $reviewee->initials() }}
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $reviewee->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Share your experience working with this {{ $collaboration->influencer_id === auth()->id() ? 'business' : 'influencer' }}
                            </p>
                        </div>
                    </div>

                    <!-- Time remaining notice -->
                    @if($daysRemaining !== null)
                        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <span class="font-medium">{{ $daysRemaining }}</span> day{{ $daysRemaining !== 1 ? 's' : '' }} remaining to submit your review.
                                        Reviews will be visible once both parties have submitted, or when the review period ends.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form wire:submit="submitReview" class="space-y-6">
                        <!-- Star Rating -->
                        <div x-data="{ hoverRating: 0 }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rating <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-1" @mouseleave="hoverRating = 0">
                                @for($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="setRating({{ $i }})"
                                        @mouseenter="hoverRating = {{ $i }}"
                                        class="focus:outline-none transition-colors duration-150"
                                    >
                                        <svg
                                            class="h-10 w-10 transition-colors duration-150"
                                            :class="(hoverRating >= {{ $i }} || (hoverRating === 0 && {{ $rating }} >= {{ $i }})) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($rating === 0)
                                    Click a star to rate
                                @elseif($rating === 1)
                                    Poor
                                @elseif($rating === 2)
                                    Fair
                                @elseif($rating === 3)
                                    Good
                                @elseif($rating === 4)
                                    Very Good
                                @else
                                    Excellent
                                @endif
                            </p>
                            @error('rating')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Review Comment <span class="text-gray-400">(optional)</span>
                            </label>
                            <textarea
                                id="comment"
                                wire:model="comment"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Share details about your experience working together..."
                            ></textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ strlen($comment) }}/2000 characters
                            </p>
                            @error('comment')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Privacy notice -->
                        <div class="rounded-md bg-gray-50 dark:bg-gray-700 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Your review will remain hidden until {{ $reviewee->name }} submits their review, or until the 15-day review period ends. This ensures honest, unbiased feedback from both parties.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="flex items-center justify-between pt-4">
                            <a href="{{ route('dashboard') }}" wire:navigate class="text-gray-600 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                                Cancel
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                {{ $rating === 0 ? 'disabled' : '' }}
                            >
                                <span wire:loading.remove wire:target="submitReview">Submit Review</span>
                                <span wire:loading wire:target="submitReview">Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
