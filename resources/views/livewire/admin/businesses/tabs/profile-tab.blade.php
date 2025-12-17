<flux:card>
    <div class="space-y-6">
        <div>
            <flux:heading>Business Profile</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                View and manage business information and profile details.
            </flux:text>
        </div>

        <flux:separator />

        <!-- Business Logo and Name -->
        <div class="flex items-start gap-6">
            @if($business->getLogoUrl())
                <img src="{{ $business->getLogoUrl() }}" alt="{{ $business->name }}" class="w-20 h-20 rounded-lg object-cover">
            @else
                <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-purple-400 rounded-lg flex items-center justify-center text-white text-2xl font-bold">
                    {{ substr($business->name ?? 'B', 0, 1) }}
                </div>
            @endif
            <div>
                <flux:heading size="xl">{{ $business->name }}</flux:heading>
                @if($business->username)
                    <flux:text class="text-gray-500 dark:text-gray-400">@{{ $business->username }}</flux:text>
                @endif
            </div>
        </div>

        <flux:separator />

        <!-- Basic Information -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <flux:label>Business Type</flux:label>
                <flux:text class="mt-1">{{ $business->type?->label() ?? 'Not set' }}</flux:text>
            </div>
            <div>
                <flux:label>Industry</flux:label>
                <flux:text class="mt-1">{{ $business->industry?->label() ?? 'Not set' }}</flux:text>
            </div>
            <div>
                <flux:label>Business Size</flux:label>
                <flux:text class="mt-1">{{ $business->size ?? 'Not set' }}</flux:text>
            </div>
            <div>
                <flux:label>Business Maturity</flux:label>
                <flux:text class="mt-1">{{ $business->maturity ?? 'Not set' }}</flux:text>
            </div>
            <div>
                <flux:label>Email</flux:label>
                <flux:text class="mt-1">{{ $business->email ?? 'Not set' }}</flux:text>
            </div>
            <div>
                <flux:label>Phone</flux:label>
                <flux:text class="mt-1">{{ $business->phone ?? 'Not set' }}</flux:text>
            </div>
        </div>

        <!-- Location Information -->
        <flux:separator />

        <div>
            <flux:heading size="sm" class="mb-4">Location</flux:heading>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <flux:label>City</flux:label>
                    <flux:text class="mt-1">{{ $business->city ?? 'Not set' }}</flux:text>
                </div>
                <div>
                    <flux:label>State</flux:label>
                    <flux:text class="mt-1">{{ $business->state ?? 'Not set' }}</flux:text>
                </div>
                <div>
                    <flux:label>Postal Code</flux:label>
                    <flux:text class="mt-1">{{ $business->postal_code ?? 'Not set' }}</flux:text>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <flux:separator />

        <div>
            <flux:heading size="sm" class="mb-4">Primary Contact</flux:heading>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <flux:label>Contact Name</flux:label>
                    <flux:text class="mt-1">{{ $business->primary_contact ?? 'Not set' }}</flux:text>
                </div>
                <div>
                    <flux:label>Contact Role</flux:label>
                    <flux:text class="mt-1">{{ $business->contact_role ?? 'Not set' }}</flux:text>
                </div>
            </div>
        </div>

        <!-- Website -->
        @if($business->website)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Website</flux:heading>
                <a href="{{ $business->website }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                    {{ $business->website }}
                </a>
            </div>
        @endif

        <!-- Description -->
        @if($business->description)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Description</flux:heading>
                <flux:text>{{ $business->description }}</flux:text>
            </div>
        @endif

        <!-- Selling Points -->
        @if($business->selling_points)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Selling Points</flux:heading>
                <flux:text>{{ $business->selling_points }}</flux:text>
            </div>
        @endif

        <!-- Target Audience -->
        <flux:separator />

        <div>
            <flux:heading size="sm" class="mb-4">Target Audience</flux:heading>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @if($business->target_age_range)
                    <div>
                        <flux:label>Target Age Range</flux:label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($business->target_age_range as $age)
                                <flux:badge>{{ $age }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($business->target_gender)
                    <div>
                        <flux:label>Target Gender</flux:label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($business->target_gender as $gender)
                                <flux:badge>{{ $gender }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Social Accounts -->
        @if($business->socials && $business->socials->count() > 0)
            <flux:separator />
            <div>
                <flux:heading size="sm" class="mb-4">Social Media Accounts</flux:heading>
                <div class="space-y-2">
                    @foreach($business->socials as $social)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <flux:badge color="zinc">{{ $social->platform }}</flux:badge>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $social->handle ?? $social->url }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Campaigns Stats -->
        <flux:separator />

        <div>
            <flux:heading size="sm" class="mb-4">Campaigns</flux:heading>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 text-center">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $business->campaigns()->count() }}</flux:text>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Total Campaigns</flux:text>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 text-center">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $business->campaigns()->where('status', 'published')->count() }}</flux:text>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Active Campaigns</flux:text>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 text-center">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $business->campaigns()->where('status', 'draft')->count() }}</flux:text>
                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">Draft Campaigns</flux:text>
                </div>
            </div>
        </div>
    </div>
</flux:card>
