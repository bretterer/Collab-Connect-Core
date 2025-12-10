<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Custom Signup Pages</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Create and manage custom signup pages for webinars and special offers.
            </p>
        </div>
        <flux:button :href="route('admin.custom-signup-pages.create')" wire:navigate icon="plus">
            Create Signup Page
        </flux:button>
    </div>

    <!-- Filters and Search -->
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            <!-- Search -->
            <flux:field>
                <flux:label>Search Pages</flux:label>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name, slug, or title..."
                />
            </flux:field>

            <!-- Status Filter -->
            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="statusFilter">
                    @foreach($this->getStatusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <!-- Account Type Filter -->
            <flux:field>
                <flux:label>Account Type</flux:label>
                <flux:select wire:model.live="accountTypeFilter">
                    @foreach($this->getAccountTypeOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <!-- Results Count -->
            <div class="flex items-end pb-2">
                <flux:text class="text-sm">
                    Showing {{ $pages->count() }} of {{ $pages->total() }} pages
                </flux:text>
            </div>
        </div>
    </flux:card>

    <!-- Custom Signup Pages Table -->
    @if($pages->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                wire:click="sortBy('name')"
                                class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex items-center gap-1">
                                    <span>Name</span>
                                    @if($sortBy === 'name')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Slug / URL
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Account Type
                            </th>
                            <th scope="col"
                                wire:click="sortBy('is_active')"
                                class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex items-center gap-1">
                                    <span>Status</span>
                                    @if($sortBy === 'is_active')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                wire:click="sortBy('updated_at')"
                                class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex items-center gap-1">
                                    <span>Last Updated</span>
                                    @if($sortBy === 'updated_at')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pages as $page)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $page->name }}
                                        </div>
                                        @if($page->title !== $page->name)
                                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                {{ Str::limit($page->title, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white font-mono">
                                        /signup/{{ $page->slug }}
                                    </div>
                                    @if($page->isPublished())
                                        <a href="{{ route('signup.show', $page->slug) }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                            View page &rarr;
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge color="{{ $page->account_type->value === 'influencer' ? 'pink' : 'blue' }}" size="sm">
                                        {{ $page->account_type->label() }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge color="{{ $page->is_active ? 'green' : 'yellow' }}" size="sm">
                                        {{ $page->is_active ? 'Active' : 'Draft' }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div>{{ $page->updated_at->diffForHumans() }}</div>
                                    @if($page->updater)
                                        <div class="text-xs">by {{ $page->updater->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <flux:dropdown position="left">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />

                                        <flux:menu>
                                            <flux:menu.item icon="pencil" :href="route('admin.custom-signup-pages.edit', $page)" wire:navigate>
                                                Edit
                                            </flux:menu.item>

                                            <flux:menu.item icon="document-duplicate" wire:click="duplicatePage({{ $page->id }})">
                                                Duplicate
                                            </flux:menu.item>

                                            <flux:menu.item
                                                icon="{{ $page->is_active ? 'eye-slash' : 'eye' }}"
                                                wire:click="toggleStatus({{ $page->id }})">
                                                {{ $page->is_active ? 'Deactivate' : 'Activate' }}
                                            </flux:menu.item>

                                            <flux:menu.separator />

                                            <flux:menu.item
                                                icon="trash"
                                                variant="danger"
                                                wire:click="confirmDelete({{ $page->id }})"
                                                x-on:click="$flux.modal('delete-page').show()">
                                                Delete
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $pages->links() }}
        </div>
    @else
        <!-- Empty State -->
        <flux:card>
            <div class="text-center py-12">
                <flux:icon.document-text class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No custom signup pages</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Get started by creating a new custom signup page for your webinars.
                </p>
                <div class="mt-6">
                    <flux:button :href="route('admin.custom-signup-pages.create')" wire:navigate icon="plus">
                        Create Signup Page
                    </flux:button>
                </div>
            </div>
        </flux:card>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($deletingPageId)
        <flux:modal name="delete-page" class="max-w-md">
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <flux:heading size="lg" class="text-center">Delete Signup Page?</flux:heading>
                    <flux:subheading class="text-center mt-2">
                        This action cannot be undone. The signup page will be permanently deleted.
                    </flux:subheading>
                </div>

                <div class="flex gap-3 justify-end">
                    <flux:button
                        variant="ghost"
                        wire:click="cancelDelete"
                        x-on:click="$flux.modal('delete-page').close()">
                        Cancel
                    </flux:button>
                    <flux:button
                        variant="danger"
                        wire:click="deletePage"
                        x-on:click="$flux.modal('delete-page').close()">
                        Delete Page
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
