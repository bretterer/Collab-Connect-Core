<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading size="xl">Market Waitlist Management</flux:heading>
        <p class="text-gray-600 dark:text-gray-400">View and approve users waiting for market access</p>
    </div>

    @if(!empty($selectedPostalCodes))
        <div class="mb-6 flex items-center gap-4">
            <flux:badge variant="info">{{ count($selectedPostalCodes) }} zipcode(s) selected</flux:badge>
            <flux:button wire:click="approveSelected" variant="primary">
                Approve Selected
            </flux:button>
            <flux:button wire:click="$set('selectedPostalCodes', [])" variant="ghost">
                Clear Selection
            </flux:button>
        </div>
    @endif

    @if($waitlistData->isEmpty())
        <flux:callout variant="success">
            No users are currently on the waitlist!
        </flux:callout>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <input type="checkbox" class="rounded" wire:click="$set('selectedPostalCodes', {{ $waitlistData->pluck('postal_code')->toJson() }})" />
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zipcode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">City, State</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Users Waiting</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($waitlistData as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:checkbox
                                    wire:click="togglePostalCodeSelection('{{ $item->postal_code }}')"
                                    :checked="in_array($item->postal_code, $selectedPostalCodes)"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-900 dark:text-gray-300">{{ $item->postal_code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->postal_code_details->place_name ?? 'Unknown' }},
                                {{ $item->postal_code_details->admin_name1 ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="info">{{ $item->user_count }} {{ Str::plural('user', $item->user_count) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <flux:button
                                    wire:click="approveZipcode('{{ $item->postal_code }}')"
                                    wire:confirm="Approve all {{ $item->user_count }} users in zipcode {{ $item->postal_code }}?"
                                    size="sm"
                                    variant="primary">
                                    Approve All
                                </flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-4">
                {{ $waitlistData->links() }}
            </div>
        </div>
    @endif

    <div class="mt-6">
        <flux:callout variant="info">
            <strong>Note:</strong> When you approve users for a zipcode, they will immediately gain access to the platform on their next login.
            Make sure you've added the zipcode to an active market before approving users.
        </flux:callout>
    </div>
</div>
