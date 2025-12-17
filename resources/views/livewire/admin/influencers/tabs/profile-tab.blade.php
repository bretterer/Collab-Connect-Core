<flux:card>
    <div class="space-y-6">
        <div>
            <flux:heading>Influencer Profile</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                View and manage influencer information and profile details.
            </flux:text>
        </div>

        <flux:separator />

        <!-- Profile Image and Name -->
        <div class="flex items-start gap-6">
            @if($influencer->getProfileImageUrl())
                <img src="{{ $influencer->getProfileImageUrl() }}" alt="{{ $influencer->user?->name }}" class="w-20 h-20 rounded-full object-cover">
            @else
                <div class="w-20 h-20 bg-gradient-to-br from-pink-400 to-purple-400 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    {{ substr($influencer->user?->name ?? 'I', 0, 1) }}
                </div>
            @endif
            <div>
                <flux:heading size="xl">{{ $influencer->user?->name ?? 'Unknown' }}</flux:heading>
                <flux:text class="text-gray-500 dark:text-gray-400">{{ $influencer->user?->email }}</flux:text>
            </div>
        </div>

        <flux:separator />

        <!-- Bio -->
        @if($influencer->bio)
            <div>
                <flux:heading size="sm" class="mb-2">Bio</flux:heading>
                <flux:text>{{ $influencer->bio }}</flux:text>
            </div>
            <flux:separator />
        @endif

        <!-- Location Information -->
        <div>
            <flux:heading size="sm" class="mb-4">Location</flux:heading>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <flux:label>City</flux:label>
                    <flux:text class="mt-1">{{ $influencer->city ?? 'Not set' }}</flux:text>
                </div>
                <div>
                    <flux:label>State</flux:label>
                    <flux:text class="mt-1">{{ $influencer->state ?? 'Not set' }}</flux:text>
                </div>
                <div>
                    <flux:label>Postal Code</flux:label>
                    <flux:text class="mt-1">{{ $influencer->postal_code ?? 'Not set' }}</flux:text>
                </div>
            </div>
        </div>

        <!-- Content Types / Niches -->
        @if($influencer->content_types && count($influencer->content_types) > 0)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Content Types / Niches</flux:heading>
                <div class="flex flex-wrap gap-2">
                    @foreach($influencer->content_types as $type)
                        <flux:badge color="pink">{{ $type }}</flux:badge>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Platforms -->
        @if($influencer->platforms && count($influencer->platforms) > 0)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Preferred Platforms</flux:heading>
                <div class="flex flex-wrap gap-2">
                    @foreach($influencer->platforms as $platform)
                        <flux:badge color="blue">{{ $platform }}</flux:badge>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Compensation Preferences -->
        @if($influencer->compensation_types && count($influencer->compensation_types) > 0)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Compensation Preferences</flux:heading>
                <div class="flex flex-wrap gap-2">
                    @foreach($influencer->compensation_types as $type)
                        <flux:badge color="green">{{ $type }}</flux:badge>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Collaboration Preferences -->
        <flux:separator />
        <div>
            <flux:heading size="sm" class="mb-4">Collaboration Preferences</flux:heading>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <flux:label>Search Radius</flux:label>
                    <flux:text class="mt-1">{{ $influencer->search_radius ?? 'Not set' }} miles</flux:text>
                </div>
                <div>
                    <flux:label>Profile Visibility</flux:label>
                    <div class="mt-1">
                        @if($influencer->is_searchable)
                            <flux:badge color="green">Searchable</flux:badge>
                        @else
                            <flux:badge color="zinc">Hidden from Search</flux:badge>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Accounts -->
        @if($influencer->user?->socialMediaAccounts && $influencer->user->socialMediaAccounts->count() > 0)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Social Media Accounts</flux:heading>
                <div class="space-y-2">
                    @foreach($influencer->user->socialMediaAccounts as $account)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <flux:badge color="zinc">{{ $account->platform }}</flux:badge>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $account->handle }}</span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($account->followers) }} followers
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Portfolio -->
        @if($influencer->portfolio_urls && count($influencer->portfolio_urls) > 0)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Portfolio Links</flux:heading>
                <div class="space-y-2">
                    @foreach($influencer->portfolio_urls as $url)
                        <a href="{{ $url }}" target="_blank" class="block text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                            {{ $url }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Account Dates -->
        <flux:separator />
        <div>
            <flux:heading size="sm" class="mb-4">Account Information</flux:heading>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <flux:label>Profile Created</flux:label>
                    <flux:text class="mt-1">{{ $influencer->created_at->format('F j, Y') }}</flux:text>
                </div>
                <div>
                    <flux:label>Last Updated</flux:label>
                    <flux:text class="mt-1">{{ $influencer->updated_at->format('F j, Y') }}</flux:text>
                </div>
            </div>
        </div>
    </div>
</flux:card>
