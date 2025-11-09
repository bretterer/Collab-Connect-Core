<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-900 px-6 pb-4 ring-1 ring-gray-900/10 dark:ring-gray-800">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <div x-show="sidebarExpanded || window.innerWidth < 1024" x-transition class="flex items-center space-x-3">
            <img class="block h-8 w-auto dark:hidden"
                src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
                alt="CollabConnect Logo" />
            <img class="hidden h-8 w-auto dark:block"
                src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
                alt="CollabConnect Logo" />
        </div>
        <div x-show="!sidebarExpanded && window.innerWidth >= 1024" class="flex items-center justify-center mx-auto">
            <img class="h-auto w-10" src="{{ Vite::asset('resources/images/CollabConnectMark.png') }}" alt="CollabConnect Icon" />
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <!-- Main Navigation -->
                <ul role="list" class="-mx-2 space-y-1">


                    @if(auth()->user()->account_type === App\Enums\AccountType::BUSINESS && request()->routeIs('admin.*') === false)
                    <!-- Business Navigation -->

                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Dashboard</span>
                        </a>
                    </li>

                    <!-- Campaigns -->
                    <li>
                        <a href="{{ route('campaigns.index') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('campaigns.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('campaigns.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Campaigns</span>
                        </a>
                    </li>

                    <!-- Applications -->
                    <li>
                        <a href="{{ route('applications.index') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('applications.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('applications.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Applications</span>
                        </a>
                    </li>

                    <!-- Find Influencers -->
                    <li>
                        <a href="{{ route('search') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('search') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('search') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Find Influencers</span>
                        </a>
                    </li>

                    <!-- Referrals -->
                    <li>
                        <a href="{{ route('referral.index') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('referral.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('referral.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Referrals</span>
                        </a>
                    </li>

                    @elseif(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER && request()->routeIs('admin.*') === false)
                    <!-- Influencer Navigation -->

                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Dashboard</span>
                        </a>
                    </li>

                    <!-- Discover -->
                    <li>
                        <a href="{{ route('discover') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('discover') ? 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-400' : 'text-gray-700 dark:text-gray-300 hover:text-pink-700 dark:hover:text-pink-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('discover') ? 'text-pink-700 dark:text-pink-400' : 'text-gray-400 group-hover:text-pink-700 dark:group-hover:text-pink-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Discover</span>
                        </a>
                    </li>

                    <!-- Find Businesses -->
                    <li>
                        <a href="{{ route('search') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('search') ? 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-400' : 'text-gray-700 dark:text-gray-300 hover:text-pink-700 dark:hover:text-pink-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('search') ? 'text-pink-700 dark:text-pink-400' : 'text-gray-400 group-hover:text-pink-700 dark:group-hover:text-pink-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Find Businesses</span>
                        </a>
                    </li>

                    <!-- Referrals -->
                    <li>
                        <a href="{{ route('referral.index') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('referral.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('referral.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Referrals</span>
                        </a>
                    </li>

                    @elseif(auth()->user()->isAdmin())
                    <!-- Admin Navigation -->

                    @if(auth()->user()->account_type !== App\Enums\AccountType::ADMIN)
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <!-- Back Arrow Icon -->
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Back to App</span>
                        </a>
                    </li>
                    @endif

                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Admin Dashboard</span>
                        </a>
                    </li>
                    <!-- Users -->
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5zM12 21a9 9 0 100-18 9 9 0 000 18zM12 18a6 6 0 110-12 6 6 0 010 12z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Users</span>
                        </a>
                    </li>

                    <!-- Beta Invites -->
                    <li>
                        <a href="{{ route('admin.beta-invites') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.beta-invites') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.beta-invites') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5zM12 21a9 9 0 100-18 9 9 0 000 18zM12 18a6 6 0 110-12 6 6 0 010 12z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Beta Invites</span>
                        </a>
                    </li>

                        <!-- Feedback -->
                        <li>
                            <a href="{{ route('admin.feedback') }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.feedback') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.feedback') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                                </svg>
                                <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Feedback</span>
                            </a>
                        </li>

                        <!-- Markets -->
                        <li>
                            <a href="{{ route('admin.markets.index') }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.markets.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.markets.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                                </svg>
                                <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Markets</span>
                            </a>
                        </li>

                        <!-- Market Waitlist -->
                        <li>
                            <a href="{{ route('admin.markets.waitlist') }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.markets.waitlist') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.markets.waitlist') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                                <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Waitlist</span>
                            </a>
                        </li>

                    <!-- Referrals -->
                    <li>
                        <a href="{{ route('admin.referrals.index') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.referrals.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('admin.referrals.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Referrals</span>
                        </a>
                    </li>
                    @endif


                    <!-- Messages -->
                    <li>
                        <a href="{{ route('chat.index') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('chat.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <div class="relative">
                                <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('chat.*') ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 group-hover:text-blue-700 dark:group-hover:text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                                </svg>
                                @if(auth()->user()->hasUnreadMessages())
                                <span class="absolute -top-1 -right-1 block h-2 w-2 rounded-full bg-red-400"></span>
                                @endif
                            </div>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Messages</span>
                        </a>
                    </li>



                </ul>
            </li>

            <!-- Secondary Navigation -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-400" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                    Account
                </div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <!-- Profile -->
                    <li>
                        <a href="{{ route('profile.edit') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('profile.edit') ? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('profile') ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Profile</span>
                        </a>
                    </li>

                    <!-- Help -->
                    <li>
                        <a href="{{ route('help') }}"
                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('help') ? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="h-6 w-6 shrink-0 {{ request()->routeIs('help') ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>Help & Support</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Bottom section with account info -->
            <li class="mt-auto">
                <div class="flex items-center gap-x-4 px-2 py-3 text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                    <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div x-show="sidebarExpanded || window.innerWidth < 1024" x-transition class="flex flex-col">
                        <span class="text-sm">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->account_type->label() }}</span>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
</div>