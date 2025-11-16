<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Email Sequences</flux:heading>
            <flux:text class="mt-1">Manage automated email sequences for your landing pages</flux:text>
        </div>
        <flux:button href="{{ route('admin.marketing.email-sequences.create') }}" wire:navigate variant="primary" icon="plus">
            New Sequence
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4">
            <flux:input wire:model.live="search" placeholder="Search sequences..." />
        </div>

        @if($sequences->count() > 0)
            <flux:table :paginate="$sequences">
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Emails</flux:table.column>
                    <flux:table.column>Subscribers</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($sequences as $sequence)
                        <flux:table.row :key="$sequence->id">
                            <flux:table.cell>
                                <div>
                                    <div class="font-medium text-zinc-900 dark:text-white">
                                        {{ $sequence->name }}
                                    </div>
                                    @if($sequence->description)
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ Str::limit($sequence->description, 60) }}
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge color="blue">
                                    {{ $sequence->emails_count }} {{ Str::plural('email', $sequence->emails_count) }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge color="green">
                                    {{ $sequence->active_subscribers_count }} active
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $sequence->created_at->format('M d, Y') }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:button
                                        href="{{ route('admin.marketing.email-sequences.edit', $sequence) }}"
                                        wire:navigate
                                        size="sm"
                                        variant="ghost"
                                        icon="pencil"
                                    >
                                        Edit
                                    </flux:button>

                                    <flux:button
                                        wire:click="duplicateSequence({{ $sequence->id }})"
                                        size="sm"
                                        variant="ghost"
                                        icon="document-duplicate"
                                    >
                                        Duplicate
                                    </flux:button>

                                    <flux:button
                                        wire:click="deleteSequence({{ $sequence->id }})"
                                        wire:confirm="Are you sure you want to delete this sequence?"
                                        size="sm"
                                        variant="ghost"
                                        class="text-red-600 hover:text-red-700"
                                        icon="trash"
                                    >
                                        Delete
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <div class="text-center py-12">
                <flux:icon.envelope class="size-12 mx-auto text-zinc-400 mb-4" />
                <flux:heading size="sm" class="mb-2">No email sequences found</flux:heading>
                <flux:text>
                    @if($search)
                        No sequences match your search. Try a different query.
                    @else
                        Get started by creating your first email sequence.
                    @endif
                </flux:text>
                @if(!$search)
                    <flux:button
                        href="{{ route('admin.marketing.email-sequences.create') }}"
                        wire:navigate
                        variant="primary"
                        class="mt-4"
                    >
                        Create Email Sequence
                    </flux:button>
                @endif
            </div>
        @endif
    </flux:card>
</div>
