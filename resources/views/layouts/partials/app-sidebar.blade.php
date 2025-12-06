<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-900 px-6 pb-4 ring-1 ring-gray-900/10 dark:ring-gray-800">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <div class="flex items-center space-x-3">
            <img class="block h-8 w-auto dark:hidden"
                src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
                alt="CollabConnect Logo" />
            <img class="hidden h-8 w-auto dark:block"
                src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
                alt="CollabConnect Logo" />
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <div class="flex flex-1 flex-col gap-y-7">
            <!-- Main Navigation -->
            <div>
                @if(auth()->user()->account_type === App\Enums\AccountType::BUSINESS && !request()->routeIs('admin.*'))
                    @include('layouts.partials.nav.business')
                @elseif(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER && !request()->routeIs('admin.*'))
                    @include('layouts.partials.nav.influencer')
                @elseif(auth()->user()->isAdmin())
                    @include('layouts.partials.nav.admin')
                @endif
            </div>

            <!-- Bottom section with account info -->
            <div class="mt-auto">
                <div class="flex items-center gap-x-4 px-2 py-3 text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                    <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->account_type->label() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
