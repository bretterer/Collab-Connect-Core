<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Beta Invites Management</h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Manage waitlist invitations and send beta invitations to users.</p>
    </div>

    @if (session('message'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
            <p class="text-green-800 dark:text-green-200">{{ session('message') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
            <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Beta Invites List -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Waitlist ({{ count($invites) }})</h2>
                <button wire:click="loadInvites" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if(empty($invites))
                <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium">No waitlist entries found</h3>
                    <p class="mt-1 text-sm">The waitlist.csv file is empty or not found.</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($invites as $invite)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $invite['full_name'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $invite['email'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $invite['user_type'] === 'influencer' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' }}">
                                        {{ ucfirst($invite['user_type']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($invite['user_type'] === 'business' && !empty($invite['business_name']))
                                            <div>{{ $invite['business_name'] }}</div>
                                        @elseif($invite['user_type'] === 'influencer' && !empty($invite['follower_count']))
                                            <div>{{ $invite['follower_count'] }} followers</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(!empty($invite['registered_at']))
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                            Registered
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($invite['registered_at'])->format('M j, Y') }}
                                        </div>
                                    @elseif(!empty($invite['invited_at']))
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                            Invited
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($invite['invited_at'])->format('M j, Y') }}
                                        </div>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(!empty($invite['registered_at']))
                                        <span class="text-gray-400 dark:text-gray-500">Completed</span>
                                    @elseif(!empty($invite['invited_at']))
                                        <button wire:click="resendInvite({{ $invite['id'] }})"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3 disabled:opacity-50"
                                                wire:loading.attr="disabled"
                                                wire:target="resendInvite({{ $invite['id'] }})">
                                            <span wire:loading.remove wire:target="resendInvite({{ $invite['id'] }})">Resend Invite</span>
                                            <span wire:loading wire:target="resendInvite({{ $invite['id'] }})">Sending...</span>
                                        </button>
                                    @else
                                        <button wire:click="sendInvite({{ $invite['id'] }})"
                                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 disabled:opacity-50"
                                                wire:loading.attr="disabled"
                                                wire:target="sendInvite({{ $invite['id'] }})">
                                            <span wire:loading.remove wire:target="sendInvite({{ $invite['id'] }})">Send Invite</span>
                                            <span wire:loading wire:target="sendInvite({{ $invite['id'] }})">Sending...</span>
                                        </button>
                                    @endif

                                    @if(!empty($invite['referralCode']))
                                        <button wire:click="resendReferralCode({{ $invite['id'] }})"
                                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 disabled:opacity-50"
                                                wire:loading.attr="disabled"
                                                wire:target="resendReferralCode({{ $invite['id'] }})">
                                        <span wire:loading.remove wire:target="resendReferralCode({{ $invite['id'] }})">Resend Code {{ $invite['referralCode'] }}</span>
                                        <span wire:loading wire:target="resendReferralCode({{ $invite['id'] }})">Sending...</span>
                                    </button>
                                    @else
                                    <button wire:click="addToReferralProgram({{ $invite['id'] }})"
                                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 disabled:opacity-50"
                                                wire:loading.attr="disabled"
                                                wire:target="addToReferralProgram({{ $invite['id'] }})">
                                        <span wire:loading.remove wire:target="addToReferralProgram({{ $invite['id'] }})">Referral Program</span>
                                        <span wire:loading wire:target="addToReferralProgram({{ $invite['id'] }})">Sending...</span>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
