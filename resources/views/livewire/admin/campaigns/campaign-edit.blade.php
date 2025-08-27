<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Campaign</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Update campaign information and status.
        </p>
    </div>

    <!-- Edit Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form wire:submit="save" class="px-4 py-5 sm:p-6">
            <div class="space-y-6">
                <!-- Campaign Goal -->
                <div>
                    <label for="campaignGoal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Campaign Goal *
                    </label>
                    <div class="mt-1">
                        <textarea wire:model="campaignGoal"
                                  id="campaignGoal"
                                  rows="3"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                  placeholder="Describe the main goal of this campaign..."></textarea>
                        @error('campaignGoal')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Campaign Description -->
                <div>
                    <label for="campaignDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Campaign Description
                    </label>
                    <div class="mt-1">
                        <textarea wire:model="campaignDescription"
                                  id="campaignDescription"
                                  rows="4"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                  placeholder="Provide additional details about the campaign..."></textarea>
                        @error('campaignDescription')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Campaign Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Campaign Status *
                    </label>
                    <div class="mt-1">
                        <select wire:model="status"
                                id="status"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            @foreach($this->getStatusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <strong>Warning:</strong> Changing the status may affect campaign visibility and influencer applications.
                    </p>
                </div>

                <!-- Campaign Owner Info -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Campaign Owner
                    </label>
                    <div class="mt-1 flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            {{ $campaign->business->owner->first()->initials() }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $campaign->business->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $campaign->business->owner->first()->email }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Dates -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Created Date
                        </label>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $campaign->created_at->format('F j, Y g:i A') }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Last Updated
                        </label>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $campaign->updated_at->format('F j, Y g:i A') }}
                        </div>
                    </div>
                </div>

                @if($campaign->published_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Published Date
                        </label>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $campaign->published_at->format('F j, Y g:i A') }}
                        </div>
                    </div>
                @endif

                @if($campaign->scheduled_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Scheduled Date
                        </label>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $campaign->scheduled_date->format('F j, Y') }}
                        </div>
                    </div>
                @endif

                <!-- Campaign Metrics -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Campaign Metrics
                    </label>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $campaign->applications()->count() }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Applications</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $campaign->influencer_count ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Target Count</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $campaign->current_step ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Current Step</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $campaign->status === \App\Enums\CampaignStatus::PUBLISHED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                                       ($campaign->status === \App\Enums\CampaignStatus::DRAFT ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                                        ($campaign->status === \App\Enums\CampaignStatus::SCHEDULED ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
                                         'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400')) }}">
                                    {{ $campaign->status->label() }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Status</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.campaigns.show', $campaign) }}">
                        <flux:button variant="ghost">Cancel</flux:button>
                    </a>
                </div>
                <div class="flex items-center space-x-3">
                    <flux:button type="submit" variant="primary">
                        Save Changes
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</div>
