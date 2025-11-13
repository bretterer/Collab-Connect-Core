<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <flux:heading size="xl" class="mb-6">Market Management</flux:heading>

    <div class="mb-6">
        <flux:button wire:click="$set('showCreateModal', true)" variant="primary">
            Create New Market
        </flux:button>
    </div>

    @if($markets->isEmpty())
        <flux:callout variant="info">
            No markets have been created yet. Click "Create New Market" to get started.
        </flux:callout>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zipcodes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($markets as $market)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.markets.edit', $market) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    {{ $market->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-300">
                                    {{ Str::limit($market->description, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge>{{ $market->zipcodes_count }} zipcodes</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($market->is_active)
                                    <flux:badge variant="success">Active</flux:badge>
                                @else
                                    <flux:badge variant="warning">Inactive</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <flux:button
                                        wire:click="toggleActive({{ $market->id }})"
                                        size="sm"
                                        :variant="$market->is_active ? 'warning' : 'success'">
                                        {{ $market->is_active ? 'Deactivate' : 'Activate' }}
                                    </flux:button>

                                    <flux:button
                                        wire:click="deleteMarket({{ $market->id }})"
                                        wire:confirm="Are you sure you want to delete this market? All zipcodes will be removed."
                                        size="sm"
                                        variant="danger">
                                        Delete
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $markets->links() }}
        </div>
    @endif

    {{-- Create Market Modal --}}
    <flux:modal name="create-market" :open="$showCreateModal" wire:model="showCreateModal">
        <form wire:submit.prevent="createMarket" class="space-y-4">
            <flux:heading size="lg">Create New Market</flux:heading>

            <flux:input
                wire:model="name"
                label="Market Name"
                placeholder="e.g., Greater Los Angeles"
                required
            />

            <flux:textarea
                wire:model="description"
                label="Description"
                placeholder="Optional description of this market"
                rows="3"
            />

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary">Create Market</flux:button>
                <flux:button type="button" variant="ghost" wire:click="$set('showCreateModal', false)">Cancel</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
