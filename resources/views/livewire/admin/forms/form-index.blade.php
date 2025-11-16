<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Forms</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Create and manage customizable forms for lead capture and data collection.
            </p>
        </div>
        <flux:button :href="route('admin.marketing.forms.create')" wire:navigate icon="plus">
            Create Form
        </flux:button>
    </div>

    <!-- Filters and Search -->
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Search -->
            <flux:field>
                <flux:label>Search Forms</flux:label>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by title or description..."
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

            <!-- Results Count -->
            <div class="flex items-end pb-2">
                <flux:text class="text-sm">
                    Showing {{ $forms->count() }} of {{ $forms->total() }} forms
                </flux:text>
            </div>
        </div>
    </flux:card>

    <!-- Forms Table -->
    @if($forms->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                wire:click="sortBy('title')"
                                class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex items-center gap-1">
                                    <span>Title</span>
                                    @if($sortBy === 'title')
                                        <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fields
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Submissions
                            </th>
                            <th scope="col"
                                wire:click="sortBy('status')"
                                class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex items-center gap-1">
                                    <span>Status</span>
                                    @if($sortBy === 'status')
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
                        @foreach($forms as $form)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <a href="{{ route('admin.marketing.forms.edit', $form) }}" wire:navigate class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $form->title }}
                                        </a>
                                        @if($form->internal_title && $form->internal_title !== $form->title)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $form->internal_title }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ count($form->fields) }} {{ Str::plural('field', count($form->fields)) }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.marketing.forms.submissions', $form) }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $form->submissions_count }} {{ Str::plural('submission', $form->submissions_count) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge :color="$form->status === 'published' ? 'green' : ($form->status === 'archived' ? 'zinc' : 'yellow')" size="sm">
                                        {{ ucfirst($form->status) }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $form->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <flux:dropdown>
                                        <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />
                                        <flux:menu>
                                            <flux:menu.item icon="pencil" :href="route('admin.marketing.forms.edit', $form)" wire:navigate>Edit</flux:menu.item>
                                            <flux:menu.item icon="document-duplicate" wire:click="duplicateForm({{ $form->id }})">Duplicate</flux:menu.item>
                                            <flux:menu.item icon="clipboard-document-list" :href="route('admin.marketing.forms.submissions', $form)" wire:navigate>View Submissions</flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="{{ $form->isPublished() ? 'eye-slash' : 'eye' }}" wire:click="toggleStatus({{ $form->id }})">
                                                {{ $form->isPublished() ? 'Unpublish' : 'Publish' }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $form->id }})">Delete</flux:menu.item>
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
            {{ $forms->links() }}
        </div>
    @else
        <flux:card>
            <div class="text-center py-12">
                <flux:icon.clipboard-document-list class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No forms found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($search || $statusFilter)
                        Try adjusting your search or filter to find what you're looking for.
                    @else
                        Get started by creating a new form.
                    @endif
                </p>
                @if(!$search && !$statusFilter)
                    <div class="mt-6">
                        <flux:button :href="route('admin.marketing.forms.create')" wire:navigate icon="plus">
                            Create Your First Form
                        </flux:button>
                    </div>
                @endif
            </div>
        </flux:card>
    @endif

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-form-modal" :open="$deletingFormId !== null">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Form</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Are you sure you want to delete this form? All submissions will also be deleted. This action cannot be undone.
            </p>
            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteForm">Delete Form</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
