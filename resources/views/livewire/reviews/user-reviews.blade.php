<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Card --}}
        <flux:card class="mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    @if($type === 'business')
                        @php
                            $profileImageUrl = $business?->logo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($business?->name ?? 'Business') . '&size=200';
                        @endphp
                        <img src="{{ $profileImageUrl }}" alt="{{ $business?->name }}" class="w-24 h-24 rounded-full border-4 border-white dark:border-gray-700 shadow-lg">
                    @else
                        @php
                            $influencer = $user?->influencer;
                            $profileImageUrl = $influencer?->getProfileImageUrl() ?? 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&size=200';
                        @endphp
                        <img src="{{ $profileImageUrl }}" alt="{{ $user?->name }}" class="w-24 h-24 rounded-full border-4 border-white dark:border-gray-700 shadow-lg">
                    @endif
                </div>

                {{-- Profile Info --}}
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <flux:heading size="xl" class="mb-1">
                                @if($type === 'business')
                                    {{ $business?->name }}
                                @else
                                    {{ $user?->name }}
                                @endif
                            </flux:heading>

                            @if($type === 'business')
                                @if($business?->username)
                                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">{{'@' . $business->username }}</flux:text>
                                @endif
                            @else
                                @if($user?->influencer?->username)
                                    <flux:text class="text-gray-600 dark:text-gray-400 mb-2">{{'@' . $user->influencer->username }}</flux:text>
                                @endif
                            @endif

                            {{-- Rating Summary --}}
                            <div class="flex items-center gap-3 mt-3">
                                @if($averageRating !== null)
                                    <div class="flex items-center gap-1">
                                        @php $fullStars = floor($averageRating); @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $fullStars)
                                                <flux:icon.star class="w-5 h-5 text-yellow-400 fill-yellow-400" />
                                            @else
                                                <flux:icon.star class="w-5 h-5 text-gray-300 fill-gray-300" />
                                            @endif
                                        @endfor
                                    </div>
                                    <flux:text class="font-semibold text-lg">{{ $averageRating }}</flux:text>
                                    <flux:text class="text-gray-500">·</flux:text>
                                @endif
                                <flux:text class="text-gray-600 dark:text-gray-400">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</flux:text>
                            </div>
                        </div>

                        {{-- Back Button --}}
                        <div>
                            @if($type === 'business')
                                <flux:button variant="ghost" href="{{ route('business.profile', ['username' => $business?->username ?? $business?->id]) }}" wire:navigate>
                                    <flux:icon.arrow-left class="w-4 h-4 mr-1" />
                                    Back to Profile
                                </flux:button>
                            @else
                                <flux:button variant="ghost" href="{{ route('influencer.profile', ['username' => $user?->influencer?->username ?? $user?->id]) }}" wire:navigate>
                                    <flux:icon.arrow-left class="w-4 h-4 mr-1" />
                                    Back to Profile
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- Reviews List --}}
        <flux:card>
            <flux:heading size="lg" class="mb-6">All Reviews</flux:heading>

            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="pb-6 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">
                                        {{ $review->reviewer->initials() }}
                                    </div>
                                    <div>
                                        <flux:text class="font-medium">{{ $review->reviewer->name }}</flux:text>
                                        <flux:text class="text-sm text-gray-500">{{ $review->submitted_at->format('M d, Y') }}</flux:text>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <flux:icon.star class="w-4 h-4 text-yellow-400 fill-yellow-400" />
                                        @else
                                            <flux:icon.star class="w-4 h-4 text-gray-300 fill-gray-300" />
                                        @endif
                                    @endfor
                                </div>
                            </div>

                            @if($review->comment)
                                <flux:text class="text-gray-700 dark:text-gray-300 mb-3">{{ $review->comment }}</flux:text>
                            @endif

                            @if($review->collaboration?->campaign)
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <flux:text class="text-xs text-gray-500 dark:text-gray-400 mb-1">Campaign</flux:text>
                                    <flux:text class="text-sm font-medium">{{ $review->collaboration->campaign->campaign_goal }}</flux:text>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <flux:icon.chat-bubble-left-right class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
                    <flux:heading size="base" class="mb-2">No reviews yet</flux:heading>
                    <flux:text class="text-gray-500 dark:text-gray-400">
                        @if($type === 'business')
                            This business hasn't received any reviews from collaborations yet.
                        @else
                            This influencer hasn't received any reviews from collaborations yet.
                        @endif
                    </flux:text>
                </div>
            @endif
        </flux:card>
    </div>
</div>
