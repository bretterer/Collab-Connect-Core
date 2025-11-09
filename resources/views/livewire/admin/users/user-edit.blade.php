<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Update user account information, profile details, and feature access.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <flux:tab.group wire:model.live="activeTab">
        <flux:tabs class="mb-6">
            <flux:tab name="account">Account Details</flux:tab>
            <flux:tab name="profile">Profile Information</flux:tab>
            <flux:tab name="features">Feature Flags</flux:tab>
        </flux:tabs>

        <!-- Account Details Tab -->
        <flux:tab.panel name="account">
            <flux:card>
                <form wire:submit="save">
                    <div class="space-y-6">
                        <!-- Name -->
                        <flux:field>
                            <flux:label>Full Name</flux:label>
                            <flux:input wire:model="name" />
                            <flux:error name="name" />
                        </flux:field>

                        <!-- Email -->
                        <flux:field>
                            <flux:label>Email Address</flux:label>
                            <flux:input type="email" wire:model="email" />
                            <flux:error name="email" />
                        </flux:field>

                        <!-- Account Type -->
                        <flux:field>
                            <flux:label>Account Type</flux:label>
                            <flux:select wire:model="accountType">
                                @foreach($this->getAccountTypeOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="accountType" />
                            <flux:description>
                                <span class="text-yellow-600 dark:text-yellow-400 font-medium">Warning:</span> Changing the account type may affect the user's access to features and existing data.
                            </flux:description>
                        </flux:field>

                        <!-- Access Admin Panel -->
                        <div class="flex items-center space-x-3">
                            <flux:checkbox wire:model="allowAdminAccess" />
                            <flux:label>Allow this user to access the admin panel</flux:label>
                        </div>

                        <flux:separator />

                        <!-- Current Profile Status -->
                        <div>
                            <flux:label>Current Profile Status</flux:label>
                            <div class="mt-2">
                                @if($user->hasCompletedOnboarding())
                                    <flux:badge color="green" size="lg">Profile Complete</flux:badge>
                                @else
                                    <flux:badge color="yellow" size="lg">Profile Incomplete</flux:badge>
                                @endif
                            </div>
                        </div>

                        <!-- Email Verification Status -->
                        <div>
                            <flux:label>Email Verification Status</flux:label>
                            <div class="mt-2">
                                @if($user->email_verified_at)
                                    <div class="flex items-center space-x-2">
                                        <flux:badge color="green" size="lg">
                                            <flux:icon name="check-circle" class="w-4 h-4" />
                                            Verified on {{ $user->email_verified_at->format('M j, Y') }}
                                        </flux:badge>
                                    </div>
                                @else
                                    <flux:badge color="red" size="lg">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4" />
                                        Not Verified
                                    </flux:badge>
                                @endif
                            </div>
                        </div>

                        <!-- Member Since -->
                        <div>
                            <flux:label>Member Since</flux:label>
                            <flux:text class="mt-2">
                                {{ $user->created_at->format('F j, Y g:i A') }} ({{ $user->created_at->diffForHumans() }})
                            </flux:text>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <flux:button variant="ghost" href="{{ route('admin.users.show', $user) }}">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Save Changes
                        </flux:button>
                    </div>
                </form>
            </flux:card>
        </flux:tab.panel>

        <!-- Profile Information Tab -->
        <flux:tab.panel name="profile">
            <flux:card>
                @if($user->isBusinessAccount() && $user->currentBusiness)
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Business Profile</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                                View business information and profile details.
                            </flux:text>
                        </div>

                        <flux:separator />

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <flux:label>Business Name</flux:label>
                                <flux:text class="mt-1">{{ $user->currentBusiness->name }}</flux:text>
                            </div>
                            <div>
                                <flux:label>Industry</flux:label>
                                <flux:text class="mt-1">{{ $user->currentBusiness->industry }}</flux:text>
                            </div>
                            <div>
                                <flux:label>Primary Location</flux:label>
                                <flux:text class="mt-1">{{ $user->currentBusiness->primary_zip_code }}</flux:text>
                            </div>
                            <div>
                                <flux:label>Location Count</flux:label>
                                <flux:text class="mt-1">{{ $user->currentBusiness->location_count }}</flux:text>
                            </div>
                            <div>
                                <flux:label>Contact Name</flux:label>
                                <flux:text class="mt-1">{{ $user->currentBusiness->contact_name }}</flux:text>
                            </div>
                            <div>
                                <flux:label>Contact Email</flux:label>
                                <flux:text class="mt-1">{{ $user->currentBusiness->email }}</flux:text>
                            </div>
                        </div>

                        @if($user->currentBusiness->websites)
                            <div>
                                <flux:label>Websites</flux:label>
                                <div class="mt-2 space-y-1">
                                    @foreach($user->currentBusiness->websites as $website)
                                        <div>
                                            <a href="{{ $website }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                                                {{ $website }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($user->currentBusiness->description)
                            <div>
                                <flux:label>Description</flux:label>
                                <flux:text class="mt-2">{{ $user->currentBusiness->description }}</flux:text>
                            </div>
                        @endif
                    </div>
                @elseif($user->isInfluencerAccount() && $user->influencer)
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Influencer Profile</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                                View influencer information and profile details.
                            </flux:text>
                        </div>

                        <flux:separator />

                        @if($user->influencer->bio)
                            <div>
                                <flux:label>Bio</flux:label>
                                <flux:text class="mt-2">{{ $user->influencer->bio }}</flux:text>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <flux:label>Primary Location</flux:label>
                                <flux:text class="mt-1">{{ $user->influencer->primary_zip_code }}</flux:text>
                            </div>

                            @if($user->influencer->niche_categories)
                                <div>
                                    <flux:label>Niche Categories</flux:label>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($user->influencer->niche_categories as $category)
                                            <flux:badge>{{ $category }}</flux:badge>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($user->socialMediaAccounts->count() > 0)
                            <div>
                                <flux:label>Social Media Accounts</flux:label>
                                <div class="mt-2 space-y-2">
                                    @foreach($user->socialMediaAccounts as $account)
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
                    </div>
                @else
                    <div class="text-center py-12">
                        <flux:icon name="user" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <flux:heading size="lg">No Profile Information</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mt-2">
                            This user has not completed their profile setup yet.
                        </flux:text>
                    </div>
                @endif
            </flux:card>
        </flux:tab.panel>

        <!-- Feature Flags Tab -->
        <flux:tab.panel name="features">
            <flux:card class="mb-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Search -->
                    <flux:field>
                        <flux:label>Search Features</flux:label>
                        <flux:input
                            wire:model.live.debounce.300ms="featureSearch"
                            placeholder="Search by feature name..."
                        />
                    </flux:field>

                    <!-- Status Filter -->
                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model.live="featureStatusFilter">
                            <option value="">All Features</option>
                            <option value="enabled">Enabled Only</option>
                            <option value="disabled">Disabled Only</option>
                        </flux:select>
                    </flux:field>
                </div>
            </flux:card>

            <flux:card>
                <div class="space-y-1">
                    @forelse($this->getFilteredFeatures() as $feature)
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-900/50 rounded-lg transition-colors">
                            <div class="flex-1 min-w-0 mr-4">
                                <div class="flex items-center space-x-3">
                                    <flux:heading size="sm">{{ $feature->title }}</flux:heading>
                                    @if($this->isFeatureEnabled($feature->key))
                                        <flux:badge color="green" size="sm">Enabled</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Disabled</flux:badge>
                                    @endif
                                </div>
                                @if($feature->description)
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $feature->description }}
                                    </flux:text>
                                @endif

                            </div>
                            <div class="flex-shrink-0">
                                <flux:switch
                                    wire:model="features.{{ $feature->key }}"
                                    wire:change="toggleFeature('{{ $feature->key }}')"
                                />
                            </div>
                        </div>
                        @if(!$loop->last)
                            <flux:separator />
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <flux:icon name="flag" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                            <flux:heading size="lg">No Features Found</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mt-2">
                                @if($featureSearch)
                                    No features match your search criteria.
                                @else
                                    No feature flags are configured for this application.
                                @endif
                            </flux:text>
                        </div>
                    @endforelse
                </div>
            </flux:card>
        </flux:tab.panel>
    </flux:tab.group>
</div>
