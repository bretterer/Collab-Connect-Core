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
                        <!-- Business Selection -->
                        @if(count($available_businesses) > 1)
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-l-4 border-blue-400">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Select Business to Edit</h3>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mb-4">You belong to multiple businesses. Select which business profile you want to edit.</p>
                                
                                <div class="flex items-center space-x-4">
                                    <flux:select 
                                        wire:model.live="selected_business_id" 
                                        variant="listbox"
                                        class="flex-1">
                                        @foreach($available_businesses as $business)
                                            <flux:select.option value="{{ $business['id'] }}">
                                                {{ $business['name'] }} ({{ ucfirst($business['role']) }})
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:button 
                                        type="button" 
                                        wire:click="switchBusiness"
                                        variant="primary"
                                        size="sm">
                                        Switch
                                    </flux:button>
                                </div>
                            </div>
                        @endif

                        <!-- Business Images -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Business Images</h3>
                            
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
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
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
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
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
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Business Information</h3>
                            
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
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Business Profile & Identity</h3>
                            
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
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location & Target Demographics</h3>
                            
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
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Social Media Accounts</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Add your social media handles to showcase your online presence</p>
                            
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
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Business Goals & Platform Preferences</h3>
                            
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
                                            <flux:select.option value="brand-awareness">Brand Awareness</flux:select.option>
                                            <flux:select.option value="lead-generation">Lead Generation</flux:select.option>
                                            <flux:select.option value="sales-conversion">Sales Conversion</flux:select.option>
                                            <flux:select.option value="customer-acquisition">Customer Acquisition</flux:select.option>
                                            <flux:select.option value="product-launch">Product Launch</flux:select.option>
                                            <flux:select.option value="content-creation">Content Creation</flux:select.option>
                                            <flux:select.option value="social-media-growth">Social Media Growth</flux:select.option>
                                            <flux:select.option value="brand-partnership">Brand Partnership</flux:select.option>
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
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Influencer Profile</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage your influencer profile with all the information collected during onboarding</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Profile Images -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Images</h3>
                            
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
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
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
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
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

                        <!-- Creator Name -->
                        <flux:field>
                            <flux:label>Creator Name</flux:label>
                            <flux:input
                                type="text"
                                wire:model="creator_name"
                                placeholder="Your preferred creator name" />
                        </flux:field>

                        <!-- Bio -->
                        <flux:field>
                            <flux:label>Bio</flux:label>
                            <flux:textarea
                                wire:model="bio"
                                rows="3"
                                placeholder="Tell us about yourself and your content..." />
                            <flux:error name="bio" />
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

                        <!-- Content Types -->
                        <div>
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
                            <flux:error name="content_types" />
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
                            <flux:error name="preferred_business_types" />
                        </div>

                        <!-- Location Information -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location & Contact Information</h3>
                            
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
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- County -->
                                <flux:field>
                                    <flux:label>County (Optional)</flux:label>
                                    <flux:input
                                        type="text"
                                        wire:model="county"
                                        placeholder="Your county" />
                                </flux:field>

                                <!-- Primary Zip Code -->
                                <flux:field>
                                    <flux:label>Postal Code *</flux:label>
                                    <flux:input
                                        type="text"
                                        wire:model="primary_zip_code"
                                        placeholder="12345"
                                        required />
                                    <flux:error name="primary_zip_code" />
                                </flux:field>
                            </div>
                            
                            <!-- Phone Number -->
                            <flux:field class="mt-4">
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
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Social Media Accounts</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Add your social media accounts to showcase your reach</p>
                            
                            @foreach($social_accounts as $platform => $account)
                                @php $platformEnum = \App\Enums\SocialPlatform::from($platform) @endphp
                                <div class="flex items-center space-x-4 mb-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-2 flex-shrink-0">
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

                        <!-- Compensation & Timeline -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Compensation & Timeline Preferences</h3>
                            
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
                                <flux:error name="compensation_types" />
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
                                <flux:error name="typical_lead_time_days" />
                            </flux:field>
                        </div>

                        <!-- Additional Information (Optional) -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information (Optional)</h3>
                            
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