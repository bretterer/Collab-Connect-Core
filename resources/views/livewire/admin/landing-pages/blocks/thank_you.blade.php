<div class="space-y-4">
    <flux:field>
        <flux:label>Headline</flux:label>
        <flux:input wire:model="blockData.headline" placeholder="Thank You!" />
    </flux:field>

    <flux:field>
        <flux:label>Message</flux:label>
        <flux:textarea wire:model="blockData.message" placeholder="Your payment was successful..." rows="3" />
    </flux:field>

    <flux:field>
        <flux:label>Show Order ID</flux:label>
        <flux:checkbox wire:model="blockData.show_order_id" label="Display the Stripe session ID to the customer" />
    </flux:field>

    <div class="grid grid-cols-2 gap-4">
        <flux:field>
            <flux:label>Button Text</flux:label>
            <flux:input wire:model="blockData.button_text" placeholder="Return to Home" />
        </flux:field>

        <flux:field>
            <flux:label>Button URL</flux:label>
            <flux:input wire:model="blockData.button_url" placeholder="/" />
        </flux:field>
    </div>

    <flux:callout variant="info">
        <strong>Usage:</strong> Add this block to your landing page. After a Stripe Checkout, redirect to this page with <code>?success=true</code> parameter to display this thank you message.
    </flux:callout>
</div>
