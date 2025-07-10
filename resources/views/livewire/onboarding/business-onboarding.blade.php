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
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                     style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        @if($currentStep === 1)
            <!-- Step 1: Business Information -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Business Information</h2>

                <div class="space-y-4">
                    <!-- Business Name -->
                    <flux:input
                        type="text"
                        wire:model="businessName"
                        label="Business Name"
                        placeholder="Enter your business name"
                        required />

                    <!-- Industry -->
                    <flux:input
                        type="text"
                        wire:model="industry"
                        label="Industry or Type of Business"
                        placeholder="e.g., Coffee Shop, Salon, Restaurant, Home Service"
                        required />

                    <!-- Websites -->
                    <div>
                        <flux:label>Business Website or Social Media (Optional)</flux:label>
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

                    <!-- Primary Zip Code -->
                    <flux:input
                        type="text"
                        wire:model="primaryZipCode"
                        label="Primary Zip Code or Service Area"
                        placeholder="Enter zip code"
                        required />

                    <!-- Location Count -->
                    <flux:input
                        type="number"
                        wire:model="locationCount"
                        label="How many locations does your business have?"
                        min="1"
                        required />

                    @if($locationCount > 1)
                        <div class="space-y-3">
                            <flux:checkbox
                                wire:model="isFranchise"
                                label="This is a franchise" />

                            @if($locationCount >= 30)
                                <flux:checkbox
                                    wire:model="isNationalBrand"
                                    label="This is a national brand (30+ locations)" />
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        @elseif($currentStep === 2)
            <!-- Step 2: Contact & Billing -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Contact & Billing Information</h2>

                <div class="space-y-4">
                    <!-- Contact Name -->
                    <flux:input
                        type="text"
                        wire:model="contactName"
                        label="Contact Name"
                        placeholder="Primary contact for this account"
                        required />

                    <!-- Contact Email -->
                    <flux:input
                        type="email"
                        wire:model="contactEmail"
                        label="Contact Email"
                        placeholder="Primary email for this account"
                        required />

                    <!-- Subscription Plan -->
                    <div>
                        <flux:label>Subscription Plan</flux:label>
                        <div class="mt-2 space-y-2">
                            @foreach($subscriptionPlanOptions as $value => $label)
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $subscriptionPlan === $value ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input
                                        type="radio"
                                        wire:model="subscriptionPlan"
                                        value="{{ $value }}"
                                        class="text-blue-600" />
                                    <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    @if($subscriptionPlan === 'enterprise')
                        <flux:checkbox
                            wire:model="requestCustomQuote"
                            label="Request a custom quote" />
                    @endif
                </div>
            </div>

        @elseif($currentStep === 3)
            <!-- Step 3: Collaboration Goals -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Collaboration Goals</h2>

                <div class="space-y-6">
                    <!-- Collaboration Goals -->
                    <div>
                        <flux:label>What are your primary goals for using CollabConnect? (Select all that apply)</flux:label>
                        <div class="mt-3 space-y-2">
                            @foreach($collaborationGoalOptions as $value => $label)
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($value, $collaborationGoals) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input
                                        type="checkbox"
                                        wire:model="collaborationGoals"
                                        value="{{ $value }}"
                                        class="text-blue-600" />
                                    <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Campaign Types -->
                    <div>
                        <flux:label>What types of campaigns do you anticipate running? (Select all that apply)</flux:label>
                        <div class="mt-3 space-y-2">
                            @foreach($campaignTypeOptions as $value => $label)
                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($value, $campaignTypes) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                    <input
                                        type="checkbox"
                                        wire:model="campaignTypes"
                                        value="{{ $value }}"
                                        class="text-green-600" />
                                    <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        @elseif($currentStep === 4)
            <!-- Step 4: Team Members -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Team Members</h2>

                <div class="space-y-4">
                    <flux:checkbox
                        wire:model="addTeamMembers"
                        label="Add team members to your account now" />

                    @if($addTeamMembers)
                        <div class="space-y-4">
                            @foreach($teamMembers as $index => $member)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Team Member {{ $index + 1 }}</h4>
                                        @if(count($teamMembers) > 1)
                                            <flux:button
                                                type="button"
                                                variant="danger"
                                                size="sm"
                                                wire:click="removeTeamMember({{ $index }})">
                                                Remove
                                            </flux:button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <flux:input
                                            type="text"
                                            wire:model="teamMembers.{{ $index }}.name"
                                            placeholder="Team member name"
                                            required />
                                        <flux:input
                                            type="email"
                                            wire:model="teamMembers.{{ $index }}.email"
                                            placeholder="team@example.com"
                                            required />
                                    </div>
                                </div>
                            @endforeach

                            <flux:button
                                type="button"
                                variant="ghost"
                                wire:click="addTeamMember">
                                + Add Another Team Member
                            </flux:button>
                        </div>
                    @endif
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
