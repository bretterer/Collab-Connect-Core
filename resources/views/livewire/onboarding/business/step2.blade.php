<!-- Step 2: Contact -->
<div>
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Contact Information</h2>

    <div class="space-y-4">
        <!-- Contact Name -->
        <flux:input
            type="text"
            wire:model="contactName"
            label="Contact Name"
            placeholder="Primary contact for this account"
            required />

        <!-- Contact Email -->
        <flux:input
            type="email"
            wire:model="contactEmail"
            label="Contact Email"
            placeholder="Primary email for this account"
            required />

    </div>
</div>