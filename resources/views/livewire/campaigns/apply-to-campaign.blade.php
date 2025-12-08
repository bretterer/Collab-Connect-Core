<div>
    @if($existingApplication)
        <!-- Already Applied State -->
        <div class="w-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium py-3 px-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 text-center">
            <div class="flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>
                    Application {{ $existingApplication->status->label() }}
                </span>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Applied {{ $existingApplication->submitted_at->diffForHumans() }}
            </div>
        </div>
    @else
        <!-- Apply Button -->
        <flux:button wire:click="openModal" variant="{{ $buttonVariant }}" class="flex-1">
            {{ $buttonText }}
        </flux:button>
    @endif

    <!-- Application Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.5);" wire:click="closeModal">
        <!-- Modal Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Apply to Campaign
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Review the campaign details and submit your application.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Details -->
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Campaign Details</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Campaign Goal:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $campaign->campaign_goal }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Campaign Type:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    @if($campaign->campaign_type && count($campaign->campaign_type) > 0)
                                        {{ $campaign->campaign_type->take(2)->map(fn($type) => $type->label())->join(', ') }}{{ count($campaign->campaign_type) > 2 ? ' +' . (count($campaign->campaign_type) - 2) . ' more' : '' }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Compensation:</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $campaign->compensation?->compensation_display ?? 'Not set' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Location:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $campaign->target_zip_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Match Score:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($this->getMatchScore(), 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Application Form -->
                    <div class="mt-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your Message (Cover Letter)
                        </label>
                        <textarea
                            wire:model="message"
                            id="message"
                            rows="6"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Tell the business why you're a great fit for this campaign. Include your relevant experience, audience demographics, and how you can help achieve their goals..."
                        ></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        wire:click="submitApplication"
                        wire:loading.attr="disabled"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove>Submit Application</span>
                        <span wire:loading>Submitting...</span>
                    </button>
                    <button
                        wire:click="closeModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
        </div>
    </div>
    @endif
</div>
