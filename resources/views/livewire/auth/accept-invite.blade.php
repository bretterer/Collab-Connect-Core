<div>
    <div>
        <img class="block h-10 w-auto dark:hidden"
             src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
             alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto dark:block"
             src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
             alt="CollabConnect Logo" />
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">
            You've been invited to join a business!
        </h2>
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">
            Please review the invitation details below
        </p>
    </div>

    <div class="mt-10">
        @if ($invite)
            <!-- Invitation Details Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center dark:bg-blue-900/20">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18h15m-3 18V9.75m-6 9.75V9.75m-6 9.75V9.75M6.75 6.75h.75a.75.75 0 01.75.75v.75m-1.5-1.5h.75a.75.75 0 00.75.75v.75m-1.5-1.5v.75a.75.75 0 00.75.75h.75M6.75 6.75v.75a.75.75 0 01-.75.75h-.75m0 0v.75a.75.75 0 01-.75.75H4.5m2.25-1.5H6a.75.75 0 00-.75.75v.75m0 0H4.5m2.25-1.5v.75a.75.75 0 00.75.75H6m-1.5 1.5v.75a.75.75 0 01-.75.75h-.75m0 0v.75a.75.75 0 01-.75.75H4.5" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $invite->business->name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Invited by {{ $invite->invitedBy->name }}
                        </p>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Business</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $invite->business->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</span>
                        <flux:badge variant="outline">{{ ucfirst($invite->role) }}</flux:badge>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Invited by</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $invite->invitedBy->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Invitation Date</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $invite->invited_at->format('F j, Y') }}</span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6 dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">What happens when you accept?</h4>
                            <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>You'll become a team member of {{ $invite->business->name }}</li>
                                    <li>You'll have access to manage campaigns and review applications</li>
                                    <li>You can collaborate with other team members</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <flux:button 
                    class="flex-1 justify-center" 
                    variant="primary"
                    wire:click="acceptInvite"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Accept Invitation</span>
                    <span wire:loading>Accepting...</span>
                </flux:button>
                
                <flux:button 
                    class="flex-1 justify-center" 
                    variant="ghost"
                    wire:click="declineInvite"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Decline</span>
                    <span wire:loading>Declining...</span>
                </flux:button>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    By accepting this invitation, you agree to collaborate as part of the {{ $invite->business->name }} team.
                </p>
            </div>
        @else
            <!-- Invalid/Expired Invite -->
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Invalid Invitation</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    This invitation link is invalid or has expired.
                </p>
                <div class="mt-6">
                    <flux:button variant="primary" :href="route('dashboard')" wire:navigate>
                        Go to Dashboard
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</div>
