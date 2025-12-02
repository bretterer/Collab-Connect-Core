<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Business Profile
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    View business information and details
                </p>
            </div>
        </div>
    </div>

    <!-- Business Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
        <!-- Cover Image -->
        <div class="h-48 relative overflow-hidden">
            @php
                $randomSeed = rand(1, 799);
                $coverImageUrl = "https://picsum.photos/seed/" . ($randomSeed + 1) . "/800/300";
            @endphp
            <img src="{{ $coverImageUrl }}" alt="Business cover" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <!-- Business Info -->
        <div class="p-6">
            <div class="flex items-center space-x-4 mb-6">
                <!-- Business Logo -->
                <div class="w-20 h-20 rounded-lg border-4 border-white dark:border-gray-800 relative -mt-12 bg-white dark:bg-gray-800">
                    @php
                        $logoUrl = null;
                        try {
                            $logoUrl = $user->currentBusiness?->getLogoUrl();
                        } catch (Exception $e) {
                            $logoUrl = null;
                        }
                        $profileImageUrl = "https://picsum.photos/seed/{$randomSeed}/150/150";
                    @endphp
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Business logo" class="w-full h-full object-cover rounded-lg">
                    @else
                        <img src="{{ $profileImageUrl }}" alt="Business logo" class="w-full h-full object-cover rounded-lg">
                    @endif
                </div>

                <!-- Business Details -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $user->currentBusiness->name ?? $user->name }}
                    </h2>
                    @if($user->currentBusiness && $user->currentBusiness->industry)
                    <p class="text-gray-600 dark:text-gray-300">{{ $user->currentBusiness->industry->label() }}</p>
                    @endif
                    @if($user->currentBusiness && $user->currentBusiness->city)
                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mt-1">
                        <flux:icon.map-pin class="w-4 h-4 mr-1" />
                        {{ $user->currentBusiness->city }}, {{ $user->currentBusiness->state }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Business Description -->
            @if($user->currentBusiness && $user->currentBusiness->description)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">About</h3>
                <p class="text-gray-600 dark:text-gray-300">{{ $user->currentBusiness->description }}</p>
            </div>
            @endif

            <!-- Business Stats -->
            <div class="grid grid-cols-3 gap-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $user->currentBusiness ? $user->currentBusiness->campaigns()->count() : 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Campaigns</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $user->created_at->format('Y') }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Member Since</div>
                </div>
                <div class="text-center">
                    @if($averageRating !== null)
                        <div class="flex items-center justify-center gap-0.5 mb-1">
                            @php $fullStars = floor($averageRating); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $fullStars)
                                    <flux:icon.star class="w-4 h-4 text-yellow-400 fill-yellow-400" />
                                @else
                                    <flux:icon.star class="w-4 h-4 text-gray-300 fill-gray-300" />
                                @endif
                            @endfor
                        </div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $averageRating }}/5</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</div>
                    @else
                        <div class="text-lg text-gray-400 dark:text-gray-500">-</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">No reviews</div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            @if($user->currentBusiness)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($user->currentBusiness->email)
                    <div class="flex items-center space-x-2">
                        <flux:icon.envelope class="w-4 h-4 text-gray-400" />
                        <span class="text-gray-600 dark:text-gray-300">{{ $user->currentBusiness->email }}</span>
                    </div>
                    @endif
                    @if($user->currentBusiness->phone)
                    <div class="flex items-center space-x-2">
                        <flux:icon.phone class="w-4 h-4 text-gray-400" />
                        <span class="text-gray-600 dark:text-gray-300">{{ $user->currentBusiness->phone }}</span>
                    </div>
                    @endif
                    @if($user->currentBusiness->website)
                    <div class="flex items-center space-x-2">
                        <flux:icon.globe-alt class="w-4 h-4 text-gray-400" />
                        <a href="{{ $user->currentBusiness->website }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $user->currentBusiness->website }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reviews</h3>
                @if($reviewCount > 0)
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span>
                @endif
            </div>

            @if($reviews->count() > 0)
                <div class="space-y-4">
                    @foreach($reviews->take(5) as $review)
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center text-white text-sm font-medium">
                                        {{ $review->reviewer->initials() }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $review->reviewer->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $review->submitted_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5">
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
                                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No reviews yet.</p>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-4">
        <flux:button variant="outline" wire:navigate href="{{ route('search') }}" icon="arrow-left">
            Back to Search
        </flux:button>
        @if($user->currentBusiness && $user->currentBusiness->campaigns()->exists())
            <flux:button variant="primary" wire:navigate href="{{ route('business.campaigns', $user) }}" icon="eye">
                View Campaigns
            </flux:button>
        @endif
        <flux:button variant="ghost" icon="chat-bubble-left">
            Contact Business
        </flux:button>
    </div>
</div>