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
        Add at least one social media account to help businesses understand your reach and engagement. If you provide a username, you must also provide the follower count.
    </flux:description>

    @error('socialAccounts')
        <flux:callout variant="warning" icon="exclamation-triangle" class="dark:bg-yellow-900/20 dark:border-yellow-700">
            <flux:callout.heading class="dark:text-yellow-200">Required</flux:callout.heading>
            <flux:callout.text class="dark:text-yellow-100">{{ $message }}</flux:callout.text>
        </flux:callout>
    @enderror

    <div class="space-y-4">
        @foreach(\App\Enums\SocialPlatform::cases() as $platform)
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $platform->getStyleClasses() }}">
                        {!! $platform->svg('w-5 h-5') !!}
                    </div>
                    <div>
                        <flux:heading class="text-gray-800 dark:text-gray-200">{{ $platform->label() }}</flux:heading>
                        <flux:text class="text-sm text-zinc-500">Connect your {{ $platform->label() }} account</flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Username</flux:label>
                        <flux:input
                            wire:model="socialAccounts.{{ $platform->value }}.username"
                            placeholder="Username (without @)"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>Follower count</flux:label>
                        <flux:input
                            type="number"
                            wire:model="socialAccounts.{{ $platform->value }}.followers"
                            placeholder="Follower count"
                            min="0"
                        />
                        <flux:error name="socialAccounts.{{ $platform->value }}.followers" />
                    </flux:field>
                </div>
            </div>
        @endforeach
    </div>
</div>
