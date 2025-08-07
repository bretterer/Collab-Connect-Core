<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $user->account_type === \App\Enums\AccountType::BUSINESS ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
                       ($user->account_type === \App\Enums\AccountType::INFLUENCER ? 'bg-pink-100 text-pink-800 dark:bg-pink-900/20 dark:text-pink-400' :
                        'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                    {{ $user->account_type->label() }}
                </span>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Member since {{ $user->created_at->format('F j, Y') }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}">
                <flux:button variant="outline" icon="pencil">Edit User</flux:button>
            </a>
        </div>
    </div>

    @if(session('message'))
        <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- User Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Basic Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Account Details -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Account Details</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Status</dt>
                            <dd class="mt-1">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                        Unverified
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Type</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->account_type->label() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Profile Status</dt>
                            <dd class="mt-1">
                                @if($user->hasCompletedOnboarding())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Complete
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                        Incomplete
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('F j, Y g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Profile Specific Details -->
            @if($user->isBusinessAccount() && $user->businessProfile)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Profile</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Business Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->businessProfile->business_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Industry</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->businessProfile->industry }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Primary Location</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->businessProfile->primary_zip_code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location Count</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->businessProfile->location_count }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->businessProfile->contact_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->businessProfile->contact_email }}</dd>
                            </div>
                            @if($user->businessProfile->websites)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Websites</dt>
                                    <dd class="mt-1">
                                        @foreach($user->businessProfile->websites as $website)
                                            <a href="{{ $website }}" target="_blank" class="inline-block text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 mr-2">{{ $website }}</a>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @elseif($user->isInfluencerAccount() && $user->influencerProfile)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Influencer Profile</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            @if($user->influencerProfile->bio)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bio</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->influencerProfile->bio }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Primary Location</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->influencerProfile->primary_zip_code }}</dd>
                            </div>
                            @if($user->influencerProfile->niche_categories)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Niche Categories</dt>
                                    <dd class="mt-1">
                                        @foreach($user->influencerProfile->niche_categories as $category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 mr-1 mb-1">
                                                {{ $category }}
                                            </span>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif
        </div>

        <!-- Stats Sidebar -->
        <div class="space-y-6">
            <!-- Activity Stats -->
            @php $stats = $this->getUserStats(); @endphp
            @if($stats)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Activity Stats</h3>
                        <dl class="space-y-4">
                            @if($user->isBusinessAccount())
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Campaigns</dt>
                                    <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['campaigns_created'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Published Campaigns</dt>
                                    <dd class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['published_campaigns'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Draft Campaigns</dt>
                                    <dd class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['draft_campaigns'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications Received</dt>
                                    <dd class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_applications'] }}</dd>
                                </div>
                            @elseif($user->isInfluencerAccount())
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Applications Submitted</dt>
                                    <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['applications_submitted'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Applications Accepted</dt>
                                    <dd class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['applications_accepted'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Applications Pending</dt>
                                    <dd class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['applications_pending'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Applications Rejected</dt>
                                    <dd class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['applications_rejected'] }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="w-full">
                            <flux:button variant="outline" class="w-full">Edit User Details</flux:button>
                        </a>
                        @if($user->isBusinessAccount())
                            <a href="{{ route('admin.campaigns.index') }}?user={{ $user->id }}" class="w-full">
                                <flux:button variant="ghost" class="w-full">View Campaigns</flux:button>
                            </a>
                        @elseif($user->isInfluencerAccount())
                            <a href="{{ route('admin.campaigns.index') }}?applicant={{ $user->id }}" class="w-full">
                                <flux:button variant="ghost" class="w-full">View Applications</flux:button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
