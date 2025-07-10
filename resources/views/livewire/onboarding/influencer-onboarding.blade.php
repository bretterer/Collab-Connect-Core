<div>
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="text-center">
                <img class="block h-8 w-auto mx-auto dark:hidden"
                     src="{{ Vite::asset('resources/images/CollabConnectLogo.png') }}"
                     alt="CollabConnect Logo" />
                <img class="hidden h-8 w-auto mx-auto dark:block"
                     src="{{ Vite::asset('resources/images/CollabConnectLogoDark.png') }}"
                     alt="CollabConnect Logo" />
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <span>Step {{ $currentStep }} of {{ $totalSteps }}</span>
            </div>
        </div>

        <div class="mt-4">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300"
                     style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        @if($currentStep === 1)
            <!-- Step 1: Profile Information -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Profile Information</h2>

                <div class="space-y-4">
                    <!-- Creator Name -->
                    <flux:input
                        type="text"
                        wire:model="creatorName"
                        label="Full Name or Preferred Creator Name"
                        placeholder="Enter your name or creator name"
                        required />

                    <!-- Primary Niche -->
                    <div>
                        <flux:label>Primary Content Niche/Interest</flux:label>
                        <select wire:model="primaryNiche"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-800 dark:text-white">
                            <option value="">Select your primary niche</option>
                            @foreach($nicheOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Primary Zip Code -->
                    <flux:input
                        type="text"
                        wire:model="primaryZipCode"
                        label="Primary Zip Code or Location"
                        placeholder="Enter your location"
                        required />
                </div>
            </div>

        @elseif($currentStep === 2)
            <!-- Step 2: Social Media Connections -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Social Media Connections</h2>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Connect your social media accounts where you primarily create content.
                    </p>

                    @foreach($socialMediaAccounts as $index => $account)
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    Account {{ $index + 1 }}
                                    @if($account['is_primary'])
                                        <span class="ml-2 px-2 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-200 text-xs rounded-full">Primary</span>
                                    @endif
                                </h4>
                                <div class="flex items-center space-x-2">
                                    @if(!$account['is_primary'])
                                        <flux:button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            wire:click="setPrimaryAccount({{ $index }})">
                                            Set as Primary
                                        </flux:button>
                                    @endif
                                    @if(count($socialMediaAccounts) > 1)
                                        <flux:button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            wire:click="removeSocialMediaAccount({{ $index }})">
                                            Remove
                                        </flux:button>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Platform -->
                                <div>
                                    <flux:label>Platform</flux:label>
                                    <select wire:model="socialMediaAccounts.{{ $index }}.platform"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-800 dark:text-white">
                                        @foreach($platformOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Username -->
                                <flux:input
                                    type="text"
                                    wire:model="socialMediaAccounts.{{ $index }}.username"
                                    label="Username/Handle"
                                    placeholder="username"
                                    required />

                                <!-- Follower Count -->
                                <flux:input
                                    type="number"
                                    wire:model="socialMediaAccounts.{{ $index }}.follower_count"
                                    label="Approximate Follower Count"
                                    min="0"
                                    required />
                            </div>
                        </div>
                    @endforeach

                    <flux:button
                        type="button"
                        variant="ghost"
                        wire:click="addSocialMediaAccount">
                        + Add Another Social Media Account
                    </flux:button>
                </div>
            </div>

        @elseif($currentStep === 3)
            <!-- Step 3: Media Kit & Portfolio -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Media Kit & Portfolio</h2>

                <div class="space-y-4">
                    <flux:checkbox
                        wire:model="hasMediaKit"
                        label="I have an existing media kit or portfolio" />

                    @if($hasMediaKit)
                        <flux:input
                            type="url"
                            wire:model="mediaKitUrl"
                            label="Media Kit or Portfolio URL"
                            placeholder="https://example.com/media-kit"
                            required />
                    @else
                        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                            <flux:checkbox
                                wire:model="wantMediaKitBuilder"
                                label="I'd like to use the CollabConnect media kit builder" />
                            <p class="mt-2 text-sm text-purple-700 dark:text-purple-300">
                                Don't worry! You can create a professional media kit after completing your profile setup.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($currentStep === 4)
            <!-- Step 4: Collaboration Preferences & Pricing -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Collaboration Preferences & Pricing</h2>

                <div class="space-y-6">
                    <!-- Collaboration Preferences -->
                    <div>
                        <flux:label>What types of collaborations are you interested in? (Select all that apply)</flux:label>
                        <div class="mt-3 space-y-2">
                            @foreach($collaborationPreferenceOptions as $value => $label)
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($value, $collaborationPreferences) ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input
                                        type="checkbox"
                                        wire:model="collaborationPreferences"
                                        value="{{ $value }}"
                                        class="text-purple-600" />
                                    <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preferred Brands -->
                    <div>
                        <flux:input
                            type="text"
                            wire:model="preferredBrands"
                            label="Preferred Brands or Industries (Optional)"
                            placeholder="e.g., Local restaurants, beauty brands, fitness companies" />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            List any specific brands or industries you prefer to work with
                        </p>
                    </div>

                    <!-- Subscription Plan -->
                    <div>
                        <flux:label>Select Your Subscription Plan</flux:label>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-3">
                            Based on your total follower count across all platforms
                        </p>
                        <div class="space-y-2">
                            @foreach($subscriptionPlanOptions as $value => $label)
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $subscriptionPlan === $value ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input
                                        type="radio"
                                        wire:model="subscriptionPlan"
                                        value="{{ $value }}"
                                        class="text-purple-600" />
                                    <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            @if($currentStep > 1)
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="previousStep">
                    ← Previous
                </flux:button>
            @else
                <div></div>
            @endif

            @if($currentStep < $totalSteps)
                <flux:button
                    type="button"
                    variant="primary"
                    wire:click="nextStep">
                    Next →
                </flux:button>
            @else
                <flux:button
                    type="button"
                    variant="primary"
                    wire:click="completeOnboarding">
                    Complete Setup
                </flux:button>
            @endif
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="text-sm text-red-600 dark:text-red-400">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
