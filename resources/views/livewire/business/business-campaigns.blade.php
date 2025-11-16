<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $user->currentBusiness->name ?? $user->name }} Campaigns
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    View all campaigns from this business
                </p>
            </div>
            <flux:button variant="outline" wire:navigate href="{{ route('business.profile', ['username' => $user->currentBusiness->username ?? $user->id]) }}" icon="arrow-left">
                Back to Profile
            </flux:button>
        </div>
    </div>

    <!-- Business Info Bar -->
    @if($user->currentBusiness)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                @php
                    $logoUrl = null;
                    try {
                        $logoUrl = $user->currentBusiness?->getLogoUrl();
                    } catch (Exception $e) {
                        $logoUrl = null;
                    }
                    $randomSeed = rand(1, 799);
                    $profileImageUrl = "https://picsum.photos/seed/{$randomSeed}/150/150";
                @endphp
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Business logo" class="w-full h-full object-cover rounded-lg">
                @else
                    <img src="{{ $profileImageUrl }}" alt="Business logo" class="w-full h-full object-cover rounded-lg">
                @endif
            </div>
            <div class="flex-1">
                <h2 class="font-semibold text-gray-900 dark:text-white">{{ $user->currentBusiness->name }}</h2>
                @if($user->currentBusiness->industry)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->currentBusiness->industry->label() }}</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $campaigns->total() }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Campaigns</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Campaigns Grid -->
    @if($campaigns->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($campaigns as $campaign)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Campaign Header -->
                    <div class="h-32 relative overflow-hidden bg-gradient-to-r from-blue-500 to-purple-600">
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 text-xs font-medium bg-white/20 text-white rounded-full">
                                {{ $campaign->status->value }}
                            </span>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="text-lg font-semibold text-white line-clamp-2">
                                {{ $campaign->project_name ?? 'Campaign #' . $campaign->id }}
                            </h3>
                        </div>
                    </div>

                    <!-- Campaign Content -->
                    <div class="p-4 space-y-3">
                        <!-- Campaign Details -->
                        @if($campaign->campaign_description)
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                            {{ $campaign->campaign_description }}
                        </p>
                        @endif

                        <!-- Campaign Stats -->
                        <div class="grid grid-cols-2 gap-4 py-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $campaign->applications()->count() }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Applications</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    @if($campaign->compensation_type)
                                        {{ $campaign->compensation_type->value }}
                                    @else
                                        TBD
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Compensation</div>
                            </div>
                        </div>

                        <!-- Campaign Dates -->
                        @if($campaign->application_deadline || $campaign->campaign_completion_date)
                        <div class="space-y-2 text-sm">
                            @if($campaign->application_deadline)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Application Deadline:</span>
                                <span class="text-gray-900 dark:text-white">{{ $campaign->application_deadline->format('M j, Y') }}</span>
                            </div>
                            @endif
                            @if($campaign->campaign_completion_date)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Campaign End:</span>
                                <span class="text-gray-900 dark:text-white">{{ $campaign->campaign_completion_date->format('M j, Y') }}</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Action Button -->
                        <div class="pt-3">
                            <flux:button variant="primary" size="sm" class="w-full" wire:navigate href="{{ route('campaigns.show', $campaign) }}">
                                View Campaign Details
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $campaigns->links() }}
        </div>

    @else
        <!-- No Campaigns -->
        <div class="text-center py-12">
            <flux:icon.briefcase class="mx-auto h-12 w-12 text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No campaigns yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                This business hasn't created any campaigns yet.
            </p>
            <flux:button variant="outline" wire:navigate href="{{ route('business.profile', ['username' => $user->currentBusiness->username ?? $user->id]) }}">
                Back to Profile
            </flux:button>
        </div>
    @endif
</div>