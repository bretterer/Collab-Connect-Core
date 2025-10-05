@php
    $influencer = $user->influencer;
    $socialAccounts = $influencer?->socialAccounts ?? collect();
    $completedCampaigns = \App\Models\CampaignApplication::with('campaign.business')
        ->where('user_id', $user->id)
        ->where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)
        ->latest()
        ->limit(5)
        ->get();
    $totalFollowers = $socialAccounts->sum('followers');
    $profileImageUrl = $influencer?->getProfileImageUrl() ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=200';
    $location = collect([$influencer?->city, $influencer?->state])->filter()->implode(', ') ?: 'Location not provided';
    $joinedDate = 'Member since ' . $user->created_at->format('M Y');
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Card --}}
        <flux:card class="mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    <img src="{{ $profileImageUrl }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-700 shadow-lg">
                </div>

                {{-- Profile Info --}}
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <flux:heading size="xl" class="mb-1">{{ $user->name }}</flux:heading>
                            @if($influencer?->username)
                                <flux:text class="text-gray-600 dark:text-gray-400 mb-2">{{'@' . $influencer->username }}</flux:text>
                            @endif

                            {{-- Stats --}}
                            <div class="flex flex-wrap gap-4 mb-3">
                                <div class="flex items-center gap-2">
                                    <flux:icon.check-badge variant="solid" class="w-5 h-5 text-green-500" />
                                    <flux:text class="font-medium">{{ $influencer?->completed_collaborations ?? 0 }} campaigns completed</flux:text>
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:icon.map-pin variant="solid" class="w-5 h-5 text-gray-400" />
                                    <flux:text class="text-gray-600 dark:text-gray-400">{{ $location }}</flux:text>
                                </div>
                            </div>

                            <flux:text class="text-gray-600 dark:text-gray-400 text-sm">{{ $joinedDate }}</flux:text>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            <flux:button variant="primary">Contact</flux:button>
                            <flux:button variant="ghost">
                                <flux:icon.heart />
                            </flux:button>
                        </div>
                    </div>

                    {{-- Bio --}}
                    @if($influencer?->bio)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <flux:text>{{ $influencer->bio }}</flux:text>
                        </div>
                    @endif

                    {{-- Niches/Industries --}}
                    @if($influencer && $influencer->content_types && count($influencer->content_types) > 0)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($influencer->content_types_labels as $niche)
                                <flux:badge>{{ $niche }}</flux:badge>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </flux:card>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Social Media Stats --}}
                @if($socialAccounts->count() > 0)
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Social Media Presence</flux:heading>
                        <div class="space-y-4">
                            @foreach ($socialAccounts as $account)
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($account->platform->value, 0, 1)) }}
                                            </div>
                                            <div>
                                                <flux:heading size="sm">{{ $account->platform->value }}</flux:heading>
                                                <flux:text class="text-sm text-gray-600 dark:text-gray-400">{{ '@' . $account->username }}</flux:text>
                                            </div>
                                        </div>
                                        <flux:badge variant="primary">{{ number_format($account->followers) }} followers</flux:badge>
                                    </div>

                                    @if($account->url)
                                        <div class="mt-2">
                                            <a href="{{ $account->url }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                                View Profile →
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endif

                {{-- Completed Campaigns --}}
                @if($completedCampaigns->count() > 0)
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Campaign History</flux:heading>
                        <div class="space-y-4">
                            @foreach ($completedCampaigns as $application)
                                @if($application->campaign)
                                    <div class="pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <flux:heading size="sm">{{ $application->campaign->business->name ?? 'Business' }}</flux:heading>
                                                <flux:text class="text-sm text-gray-600 dark:text-gray-400">{{ $application->campaign->campaign_goal }}</flux:text>
                                            </div>
                                            <div class="text-right">
                                                <flux:badge variant="outline">{{ $application->accepted_at?->format('M Y') }}</flux:badge>
                                            </div>
                                        </div>
                                        @if($application->campaign->campaign_type && $application->campaign->campaign_type->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach($application->campaign->campaign_type as $type)
                                                    <flux:badge variant="outline" class="text-xs">{{ $type->label() }}</flux:badge>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </flux:card>
                @else
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Campaign History</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400">No completed campaigns yet.</flux:text>
                    </flux:card>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Quick Stats --}}
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Quick Stats</flux:heading>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <flux:text class="text-gray-600 dark:text-gray-400">Total Followers</flux:text>
                            <flux:text class="font-semibold">{{ number_format($totalFollowers) }}</flux:text>
                        </div>
                        <div class="flex justify-between items-center">
                            <flux:text class="text-gray-600 dark:text-gray-400">Platforms</flux:text>
                            <flux:text class="font-semibold">{{ $socialAccounts->count() }}</flux:text>
                        </div>
                        <div class="flex justify-between items-center">
                            <flux:text class="text-gray-600 dark:text-gray-400">Completed Campaigns</flux:text>
                            <flux:text class="font-semibold">{{ $influencer?->completed_collaborations ?? 0 }}</flux:text>
                        </div>
                        @if($influencer?->is_verified)
                            <div class="flex justify-between items-center">
                                <flux:text class="text-gray-600 dark:text-gray-400">Verified</flux:text>
                                <flux:icon.check-badge variant="solid" class="w-5 h-5 text-green-500" />
                            </div>
                        @endif
                    </div>
                </flux:card>

                {{-- Profile Details --}}
                @if($influencer)
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Profile Details</flux:heading>
                        <div class="space-y-4">
                            @if($influencer->typical_lead_time_days)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Typical Lead Time</flux:text>
                                    <flux:text class="font-semibold">{{ $influencer->typical_lead_time_days }} days</flux:text>
                                </div>
                            @endif

                            @if($influencer->primary_industry)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Primary Industry</flux:text>
                                    <flux:text class="font-semibold">{{ $influencer->primary_industry->label() }}</flux:text>
                                </div>
                            @endif

                            @if($influencer->compensation_types && count($influencer->compensation_types) > 0)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-2">Compensation Types</flux:text>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($influencer->compensation_types_labels as $type)
                                            <flux:badge variant="outline" class="text-xs">{{ $type }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($influencer->avg_response_time_hours)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Avg. Response Time</flux:text>
                                    <flux:text class="font-semibold">{{ round($influencer->avg_response_time_hours) }} hours</flux:text>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                @endif
            </div>
        </div>
    </div>
</div>
