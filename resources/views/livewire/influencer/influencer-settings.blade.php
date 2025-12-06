<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-3xl font-bold">Influencer Settings</h1>
                        <p class="text-purple-100 text-lg">
                            Manage your creator profile, social accounts, and preferences
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Creator Name Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Creator</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Accounts Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Connected Accounts</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ collect($social_accounts)->filter(fn($a) => !empty($a['username']))->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Public Profile Link Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Public Profile</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($username)
                                    <flux:link href="{{ route('influencer.profile', $username) }}" wire:navigate class="text-blue-600 dark:text-blue-400">View Profile</flux:link>
                                @else
                                    <span class="text-gray-400">Set username below</span>
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
        <form wire:submit="updateInfluencerSettings">
            <!-- Username Section -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Username</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Set your unique public profile URL</p>
                    </div>
                </div>

                <flux:field>
                    <flux:label>Username</flux:label>
                    <flux:input
                        type="text"
                        wire:model.live.debounce.500ms="username"
                        placeholder="yourusername" />
                    <flux:error name="username" />
                    @if($username && !$errors->has('username'))
                        <div class="flex items-center gap-2 mt-2 text-sm text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ config('app.url') }}/@{{ $username }} is available!</span>
                        </div>
                    @else
                        <flux:description class="mt-2">Your unique public profile URL will be: <span class="font-mono">{{ config('app.url') }}/{{ '@' . ($username ?: 'your-username') }}</span></flux:description>
                    @endif
                </flux:field>
            </div>

            <!-- Profile Images -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-pink-100 dark:bg-pink-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Profile Images</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload your profile photo and banner image</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Profile Image -->
                    <div>
                        <flux:label>Profile Image</flux:label>
                        <div class="mt-2">
                            @if($user->influencer?->getProfileImageUrl())
                            <div class="mb-4">
                                <img src="{{ $user->influencer->getProfileImageUrl() }}" alt="Current profile image" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current profile image</p>
                            </div>
                            @endif

                            <div class="flex items-center justify-center w-full">
                                <label for="profile_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                    </div>
                                    <input id="profile_image" type="file" wire:model="profile_image" class="hidden" accept="image/*" />
                                </label>
                            </div>

                            @if ($profile_image)
                            <div class="mt-4">
                                <img src="{{ $profile_image->temporaryUrl() }}" alt="Profile preview" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">New profile image ready to save</p>
                            </div>
                            @endif

                            @error('profile_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Banner Image -->
                    <div>
                        <flux:label>Banner Image</flux:label>
                        <div class="mt-2">
                            @if($user->influencer?->getBannerImageUrl())
                            <div class="mb-4">
                                <img src="{{ $user->influencer->getBannerImageUrl() }}" alt="Current banner image" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current banner image</p>
                            </div>
                            @endif

                            <div class="flex items-center justify-center w-full">
                                <label for="banner_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                    </div>
                                    <input id="banner_image" type="file" wire:model="banner_image" class="hidden" accept="image/*" />
                                </label>
                            </div>

                            @if ($banner_image)
                            <div class="mt-4">
                                <img src="{{ $banner_image->temporaryUrl() }}" alt="Banner preview" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">New banner image ready to save</p>
                            </div>
                            @endif

                            @error('banner_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Creator Profile -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Creator Profile</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tell businesses about yourself and your content</p>
                    </div>
                </div>

                <!-- Bio -->
                <flux:field class="mb-4">
                    <flux:label>Bio</flux:label>
                    <flux:textarea
                        wire:model="bio"
                        rows="4"
                        placeholder="Tell us about yourself and your content..." />
                    <flux:error name="bio" />
                </flux:field>

                <!-- Primary Industry -->
                <flux:field class="mb-4">
                    <flux:label>Primary Industry</flux:label>
                    <flux:select
                        wire:model="primary_industry"
                        variant="listbox"
                        placeholder="Select your primary industry">
                        @foreach ($businessIndustryOptions as $option)
                        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <!-- Content Types -->
                <div class="mb-4">
                    <flux:label>Content Types (Select up to 3)</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">What types of content do you create?</p>
                    @foreach($content_types as $index => $contentType)
                    <div class="flex items-center space-x-2 mt-2">
                        <flux:select
                            wire:model="content_types.{{ $index }}"
                            variant="listbox"
                            placeholder="Select content type"
                            class="flex-1">
                            @foreach ($businessIndustryOptions as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @if(count($content_types) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeContentType({{ $index }})">
                            Remove
                        </flux:button>
                        @endif
                    </div>
                    @endforeach
                    @if(count($content_types) < 3)
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        wire:click="addContentType"
                        class="mt-2">
                        + Add Content Type
                    </flux:button>
                    @endif
                </div>

                <!-- Preferred Business Types -->
                <div>
                    <flux:label>Preferred Business Types (Select up to 2)</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">What types of businesses do you prefer to work with?</p>
                    @foreach($preferred_business_types as $index => $businessType)
                    <div class="flex items-center space-x-2 mt-2">
                        <flux:select
                            wire:model="preferred_business_types.{{ $index }}"
                            variant="listbox"
                            placeholder="Select business type"
                            class="flex-1">
                            @foreach ($businessTypeOptions as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @if(count($preferred_business_types) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeBusinessType({{ $index }})">
                            Remove
                        </flux:button>
                        @endif
                    </div>
                    @endforeach
                    @if(count($preferred_business_types) < 2)
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        wire:click="addBusinessType"
                        class="mt-2">
                        + Add Business Type
                    </flux:button>
                    @endif
                </div>
            </div>

            <!-- Location & Contact Information -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Location & Contact Information</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Where are you located? This helps match you with local businesses.</p>
                    </div>
                </div>

                <!-- Address -->
                <flux:field class="mb-4">
                    <flux:label>Street Address (Optional)</flux:label>
                    <flux:input
                        type="text"
                        wire:model="address"
                        placeholder="123 Main St" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- City -->
                    <flux:field>
                        <flux:label>City *</flux:label>
                        <flux:input
                            type="text"
                            wire:model="city"
                            placeholder="Your city"
                            required />
                        <flux:error name="city" />
                    </flux:field>

                    <!-- State -->
                    <flux:field>
                        <flux:label>State *</flux:label>
                        <flux:input
                            type="text"
                            wire:model="state"
                            placeholder="Your state"
                            required />
                        <flux:error name="state" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- County -->
                    <flux:field>
                        <flux:label>County (Optional)</flux:label>
                        <flux:input
                            type="text"
                            wire:model="county"
                            placeholder="Your county" />
                    </flux:field>

                    <!-- Postal Code -->
                    <flux:field>
                        <flux:label>Postal Code *</flux:label>
                        <flux:input
                            type="text"
                            wire:model="postal_code"
                            placeholder="12345"
                            required />
                        <flux:error name="postal_code" />
                    </flux:field>
                </div>

                <!-- Phone Number -->
                <flux:field>
                    <flux:label>Phone Number *</flux:label>
                    <flux:input
                        type="tel"
                        wire:model="phone_number"
                        placeholder="(555) 123-4567"
                        required />
                    <flux:error name="phone_number" />
                </flux:field>
            </div>

            <!-- Social Media Accounts -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Social Media Accounts</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Connect your social media accounts to showcase your reach</p>
                    </div>
                </div>

                @foreach($social_accounts as $platform => $account)
                @php $platformEnum = \App\Enums\SocialPlatform::from($platform) @endphp
                <div class="flex items-center space-x-4 mb-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex items-center space-x-2 flex-shrink-0 w-32">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $platformEnum->getStyleClasses() }}">
                            {!! $platformEnum->svg('w-4 h-4') !!}
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $platformEnum->label() }}</span>
                    </div>

                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input
                            type="text"
                            wire:model="social_accounts.{{ $platform }}.username"
                            placeholder="Username (without @)"
                            class="flex-1" />
                        <flux:input
                            type="number"
                            wire:model="social_accounts.{{ $platform }}.followers"
                            placeholder="Follower count"
                            class="flex-1" />
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Compensation & Timeline Preferences -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Compensation & Timeline Preferences</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Set your compensation preferences and typical turnaround time</p>
                    </div>
                </div>

                <!-- Compensation Types -->
                <div class="mb-4">
                    <flux:label>Preferred Compensation Types (Select up to 3)</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">What types of compensation do you prefer?</p>
                    @foreach($compensation_types as $index => $compensationType)
                    <div class="flex items-center space-x-2 mt-2">
                        <flux:select
                            wire:model="compensation_types.{{ $index }}"
                            variant="listbox"
                            placeholder="Select compensation type"
                            class="flex-1">
                            @foreach ($compensationTypeOptions as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @if(count($compensation_types) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeCompensationType({{ $index }})">
                            Remove
                        </flux:button>
                        @endif
                    </div>
                    @endforeach
                    @if(count($compensation_types) < 3)
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        wire:click="addCompensationType"
                        class="mt-2">
                        + Add Compensation Type
                    </flux:button>
                    @endif
                </div>

                <!-- Typical Lead Time -->
                <flux:field>
                    <flux:label>Typical Lead Time (Days) *</flux:label>
                    <flux:input
                        type="number"
                        wire:model="typical_lead_time_days"
                        placeholder="How many days do you typically need?"
                        min="1"
                        max="365"
                        required />
                    <flux:description>How much advance notice do you typically need for a collaboration?</flux:description>
                    <flux:error name="typical_lead_time_days" />
                </flux:field>
            </div>

            <!-- Additional Information -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Additional Information (Optional)</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Add more details to help businesses understand your preferences</p>
                    </div>
                </div>

                <!-- Media Kit -->
                <div class="space-y-4 mb-6">
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
                <div class="mb-6">
                    <flux:label>Collaboration Preferences</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">What types of collaborations do you prefer?</p>
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
                    <flux:label>Preferred Brand Types</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">What types of brands do you prefer to work with?</p>
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

            <!-- Submit Section -->
            <div class="px-6 py-8 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            Update Influencer Settings
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
