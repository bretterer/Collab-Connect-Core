<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data
    @update-url.window="window.history.replaceState({}, '', $event.detail.url)"
>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Update user account information, profile details, and feature access.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <flux:navbar class="mb-6 -mx-4 border-b border-gray-200 dark:border-gray-700">
        <flux:navbar.item
            wire:click="setActiveTab('account')"
            icon="user"
            :current="$activeTab === 'account'">
            Account Details
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('profile')"
            icon="identification"
            :current="$activeTab === 'profile'">
            Profile Information
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('features')"
            icon="flag"
            :current="$activeTab === 'features'">
            Feature Flags
        </flux:navbar.item>
    </flux:navbar>

    <!-- Profile Link Callout -->
    @if($user->isBusinessAccount() && $user->currentBusiness)
        <flux:callout icon="building-office" class="mb-6">
            <flux:callout.heading>Business Profile</flux:callout.heading>
            <flux:callout.text>
                Billing, subscriptions, and credits are managed at the business level.
            </flux:callout.text>
            <x-slot:actions>
                <flux:button href="{{ route('admin.businesses.edit', ['business' => $user->currentBusiness, 'tab' => 'billing']) }}" variant="primary" size="sm" icon="arrow-right">
                    Manage Business Billing
                </flux:button>
            </x-slot:actions>
        </flux:callout>
    @elseif($user->isInfluencerAccount() && $user->influencer)
        <flux:callout icon="user" class="mb-6">
            <flux:callout.heading>Influencer Profile</flux:callout.heading>
            <flux:callout.text>
                Billing, subscriptions, and credits are managed at the influencer profile level.
            </flux:callout.text>
            <x-slot:actions>
                <flux:button href="{{ route('admin.influencers.edit', ['influencer' => $user->influencer, 'tab' => 'billing']) }}" variant="primary" size="sm" icon="arrow-right">
                    Manage Influencer Billing
                </flux:button>
            </x-slot:actions>
        </flux:callout>
    @endif

    <!-- Tab Content -->
    @if($activeTab === 'account')
        <livewire:admin.users.tabs.account-tab :user="$user" :key="'account-'.$user->id" />
    @endif

    @if($activeTab === 'profile')
        <livewire:admin.users.tabs.profile-tab :user="$user" :key="'profile-'.$user->id" />
    @endif

    @if($activeTab === 'features')
        <livewire:admin.users.tabs.features-tab :user="$user" :key="'features-'.$user->id" />
    @endif
</div>
