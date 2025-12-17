<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data
    @update-url.window="window.history.replaceState({}, '', $event.detail.url)"
>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.businesses.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Business</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Manage business profile, subscription, and billing for <strong>{{ $business->name }}</strong>.
                    </p>
                </div>
            </div>

            @php
                $owner = $business->owner()->first();
            @endphp
            @if($owner)
                <a href="{{ route('admin.users.show', $owner) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <flux:icon name="user" class="w-4 h-4" />
                    View Owner: {{ $owner->name }}
                </a>
            @endif
        </div>
    </div>

    <!-- Navigation Tabs -->
    <flux:navbar class="mb-6 -mx-4 border-b border-gray-200 dark:border-gray-700">
        <flux:navbar.item
            wire:click="setActiveTab('profile')"
            icon="building-office"
            :current="$activeTab === 'profile'">
            Profile
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('billing')"
            icon="credit-card"
            :current="$activeTab === 'billing'">
            Billing & Credits
        </flux:navbar.item>
    </flux:navbar>

    <!-- Tab Content -->
    @if($activeTab === 'profile')
        <livewire:admin.businesses.tabs.profile-tab :business="$business" :key="'profile-'.$business->id" />
    @endif

    @if($activeTab === 'billing')
        <livewire:admin.businesses.tabs.billing-tab :business="$business" :key="'billing-'.$business->id" />
    @endif
</div>
