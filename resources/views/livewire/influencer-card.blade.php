<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow group relative {{ $isPromoted ? 'ring-2 ring-yellow-400' : '' }}">
    @if($isPromoted)
        <!-- Promoted Badge -->
        <div class="absolute top-2 right-2 z-20">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gradient-to-r from-yellow-400 to-orange-500 text-yellow-900">
                Promoted
            </span>
        </div>
    @endif

    <!-- Card Header with Cover -->
    <div class="h-24 relative overflow-hidden">
        <img src="{{ $coverImageUrl }}" alt="Cover photo" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

        @if($showFavorites)
            <div class="absolute top-2 left-2 z-10 flex gap-1">
                <button
                    wire:click="toggleSave"
                    class="p-1.5 rounded-full bg-white/80 hover:bg-white transition-colors shadow-sm"
                    title="{{ $isSaved ? 'Remove from saved' : 'Save influencer' }}"
                >
                    @if($isSaved)
                        <flux:icon.heart class="w-4 h-4 text-red-500 fill-red-500" />
                    @else
                        <flux:icon.heart class="w-4 h-4 text-gray-600 hover:text-red-500" />
                    @endif
                </button>
                <button
                    wire:click="toggleHide"
                    class="p-1.5 rounded-full bg-white/80 hover:bg-white transition-colors shadow-sm"
                    title="{{ $isHidden ? 'Show influencer' : 'Hide influencer' }}"
                >
                    @if($isHidden)
                        <flux:icon.eye class="w-4 h-4 text-gray-600 hover:text-gray-800" />
                    @else
                        <flux:icon.eye-slash class="w-4 h-4 text-gray-600 hover:text-gray-800" />
                    @endif
                </button>
            </div>
        @endif
    </div>

    <!-- Card Content -->
    <div class="relative px-4 pb-4">
        <!-- Profile Avatar -->
        <div class="absolute -top-8 left-4">
            <div class="w-16 h-16 rounded-full border-4 border-white dark:border-gray-800 shadow-md overflow-hidden bg-white">
                <img src="{{ $profileImageUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            </div>
            @if($isVerified)
                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800">
                    <flux:icon.check class="w-3 h-3 text-white" />
                </div>
            @endif
        </div>

        <!-- Name and Location -->
        <div class="pt-10">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-gray-900 dark:text-white truncate flex items-center gap-1">
                        {{ $user->name }}
                        @if($isVerified)
                            <flux:icon.check-badge class="w-4 h-4 text-blue-500 shrink-0" />
                        @endif
                    </h3>
                    @if($user->influencer?->postalCodeInfo)
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-0.5">
                            <flux:icon.map-pin class="w-3 h-3" />
                            {{ $user->influencer->postalCodeInfo->place_name }}, {{ $user->influencer->postalCodeInfo->admin_code1 }}
                            @if(isset($user->distance) && $user->distance !== null)
                                <span class="text-gray-400">·</span>
                                <span>{{ number_format($user->distance, 1) }} mi</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Social Platforms -->
        @if($socialAccounts->count() > 0)
            <div class="flex items-center justify-center gap-2 mt-4 py-3 border-t border-gray-100 dark:border-gray-700">
                @foreach($socialAccounts->take(4) as $socialAccount)
                    <a
                        href="{{ $socialAccount->normalizedUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex flex-col items-center group/social"
                        title="{{ $socialAccount->platform->label() }}"
                    >
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $socialAccount->platform->getStyleClasses() }} group-hover/social:scale-110 transition-transform">
                            {!! $socialAccount->platform->svg('w-5') !!}
                        </div>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1">
                            {{ $socialAccount->followers >= 1000 ? Number::abbreviate($socialAccount->followers, 1) : $socialAccount->followers }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Stats Row -->
        <div class="grid grid-cols-2 gap-2 text-center py-3 border-t border-gray-100 dark:border-gray-700">
            <div>
                <div class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $totalFollowers >= 1000 ? Number::abbreviate($totalFollowers, 1) : $totalFollowers }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Total Followers</div>
            </div>
            <div>
                @if($averageRating !== null)
                    <div class="flex items-center justify-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <flux:icon.star class="w-3 h-3 {{ $i <= floor($averageRating) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}" />
                        @endfor
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ number_format($averageRating, 1) }} ({{ $reviewCount }})
                    </div>
                @else
                    <div class="text-sm text-gray-400 dark:text-gray-500">No reviews</div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 pt-2">
            <flux:button
                variant="outline"
                size="sm"
                class="flex-1"
                href="{{ route('influencer.profile', ['username' => $this->getUsername()]) }}"
                wire:navigate
            >
                View Profile
            </flux:button>
            @if($acceptingInvitations)
            <flux:button
                variant="primary"
                size="sm"
                class="flex-1"
                wire:click="$dispatch('open-invite-modal', { influencerId: {{ $user->id }}, influencerName: {{ json_encode($user->name) }} })"
            >
                Invite
            </flux:button>
            @endif
        </div>
    </div>
</div>
