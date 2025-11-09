<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="mb-6">
                <flux:button variant="ghost" size="sm" :href="route('admin.marketing.landing-pages.index')" wire:navigate icon="arrow-left">
                    Back to Landing Pages
                </flux:button>
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Create Landing Page
                </h1>
                <p class="text-gray-500 dark:text-gray-400">
                    Enter the basic information to create a new landing page. You'll be able to add content and customize it after creation.
                </p>
            </div>

            <form wire:submit="create" class="space-y-6">
                <flux:field>
                    <flux:label>Page Title</flux:label>
                    <flux:input wire:model.blur="title" placeholder="Enter page title" required />
                    <flux:error name="title" />
                    <flux:description>This will be the title of your landing page</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>URL Slug</flux:label>
                    <flux:input wire:model="slug" placeholder="url-slug" required>
                        <x-slot:prefix>
                            <span class="text-sm text-gray-500">/l/</span>
                        </x-slot:prefix>
                    </flux:input>
                    <flux:error name="slug" />
                    <flux:description>The URL path for your landing page (e.g., /l/your-slug)</flux:description>
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button variant="ghost" :href="route('admin.marketing.landing-pages.index')" wire:navigate>
                        Cancel
                    </flux:button>
                    <flux:button type="submit">
                        Create & Start Editing
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
