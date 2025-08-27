<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-3xl font-bold">Edit Profile</h1>
                        <p class="text-blue-100 text-lg">
                            Update your account information and {{ $user->isBusinessAccount() ? 'business' : 'creator' }} profile details
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Account Type Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            @if($user->isBusinessAccount())
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Account Type</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->account_type->label() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Email Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Current Email</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->email }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Status Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Onboarding Status</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                @if($user->hasCompletedOnboarding())
                                    <span class="text-green-600 dark:text-green-400">Complete</span>
                                    <div wire:click="resetOnboarding" class="cursor-pointer text-sm text-blue-600 dark:text-blue-400">Reset Onboarding</div>
                                @else
                                    <span class="text-yellow-600 dark:text-yellow-400">Incomplete</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
        <form wire:submit="updateProfile">
            <!-- Basic Account Information -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Account Information</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Update your basic account details</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <flux:field>
                        <flux:label>Full Name</flux:label>
                        <flux:input
                            type="text"
                            wire:model="name"
                            placeholder="Enter your full name"
                            required />
                        <flux:error name="name" />
                    </flux:field>

                    <!-- Email -->
                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input
                            type="email"
                            wire:model="email"
                            placeholder="Enter your email address"
                            required />
                        <flux:error name="email" />
                    </flux:field>
                </div>
            </div>

            <!-- Password Section -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Change Password</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Leave blank to keep your current password</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Current Password -->
                    <flux:field>
                        <flux:label>Current Password</flux:label>
                        <flux:input
                            type="password"
                            wire:model="current_password"
                            placeholder="Enter your current password" />
                        <flux:error name="current_password" />
                    </flux:field>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- New Password -->
                        <flux:field>
                            <flux:label>New Password</flux:label>
                            <flux:input
                                type="password"
                                wire:model="password"
                                placeholder="Enter new password" />
                            <flux:error name="password" />
                        </flux:field>

                        <!-- Confirm Password -->
                        <flux:field>
                            <flux:label>Confirm Password</flux:label>
                            <flux:input
                                type="password"
                                wire:model="password_confirmation"
                                placeholder="Confirm new password" />
                        </flux:field>
                    </div>
                </div>
            </div>

            <!-- Business Profile Section -->
            @if($user->isBusinessAccount())
                <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Business Profile</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage your business information and preferences</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Business Name -->
                        <flux:field>
                            <flux:label>Business Name</flux:label>
                            <flux:input
                                type="text"
                                wire:model="business_name"
                                placeholder="Enter your business name" />
                        </flux:field>

                        <!-- Industry -->
                        <flux:field>
                            <flux:label>Primary Industry</flux:label>
                            <flux:select
                                wire:model="industry"
                                variant="listbox"
                                placeholder="Select your primary industry">
                                @foreach ($nicheOptions as $niche)
                                    <flux:select.option value="{{ $niche['value'] }}">{{ $niche['label'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <!-- Websites -->
                        <div>
                            <flux:label>Business Websites (Optional)</flux:label>
                            @foreach($websites as $index => $website)
                                <div class="flex items-center space-x-2 mt-2">
                                    <flux:input
                                        type="url"
                                        wire:model="websites.{{ $index }}"
                                        placeholder="https://example.com"
                                        class="flex-1" />
                                    @if(count($websites) > 1)
                                        <flux:button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            wire:click="removeWebsite({{ $index }})">
                                            Remove
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addWebsite"
                                class="mt-2">
                                + Add Another Website
                            </flux:button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Primary Zip Code -->
                            <flux:field>
                                <flux:label>Primary Zip Code</flux:label>
                                <flux:input
                                    type="text"
                                    wire:model="primary_zip_code"
                                    placeholder="Enter zip code" />
                            </flux:field>

                            <!-- Location Count -->
                            <flux:field>
                                <flux:label>Number of Locations</flux:label>
                                <flux:input
                                    type="number"
                                    wire:model.live="location_count"
                                    min="1" />
                            </flux:field>
                        </div>

                        @if($location_count > 1)
                            <div class="space-y-3">
                                <flux:checkbox
                                    wire:model="is_franchise"
                                    label="This is a franchise" />

                                @if($location_count >= 30)
                                    <flux:checkbox
                                        wire:model="is_national_brand"
                                        label="This is a national brand (30+ locations)" />
                                @endif
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contact Name -->
                            <flux:field>
                                <flux:label>Contact Name</flux:label>
                                <flux:input
                                    type="text"
                                    wire:model="contact_name"
                                    placeholder="Primary contact name" />
                            </flux:field>

                            <!-- Contact Email -->
                            <flux:field>
                                <flux:label>Contact Email</flux:label>
                                <flux:input
                                    type="email"
                                    wire:model="contact_email"
                                    placeholder="Primary contact email" />
                            </flux:field>
                        </div>

                        <!-- Collaboration Goals -->
                        <div>
                            <flux:label>Collaboration Goals (Optional)</flux:label>
                            @foreach($collaboration_goals as $index => $goal)
                                <div class="flex items-center space-x-2 mt-2">
                                    <flux:input
                                        type="text"
                                        wire:model="collaboration_goals.{{ $index }}"
                                        placeholder="e.g., Increase local awareness"
                                        class="flex-1" />
                                    @if(count($collaboration_goals) > 1)
                                        <flux:button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            wire:click="removeCollaborationGoal({{ $index }})">
                                            Remove
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addCollaborationGoal"
                                class="mt-2">
                                + Add Goal
                            </flux:button>
                        </div>

                        <!-- Campaign Types -->
                        <div>
                            <flux:label>Preferred Campaign Types (Optional)</flux:label>
                            @foreach($campaign_types as $index => $type)
                                <div class="flex items-center space-x-2 mt-2">
                                    <flux:input
                                        type="text"
                                        wire:model="campaign_types.{{ $index }}"
                                        placeholder="e.g., Product reviews, event promotion"
                                        class="flex-1" />
                                    @if(count($campaign_types) > 1)
                                        <flux:button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            wire:click="removeCampaignType({{ $index }})">
                                            Remove
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addCampaignType"
                                class="mt-2">
                                + Add Type
                            </flux:button>
                        </div>

                        <!-- Team Members -->
                        <div>
                            <flux:label>Team Members (Optional)</flux:label>
                            @foreach($team_members as $index => $member)
                                <div class="flex items-center space-x-2 mt-2">
                                    <flux:input
                                        type="text"
                                        wire:model="team_members.{{ $index }}"
                                        placeholder="Team member name or role"
                                        class="flex-1" />
                                    @if(count($team_members) > 1)
                                        <flux:button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            wire:click="removeTeamMember({{ $index }})">
                                            Remove
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addTeamMember"
                                class="mt-2">
                                + Add Team Member
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Influencer Profile Section -->
            @if($user->isInfluencerAccount())
                <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Creator Profile</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage your creator information and collaboration preferences</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Creator Name -->
                        <flux:field>
                            <flux:label>Creator Name</flux:label>
                            <flux:input
                                type="text"
                                wire:model="creator_name"
                                placeholder="Your preferred creator name" />
                        </flux:field>

                        <!-- Primary Niche -->
                        <flux:field>
                            <flux:label>Primary Content Niche</flux:label>
                            <flux:select
                                wire:model="primary_niche"
                                variant="listbox"
                                placeholder="Select your primary niche">
                                @foreach ($nicheOptions as $niche)
                                    <flux:select.option value="{{ $niche['value'] }}">{{ $niche['label'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Primary Zip Code -->
                            <flux:field>
                                <flux:label>Primary Zip Code</flux:label>
                                <flux:input
                                    type="text"
                                    wire:model="primary_zip_code"
                                    placeholder="Enter zip code" />
                            </flux:field>

                            <!-- Follower Count -->
                            <flux:field>
                                <flux:label>Total Follower Count (Optional)</flux:label>
                                <flux:input
                                    type="text"
                                    wire:model="follower_count"
                                    placeholder="e.g., 10K, 50K, 100K+" />
                            </flux:field>
                        </div>

                        <!-- Media Kit -->
                        <div class="space-y-4">
                            <flux:checkbox
                                wire:model.live="has_media_kit"
                                label="I have a media kit" />

                            @if($has_media_kit)
                                <flux:field>
                                    <flux:label>Media Kit URL</flux:label>
                                    <flux:input
                                        type="url"
                                        wire:model="media_kit_url"
                                        placeholder="https://example.com/media-kit" />
                                </flux:field>
                            @endif
                        </div>

                        <!-- Collaboration Preferences -->
                        <div>
                            <flux:label>Collaboration Preferences (Optional)</flux:label>
                            @foreach($collaboration_preferences as $index => $preference)
                                <div class="flex items-center space-x-2 mt-2">
                                    <flux:input
                                        type="text"
                                        wire:model="collaboration_preferences.{{ $index }}"
                                        placeholder="e.g., Long-term partnerships, product trials"
                                        class="flex-1" />
                                    @if(count($collaboration_preferences) > 1)
                                        <flux:button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            wire:click="removeCollaborationPreference({{ $index }})">
                                            Remove
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addCollaborationPreference"
                                class="mt-2">
                                + Add Preference
                            </flux:button>
                        </div>

                        <!-- Preferred Brands -->
                        <div>
                            <flux:label>Preferred Brand Types (Optional)</flux:label>
                            @foreach($preferred_brands as $index => $brand)
                                <div class="flex items-center space-x-2 mt-2">
                                    <flux:input
                                        type="text"
                                        wire:model="preferred_brands.{{ $index }}"
                                        placeholder="e.g., Local restaurants, fitness brands"
                                        class="flex-1" />
                                    @if(count($preferred_brands) > 1)
                                        <flux:button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            wire:click="removePreferredBrand({{ $index }})">
                                            Remove
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addPreferredBrand"
                                class="mt-2">
                                + Add Brand Type
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Section -->
            <div class="px-6 py-8 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Ready to save your changes?</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">All changes will be saved immediately</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <flux:button
                            type="button"
                            variant="ghost"
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center"
                            icon="arrow-long-left">
                            Cancel
                        </flux:button>

                        <flux:button
                            type="submit"
                            variant="primary"
                            class="inline-flex items-center"
                            icon="check">

                            Update Profile
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>