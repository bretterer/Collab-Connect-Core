<!-- Step 2: Social Media Accounts -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">2</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Social Media Accounts
        </flux:heading>
    </div>

    <flux:description>
        Add your social media accounts to help businesses understand your reach and engagement. You can leave platforms blank if you don't use them.
    </flux:description>

    <div class="space-y-6">
        @foreach(\App\Enums\SocialPlatform::cases() as $platform)
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="text-2xl">{{ $platform->getIcon() }}</span>
                    <flux:heading class="text-gray-800 dark:text-gray-200">
                        {{ $platform->label() }}
                    </flux:heading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Username</flux:label>
                        <flux:input
                            wire:model="socialAccounts.{{ $platform->value }}.username"
                            placeholder="Enter your {{ $platform->label() }} username"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>Followers</flux:label>
                        <flux:input
                            type="number"
                            wire:model="socialAccounts.{{ $platform->value }}.followers"
                            placeholder="Number of followers"
                            min="0"
                        />
                        <flux:description>
                            Optional: Help businesses understand your reach
                        </flux:description>
                    </flux:field>
                </div>
            </div>
        @endforeach
    </div>
</div>