@php
    $business = $user->businesses()->with('campaigns')->first();
    $activeCampaigns = $business?->campaigns()->where('status', \App\Enums\CampaignStatus::PUBLISHED)->get() ?? collect();
    $completedCampaigns = $business?->campaigns()->where('status', \App\Enums\CampaignStatus::ARCHIVED)->limit(5)->get() ?? collect();
    $logoUrl = $business?->getLogoUrl() ?? 'https://ui-avatars.com/api/?name=' . urlencode($business?->name ?? $user->name) . '&size=200';
    $location = collect([$business?->city, $business?->state])->filter()->implode(', ') ?: 'Location not provided';
    $joinedDate = 'Member since ' . $user->created_at->format('M Y');
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Card --}}
        <flux:card class="mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                {{-- Logo --}}
                <div class="flex-shrink-0">
                    <img src="{{ $logoUrl }}" alt="{{ $business?->name ?? $user->name }}" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-700 shadow-lg">
                </div>

                {{-- Business Info --}}
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <flux:heading size="xl" class="mb-1">{{ $business?->name ?? $user->name }}</flux:heading>
                            @if($business?->website)
                                <a href="{{ $business->website }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                    {{ $business->website }}
                                </a>
                            @endif

                            {{-- Stats --}}
                            <div class="flex flex-wrap gap-4 mb-3 mt-3">
                                <div class="flex items-center gap-2">
                                    <flux:icon.briefcase variant="solid" class="w-5 h-5 text-purple-500" />
                                    <flux:text class="font-medium">{{ $activeCampaigns->count() }} active campaigns</flux:text>
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:icon.map-pin variant="solid" class="w-5 h-5 text-gray-400" />
                                    <flux:text class="text-gray-600 dark:text-gray-400">{{ $location }}</flux:text>
                                </div>
                            </div>

                            <flux:text class="text-gray-600 dark:text-gray-400 text-sm">{{ $joinedDate }}</flux:text>
                        </div>

                        {{-- Action Buttons --}}
                        {{-- Hidden for now
                        <div class="flex gap-3">
                            <flux:button variant="primary">Contact</flux:button>
                            <flux:button variant="ghost">
                                <flux:icon.heart />
                            </flux:button>
                        </div>
                        --}}
                    </div>

                    {{-- Description --}}
                    @if($business?->description)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <flux:text>{{ $business->description }}</flux:text>
                        </div>
                    @endif

                    {{-- Industry/Type --}}
                    @if($business)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($business->industry)
                                <flux:badge>{{ $business->industry->label() }}</flux:badge>
                            @endif
                            @if($business->type)
                                <flux:badge variant="outline">{{ $business->type->label() }}</flux:badge>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </flux:card>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Active Campaigns --}}
                @if($activeCampaigns->count() > 0)
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Active Campaigns</flux:heading>
                        <div class="space-y-4">
                            @foreach ($activeCampaigns as $campaign)
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <flux:heading size="sm">{{ $campaign->campaign_goal }}</flux:heading>
                                            @if($campaign->campaign_description)
                                                <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($campaign->campaign_description, 150) }}</flux:text>
                                            @endif
                                        </div>
                                        <flux:badge variant="primary">Active</flux:badge>
                                    </div>
                                    @if($campaign->campaign_type && $campaign->campaign_type->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($campaign->campaign_type as $type)
                                                <flux:badge variant="outline" class="text-xs">{{ $type->label() }}</flux:badge>
                                            @endforeach
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
                        <flux:heading size="lg" class="mb-4">Past Campaigns</flux:heading>
                        <div class="space-y-4">
                            @foreach ($completedCampaigns as $campaign)
                                <div class="pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <flux:heading size="sm">{{ $campaign->campaign_goal }}</flux:heading>
                                            @if($campaign->campaign_description)
                                                <flux:text class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($campaign->campaign_description, 100) }}</flux:text>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <flux:badge variant="outline">Completed</flux:badge>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                            <flux:text class="text-gray-600 dark:text-gray-400">Active Campaigns</flux:text>
                            <flux:text class="font-semibold">{{ $activeCampaigns->count() }}</flux:text>
                        </div>
                        <div class="flex justify-between items-center">
                            <flux:text class="text-gray-600 dark:text-gray-400">Completed Campaigns</flux:text>
                            <flux:text class="font-semibold">{{ $completedCampaigns->count() }}</flux:text>
                        </div>
                        <div class="flex justify-between items-center">
                            <flux:text class="text-gray-600 dark:text-gray-400">Rating</flux:text>
                            @if($averageRating !== null)
                                <div class="flex items-center gap-1">
                                    <div class="flex items-center gap-0.5">
                                        @php $fullStars = floor($averageRating); @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $fullStars)
                                                <flux:icon.star class="w-4 h-4 text-yellow-400 fill-yellow-400" />
                                            @else
                                                <flux:icon.star class="w-4 h-4 text-gray-300 fill-gray-300" />
                                            @endif
                                        @endfor
                                    </div>
                                    <flux:text class="font-semibold">{{ $averageRating }}</flux:text>
                                </div>
                            @else
                                <flux:text class="text-gray-400">No reviews</flux:text>
                            @endif
                        </div>
                    </div>
                </flux:card>

                {{-- Reviews --}}
                <flux:card>
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg">Reviews</flux:heading>
                        @if($reviewCount > 0)
                            <flux:badge>{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</flux:badge>
                        @endif
                    </div>
                    @if($reviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($reviews->take(3) as $review)
                                <div class="pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-medium">
                                                {{ $review->reviewer->initials() }}
                                            </div>
                                            <div>
                                                <flux:text class="font-medium text-sm">{{ $review->reviewer->name }}</flux:text>
                                                <flux:text class="text-xs text-gray-500">{{ $review->submitted_at->diffForHumans() }}</flux:text>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <flux:icon.star class="w-3 h-3 text-yellow-400 fill-yellow-400" />
                                                @else
                                                    <flux:icon.star class="w-3 h-3 text-gray-300 fill-gray-300" />
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <flux:text class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($review->comment, 150) }}</flux:text>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('business.reviews', ['username' => $business?->username ?? $business?->id]) }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                View all {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }} →
                            </a>
                        </div>
                    @else
                        <flux:text class="text-gray-500 dark:text-gray-400">No reviews yet.</flux:text>
                    @endif
                </flux:card>

                {{-- Business Details --}}
                @if($business)
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Business Details</flux:heading>
                        <div class="space-y-4">
                            @if($business->size)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Company Size</flux:text>
                                    <flux:text class="font-semibold">{{ $business->size }}</flux:text>
                                </div>
                            @endif

                            @if($business->industry)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Industry</flux:text>
                                    <flux:text class="font-semibold">{{ $business->industry->label() }}</flux:text>
                                </div>
                            @endif

                            @if($business->type)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Business Type</flux:text>
                                    <flux:text class="font-semibold">{{ $business->type->label() }}</flux:text>
                                </div>
                            @endif

                            @if($business->phone)
                                <div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">Phone</flux:text>
                                    <flux:text class="font-semibold">{{ $business->phone }}</flux:text>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                @endif
            </div>
        </div>
    </div>
</div>
