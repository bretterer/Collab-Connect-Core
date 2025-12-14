<!-- Step 4: Platform Preferences & Goals -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">4</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Platform Preferences & Goals
        </flux:heading>
    </div>

    <!-- Business Location -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Where is your business located?
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            This helps us find local influencers and understand your market.
        </flux:description>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:field>
                <flux:label>City</flux:label>
                <flux:input
                    wire:model="city"
                    placeholder="Enter your city"
                />
                <flux:error name="city" />
            </flux:field>

            <flux:field>
                <flux:label>State/Province</flux:label>
                <flux:input
                    wire:model="state"
                    placeholder="Enter your state"
                />
                <flux:error name="state" />
            </flux:field>

            <flux:field>
                <flux:label>Postal Code</flux:label>
                <flux:input
                    wire:model="postalCode"
                    placeholder="Enter postal code"
                />
                <flux:error name="postalCode" />
            </flux:field>
        </div>
    </div>

    <!-- Business Goals -->
    <div class="space-y-6">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            What are your main goals with influencer marketing?
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            Select all that apply to help us understand how to best support your business.
        </flux:description>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach(App\Enums\BusinessGoal::cases() as $goal)
            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                         {{ in_array($goal->value, $businessGoals)
                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-sm'
                            : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <input type="checkbox"
                       wire:model.live="businessGoals"
                       value="{{ $goal->value }}"
                       class="sr-only">
                <div class="flex items-center space-x-3 w-full">
                    <div class="text-2xl">{{ $goal->icon() }}</div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-700 dark:text-gray-300">{{ $goal->label() }}</div>
                        <div class="text-sm text-gray-500">{{ $goal->description() }}</div>
                    </div>
                    <div class="ml-auto">
                        @if(in_array($goal->value, $businessGoals))
                            <div class="text-blue-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>

                        @endif
                    </div>
                </div>
            </label>
            @endforeach
        </div>
        <flux:error name="businessGoals" />
    </div>

    <!-- Platform Preferences -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Which social media platforms are you most interested in?
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            Select the platforms where you'd like to connect with influencers.
        </flux:description>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach(App\Enums\SocialPlatform::forBusinesses() as $platform)
            <label class="flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200
                         {{ in_array($platform->value, $platforms)
                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-sm'
                            : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <input type="checkbox"
                       wire:model.live="platforms"
                       value="{{ $platform->value }}"
                       class="sr-only">
                <div class="text-center">
                    <div class="mb-2 flex justify-center text-gray-700 dark:text-gray-300">{!! $platform->svg('w-8 h-8') !!}</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $platform->label() }}</div>
                    @if(in_array($platform->value, $platforms))
                        <div class="mt-2 text-blue-500">
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @endif
                </div>
            </label>
            @endforeach
        </div>
        <flux:error name="platforms" />
    </div>

</div>
