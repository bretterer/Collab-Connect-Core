<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading size="xl">Create Email Sequence</flux:heading>
        <flux:text class="mt-1">
            Create a new automated email sequence for your landing pages
        </flux:text>
    </div>

    <flux:card>
        <form wire:submit="save">
            <div class="space-y-6">
                <flux:field>
                    <flux:label>Sequence Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g., Welcome Series" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model="description" placeholder="Brief description of this sequence" rows="3" />
                    <flux:error name="description" />
                </flux:field>

                <div class="flex items-center gap-3 pt-4">
                    <flux:button type="submit" variant="primary">
                        Create Sequence
                    </flux:button>
                    <flux:button href="{{ route('admin.marketing.email-sequences.index') }}" wire:navigate variant="ghost">
                        Cancel
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:card>
</div>
