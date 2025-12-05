<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-3xl font-bold">Business Settings</h1>
                        <p class="text-green-100 text-lg">
                            Manage your business profile, team members, and preferences
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Business Name Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Business</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white truncate">{{ $user->currentBusiness?->name ?? 'Not Set' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Team Members</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->currentBusiness?->members?->count() ?? 0 }}</dd>
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
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Public Profile</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($username)
                                    <flux:link href="{{ route('business.profile', $username) }}" wire:navigate class="text-blue-600 dark:text-blue-400">View Profile</flux:link>
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
        <form wire:submit="updateBusinessSettings">
            <!-- Business Username -->
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
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Business Username</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Set your unique public profile URL</p>
                    </div>
                </div>

                <flux:field>
                    <flux:label>Business Username</flux:label>
                    <flux:input
                        type="text"
                        wire:model.live.debounce.500ms="username"
                        placeholder="mybusiness" />
                    <flux:error name="username" />
                    @if($username && !$errors->has('username'))
                        <div class="flex items-center gap-2 mt-2 text-sm text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ config('app.url') }}/{{ '@' . $username }} is available!</span>
                        </div>
                    @else
                        <flux:description class="mt-2">Your unique public profile URL will be: <span class="font-mono">{{ config('app.url') }}/{{ '@' . ($username ?: 'your-username') }}</span></flux:description>
                    @endif
                </flux:field>
            </div>

            <!-- Business Images -->
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
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Business Images</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload your logo and banner image</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Business Logo -->
                    <div>
                        <flux:label>Business Logo</flux:label>
                        <div class="mt-2">
                            @if($user->currentBusiness?->getLogoUrl())
                            <div class="mb-4">
                                <img src="{{ $user->currentBusiness->getLogoUrl() }}" alt="Current business logo" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current business logo</p>
                            </div>
                            @endif

                            <div class="flex items-center justify-center w-full">
                                <label for="business_logo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                    </div>
                                    <input id="business_logo" type="file" wire:model="business_logo" class="hidden" accept="image/*" />
                                </label>
                            </div>

                            @if ($business_logo)
                            <div class="mt-4">
                                <img src="{{ $business_logo->temporaryUrl() }}" alt="Logo preview" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">New logo ready to save</p>
                            </div>
                            @endif

                            @error('business_logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Business Banner -->
                    <div>
                        <flux:label>Business Banner</flux:label>
                        <div class="mt-2">
                            @if($user->currentBusiness?->getBannerImageUrl())
                            <div class="mb-4">
                                <img src="{{ $user->currentBusiness->getBannerImageUrl() }}" alt="Current business banner" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current business banner</p>
                            </div>
                            @endif

                            <div class="flex items-center justify-center w-full">
                                <label for="business_banner" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                    </div>
                                    <input id="business_banner" type="file" wire:model="business_banner" class="hidden" accept="image/*" />
                                </label>
                            </div>

                            @if ($business_banner)
                            <div class="mt-4">
                                <img src="{{ $business_banner->temporaryUrl() }}" alt="Banner preview" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">New banner ready to save</p>
                            </div>
                            @endif

                            @error('business_banner') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Business Information -->
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
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Basic Business Information</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Update your business contact details</p>
                    </div>
                </div>

                <!-- Business Name -->
                <flux:field class="mb-4">
                    <flux:label>Business Name *</flux:label>
                    <flux:input
                        type="text"
                        wire:model="business_name"
                        placeholder="Enter your business name"
                        required />
                    <flux:error name="business_name" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Business Email -->
                    <flux:field>
                        <flux:label>Business Email *</flux:label>
                        <flux:input
                            type="email"
                            wire:model="business_email"
                            placeholder="business@example.com"
                            required />
                        <flux:error name="business_email" />
                    </flux:field>

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

                <!-- Website -->
                <flux:field class="mb-4">
                    <flux:label>Website (Optional)</flux:label>
                    <flux:input
                        type="url"
                        wire:model="website"
                        placeholder="https://example.com" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Contact Name -->
                    <flux:field>
                        <flux:label>Primary Contact Name *</flux:label>
                        <flux:input
                            type="text"
                            wire:model="contact_name"
                            placeholder="Primary contact person"
                            required />
                        <flux:error name="contact_name" />
                    </flux:field>

                    <!-- Contact Role -->
                    <flux:field>
                        <flux:label>Contact Role *</flux:label>
                        <flux:select
                            wire:model="contact_role"
                            variant="listbox"
                            placeholder="Select your role"
                            required>
                            @foreach ($contactRoleOptions as $role)
                            <flux:select.option value="{{ $role['value'] }}">{{ $role['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="contact_role" />
                    </flux:field>
                </div>
            </div>

            <!-- Business Profile & Identity -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Business Profile & Identity</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tell us more about your business</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Years in Business -->
                    <flux:field>
                        <flux:label>Years in Business *</flux:label>
                        <flux:select
                            wire:model="years_in_business"
                            variant="listbox"
                            placeholder="Select years in business"
                            required>
                            @foreach ($yearsInBusinessOptions as $years)
                            <flux:select.option value="{{ $years['value'] }}">{{ $years['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="years_in_business" />
                    </flux:field>

                    <!-- Company Size -->
                    <flux:field>
                        <flux:label>Company Size *</flux:label>
                        <flux:select
                            wire:model="company_size"
                            variant="listbox"
                            placeholder="Select company size"
                            required>
                            @foreach ($companySizeOptions as $size)
                            <flux:select.option value="{{ $size['value'] }}">{{ $size['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="company_size" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Business Type -->
                    <flux:field>
                        <flux:label>Business Type *</flux:label>
                        <flux:select
                            wire:model="business_type"
                            variant="listbox"
                            placeholder="Select business type"
                            required>
                            @foreach ($businessTypeOptions as $type)
                            <flux:select.option value="{{ $type['value'] }}">{{ $type['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="business_type" />
                    </flux:field>

                    <!-- Industry -->
                    <flux:field>
                        <flux:label>Primary Industry *</flux:label>
                        <flux:select
                            wire:model="industry"
                            variant="listbox"
                            placeholder="Select your primary industry"
                            required>
                            @foreach ($businessIndustryOptions as $industry)
                            <flux:select.option value="{{ $industry['value'] }}">{{ $industry['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="industry" />
                    </flux:field>
                </div>

                <!-- Business Description -->
                <flux:field class="mb-4">
                    <flux:label>Business Description *</flux:label>
                    <flux:textarea
                        wire:model="business_description"
                        rows="4"
                        placeholder="Describe your business, what you do, and what makes you unique..."
                        required />
                    <flux:error name="business_description" />
                </flux:field>

                <!-- Unique Value Proposition -->
                <flux:field>
                    <flux:label>Unique Value Proposition (Optional)</flux:label>
                    <flux:textarea
                        wire:model="unique_value_proposition"
                        rows="2"
                        placeholder="What sets your business apart from competitors?" />
                </flux:field>
            </div>

            <!-- Location & Target Demographics -->
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
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Location & Target Demographics</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Set your business location and target audience</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- City -->
                    <flux:field>
                        <flux:label>City</flux:label>
                        <flux:input
                            type="text"
                            wire:model="city"
                            placeholder="Your city" />
                    </flux:field>

                    <!-- State -->
                    <flux:field>
                        <flux:label>State</flux:label>
                        <flux:input
                            type="text"
                            wire:model="state"
                            placeholder="Your state" />
                    </flux:field>

                    <!-- Postal Code -->
                    <flux:field>
                        <flux:label>Postal Code</flux:label>
                        <flux:input
                            type="text"
                            wire:model="postal_code"
                            placeholder="12345" />
                    </flux:field>
                </div>

                <!-- Target Gender -->
                <div class="mb-4">
                    <flux:label>Target Gender (Optional)</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Who is your target audience by gender?</p>
                    @foreach($target_gender as $index => $gender)
                    <div class="flex items-center space-x-2 mt-2">
                        <flux:select
                            wire:model="target_gender.{{ $index }}"
                            variant="listbox"
                            placeholder="Select target gender"
                            class="flex-1">
                            <flux:select.option value="male">Male</flux:select.option>
                            <flux:select.option value="female">Female</flux:select.option>
                            <flux:select.option value="non-binary">Non-binary</flux:select.option>
                            <flux:select.option value="all">All Genders</flux:select.option>
                        </flux:select>
                        @if(count($target_gender) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeTargetGender({{ $index }})">
                            Remove
                        </flux:button>
                        @endif
                    </div>
                    @endforeach
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        wire:click="addTargetGender"
                        class="mt-2">
                        + Add Target Gender
                    </flux:button>
                </div>

                <!-- Target Age Range -->
                <div>
                    <flux:label>Target Age Range (Optional)</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">What age groups do you target?</p>
                    @foreach($target_age_range as $index => $ageRange)
                    <div class="flex items-center space-x-2 mt-2">
                        <flux:select
                            wire:model="target_age_range.{{ $index }}"
                            variant="listbox"
                            placeholder="Select age range"
                            class="flex-1">
                            <flux:select.option value="13-17">13-17 (Gen Alpha/Late Gen Z)</flux:select.option>
                            <flux:select.option value="18-24">18-24 (Gen Z)</flux:select.option>
                            <flux:select.option value="25-34">25-34 (Millennials)</flux:select.option>
                            <flux:select.option value="35-44">35-44 (Millennials/Gen X)</flux:select.option>
                            <flux:select.option value="45-54">45-54 (Gen X)</flux:select.option>
                            <flux:select.option value="55-64">55-64 (Boomers)</flux:select.option>
                            <flux:select.option value="65+">65+ (Boomers+)</flux:select.option>
                        </flux:select>
                        @if(count($target_age_range) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeTargetAge({{ $index }})">
                            Remove
                        </flux:button>
                        @endif
                    </div>
                    @endforeach
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        wire:click="addTargetAge"
                        class="mt-2">
                        + Add Age Range
                    </flux:button>
                </div>
            </div>

            <!-- Social Media Accounts -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Social Media Accounts</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Add your social media handles to showcase your online presence</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Instagram Handle -->
                    <flux:field>
                        <flux:label>Instagram Handle</flux:label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                            <flux:input
                                type="text"
                                wire:model="instagram_handle"
                                placeholder="username"
                                class="rounded-l-none" />
                        </div>
                    </flux:field>

                    <!-- Facebook Handle -->
                    <flux:field>
                        <flux:label>Facebook Handle</flux:label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                            <flux:input
                                type="text"
                                wire:model="facebook_handle"
                                placeholder="username"
                                class="rounded-l-none" />
                        </div>
                    </flux:field>

                    <!-- TikTok Handle -->
                    <flux:field>
                        <flux:label>TikTok Handle</flux:label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                            <flux:input
                                type="text"
                                wire:model="tiktok_handle"
                                placeholder="username"
                                class="rounded-l-none" />
                        </div>
                    </flux:field>

                    <!-- LinkedIn Handle -->
                    <flux:field>
                        <flux:label>LinkedIn Handle</flux:label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                            <flux:input
                                type="text"
                                wire:model="linkedin_handle"
                                placeholder="username"
                                class="rounded-l-none" />
                        </div>
                    </flux:field>
                </div>
            </div>

            <!-- Business Goals & Platform Preferences -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Business Goals & Platform Preferences</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Define your influencer marketing objectives</p>
                    </div>
                </div>

                <!-- Business Goals -->
                <div class="mb-4">
                    <flux:label>Business Goals (Optional)</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">What are your primary business goals for influencer marketing?</p>
                    @foreach($business_goals as $index => $goal)
                    <div class="flex items-center space-x-2 mt-2">
                        <flux:select
                            wire:model="business_goals.{{ $index }}"
                            variant="listbox"
                            placeholder="Select a business goal"
                            class="flex-1">
                            <flux:select.option value="brand_awareness">Brand Awareness</flux:select.option>
                            <flux:select.option value="product_promotion">Product Promotion</flux:select.option>
                            <flux:select.option value="growth_scaling">Growth & Scaling</flux:select.option>
                            <flux:select.option value="new_market_entry">New Market Entry</flux:select.option>
                            <flux:select.option value="community_building">Community Building</flux:select.option>
                            <flux:select.option value="customer_retention">Customer Retention</flux:select.option>
                        </flux:select>
                        @if(count($business_goals) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeBusinessGoal({{ $index }})">
                            Remove
                        </flux:button>
                        @endif
                    </div>
                    @endforeach
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        wire:click="addBusinessGoal"
                        class="mt-2">
                        + Add Goal
                    </flux:button>
                </div>

                <!-- Preferred Platforms -->
                <div>
                    <flux:label>Preferred Platforms (Optional)</flux:label>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Which social media platforms do you want to focus on?</p>
                    @foreach($platforms as $index => $platform)
                    <div class="flex items-center space-x-2 mt-2">
                        <flux:select
                            wire:model="platforms.{{ $index }}"
                            variant="listbox"
                            placeholder="Select a platform"
                            class="flex-1">
                            @foreach ($socialPlatformOptions as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @if(count($platforms) > 1)
                        <flux:button
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removePlatform({{ $index }})">
                            Remove
                        </flux:button>
                        @endif
                    </div>
                    @endforeach
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        wire:click="addPlatform"
                        class="mt-2">
                        + Add Platform
                    </flux:button>
                </div>
            </div>

            <!-- Business Members -->
            <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-teal-100 dark:bg-teal-900 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Business Members</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Add team members who can manage this business profile</p>
                    </div>
                </div>

                <!-- Invite form -->
                <div class="flex flex-row items-center mb-6 space-x-2">
                    <flux:input
                        type="email"
                        wire:model="invite_email"
                        placeholder="Enter email to invite"
                        class="flex-1"
                        />
                    <flux:button
                        type="button"
                        variant="primary"
                        wire:click="sendInvite">
                        Send Invite
                    </flux:button>
                </div>

                <div>
                    @foreach($user->currentBusiness->members as $member)
                    <div class="flex items-center justify-between mb-2 p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <div class="flex items-center space-x-3">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $member->email }}</p>
                            </div>

                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Role: {{ $member->pivot->role }}</span>
                            <div class="inline-block ml-4">
                                <button class="text-red-600 hover:text-red-800 text-sm" wire:click="removeMember({{ $member->id }})">Remove</button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach($user->currentBusiness->pendingInvites as $invite)
                    <div class="flex items-center justify-between mb-2 p-2 bg-yellow-50 dark:bg-yellow-900/50 rounded-lg shadow-sm">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $invite->email }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Invited on {{ $invite->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Status: Pending</span>
                            <div class="inline-block ml-4">
                                <button class="text-red-600 hover:text-red-800 text-sm" wire:click="rescindInvite({{ $invite->id }})">Rescind</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Section -->
            <div class="px-6 py-8 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                            Update Business Settings
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
