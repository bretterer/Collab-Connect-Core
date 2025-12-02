<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow group relative {{ $isPromoted ? 'promoted-card' : '' }}">
    @if($isPromoted)
        <!-- Promoted Badge Ribbon -->
        <div class="absolute top-0 right-0 z-20 overflow-hidden w-72 h-16">
            <div class="drop-shadow-xl bg-gradient-to-r from-yellow-400 to-orange-500 text-yellow-900 px-6 py-1 text-xs font-bold transform rotate-18 translate-x-20 translate-y-2 text-center whitespace-nowrap">
                ⭐ PROMOTED
            </div>
        </div>
        <!-- Promoted Glow Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 to-orange-500/20 rounded-lg pointer-events-none"></div>
    @endif

    <!-- Card Header with Cover -->
    <div class="h-32 relative overflow-hidden z-0">
        <img src="{{ $coverImageUrl }}" alt="Cover photo" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/10"></div>

        @if($showFavorites)
            <div class="absolute top-3 left-3 flex space-x-2 z-10">
                <button class="p-1.5 rounded-full bg-white/20 hover:bg-white/30 transition-colors">
                    <flux:icon.heart class="w-4 h-4 text-white" />
                </button>
            </div>
        @endif
    </div>

    <!-- Card Content with Profile Avatar positioned at top -->
    <div class="relative">
        <!-- Profile Avatar - positioned relative to content, not header -->
        <div class="absolute -top-8 left-4 z-10">
            <div class="w-16 h-16 rounded-full border-4 border-white dark:border-gray-800 relative bg-white">
                <img src="{{ $profileImageUrl }}" alt="Profile photo" class="w-full h-full object-cover rounded-full">
                @if($isVerified)
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center border-2 border-white z-10">
                        <flux:icon.check class="w-3 h-3 text-white" />
                    </div>
                @endif
            </div>
        </div>

        <!-- Content Section -->
        <div class="pt-10 p-4 space-y-3">
            <!-- Name and Location -->
            <div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg leading-tight">
                            {{ $user->name }}
                        </h3>
                        @if($isVerified)
                            <div class="flex items-center gap-1">
                                <flux:icon.check-badge class="w-5 h-5 text-blue-500" alt="Verified" />
                            </div>
                        @endif
                    </div>


                </div>
                @if(false === true)
                <!-- Achievement Badges Area -->
                <div class="flex -space-x-1">
                    <!-- Example badges - replace with actual user badges -->
                    <div class="w-6 h-6 cursor-default rounded-full bg-yellow-400 border-2 border-white dark:border-gray-800 flex items-center justify-center hover:transform hover:scale-150 transition-transform" title="Top Performer">
                        <span class="text-xs">🏆</span>
                    </div>
                    <div class="w-6 h-6 cursor-default rounded-full bg-purple-500 border-2 border-white dark:border-gray-800 flex items-center justify-center hover:transform hover:scale-150 transition-transform" title="Engagement Master">
                        <span class="text-xs">⚡</span>
                    </div>
                    <div class="w-6 h-6 cursor-default rounded-full bg-green-500 border-2 border-white dark:border-gray-800 flex items-center justify-center hover:transform hover:scale-150 transition-transform" title="Rising Star">
                        <span class="text-xs">⭐</span>
                    </div>
                    <div class="w-6 h-6 cursor-default rounded-full bg-blue-500 border-2 border-white dark:border-gray-800 flex items-center justify-center hover:transform hover:scale-150 transition-transform" title="Content Creator">
                        <span class="text-xs">📱</span>
                    </div>
                </div>
                @endif
                @if($user->influencer->postalCodeInfo)
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">
                    <flux:icon.map-pin class="w-3 h-3 mr-1" />
                    {{ $user->influencer->postalCodeInfo->place_name }}, {{ $user->influencer->postalCodeInfo->admin_code1 }}
                </p>
                @endif
            </div>


            <!-- Social Stats -->
            <!-- Platform Icons with Numbers -->
            <div class="flex justify-center gap-3 py-3 border-t border-gray-100 dark:border-gray-700">
                @foreach($socialAccounts as $socialAccount)
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium {{ $socialAccount->platform->getStyleClasses() }}">
                        {!! $socialAccount->platform->svg('w-6') !!}
                    </div>
                    <div class="flex items-center gap-1 mt-1">
                        <div class="text-xs font-medium text-gray-900 dark:text-white">
                            {{ $socialAccount->followers >= 1000 ? Number::abbreviate($socialAccount->followers, 1) : $socialAccount->followers }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Overall Stats -->
            <div class="grid grid-cols-2 gap-2 text-center py-2 border-t border-gray-100 dark:border-gray-700">
                <div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $user->influencer->totalFollowers >= 1000 ? Number::abbreviate($user->influencer->totalFollowers, 1) : $user->influencer->totalFollowers }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Followers</div>
                </div>
                <div>
                    @if($averageRating !== null)
                    <div class="flex items-center justify-center gap-0.5 mb-1">
                        @php $fullStars = floor($averageRating); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $fullStars)
                                <flux:icon.star class="w-3 h-3 text-yellow-400 fill-yellow-400" />
                            @else
                                <flux:icon.star class="w-3 h-3 text-gray-300 fill-gray-300" />
                            @endif
                        @endfor
                    </div>
                    <div class="text-xs font-medium text-gray-900 dark:text-white">
                        {{ $averageRating }}/5
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</div>
                    @else
                    <div class="text-sm text-gray-400 dark:text-gray-500">No reviews yet</div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2 pt-2">
                <flux:button variant="outline" size="sm" class="flex-1" href="{{ route('influencer.profile', ['username' => $this->getUsername()]) }}" wire:navigate>
                    View Profile
                </flux:button>
                <flux:button
                    variant="primary"
                    size="sm"
                    class="flex-1"
                    wire:click="$dispatch('open-invite-modal', { influencerId: {{ $user->id }}, influencerName: {{ json_encode($user->name) }} })"
                >
                    Invite to Campaign
                </flux:button>
            </div>
        </div>
    </div>
</div>