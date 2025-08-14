<!-- Step 3: Platform Preferences & Goals -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">3</span>
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
            <div class="relative">
                <flux:checkbox wire:model="businessGoals" value="{{ $goal->value }}" class="peer sr-only">
                    {{ $goal->label() }}
                </flux:checkbox>
                <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="flex items-center space-x-3">
                        <div class="text-2xl">{{ $goal->icon() }}</div>
                        <div>
                            <div class="font-medium text-gray-700 dark:text-gray-300">{{ $goal->label() }}</div>
                            <div class="text-sm text-gray-500">{{ $goal->description() }}</div>
                        </div>
                    </div>
                </label>
            </div>
            @endforeach
        </div>
        <flux:error name="businessGoals" />
    </div>

    <!-- Target Audience -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Who is your target audience?
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            Help us understand who you want to reach through influencer partnerships.
        </flux:description>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:checkbox.group wire:model="targetAgeRange" label="Target Age Range" variant="buttons">
                    <flux:checkbox value="13-17" label="Gen Z (13-17)" class="w-full"/>
                    <flux:checkbox value="18-24" label="Young Adults (18-24)" class="w-full"/>
                    <flux:checkbox value="25-34" label="Millennials (25-34)" class="w-full"/>
                    <flux:checkbox value="35-44" label="Gen X (35-44)" class="w-full"/>
                    <flux:checkbox value="45-54" label="Middle-aged (45-54)" class="w-full"/>
                    <flux:checkbox value="55+" label="Mature (55+)" class="w-full"/>
                    <flux:checkbox value="all-ages" label="All Ages" class="w-full"/>
                </flux:checkbox.group>
                <flux:error name="targetAgeRange" />
            </flux:field>

            <flux:field>
                <flux:checkbox.group wire:model="targetGender" label="Primary Gender" variant="buttons">
                    <flux:checkbox value="male" label="Male" class="w-full"/>
                    <flux:checkbox value="female" label="Female" class="w-full"/>
                    <flux:checkbox value="non-binary" label="Non-binary" class="w-full"/>
                    <flux:checkbox value="any" label="Any" class="w-full"/>
                </flux:checkbox.group>
                <flux:error name="targetGender" />
            </flux:field>

        </div>
    </div>


</div>