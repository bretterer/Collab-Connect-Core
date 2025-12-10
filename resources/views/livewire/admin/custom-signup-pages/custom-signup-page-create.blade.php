<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Link -->
    <div class="mb-4">
        <flux:button :href="route('admin.custom-signup-pages.index')" wire:navigate variant="ghost" icon="arrow-left">
            Back to Signup Pages
        </flux:button>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Custom Signup Page</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Start by entering the basic information. You'll configure the rest after creating.
        </p>
    </div>

    <!-- Create Form -->
    <flux:card>
        <form wire:submit="create" class="space-y-6">
            <!-- Page Name -->
            <flux:field>
                <flux:label>Page Name</flux:label>
                <flux:input
                    wire:model.live="name"
                    placeholder="e.g., Elite Webinar Offer"
                />
                <flux:description>
                    Internal name for easy reference (not shown to users).
                </flux:description>
                <flux:error name="name" />
            </flux:field>

            <!-- URL Slug -->
            <flux:field>
                <flux:label>URL Slug</flux:label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">/signup/</span>
                    <div class="flex-1">
                        <flux:input
                            wire:model.live="slug"
                            placeholder="elite-webinar-offer"
                        />
                    </div>
                </div>
                <flux:description>
                    The URL path for this signup page. Use lowercase letters, numbers, and hyphens only.
                </flux:description>
                <flux:error name="slug" />
            </flux:field>

            <!-- Account Type -->
            <flux:field>
                <flux:label>Account Type</flux:label>
                <flux:select wire:model="account_type">
                    @foreach($accountTypes as $type)
                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                    @endforeach
                </flux:select>
                <flux:description>
                    What type of account will users be signing up for?
                </flux:description>
                <flux:error name="account_type" />
            </flux:field>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <flux:button :href="route('admin.custom-signup-pages.index')" wire:navigate variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Create & Continue
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
