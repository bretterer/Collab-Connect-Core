<div class="space-y-4">
    <flux:field>
        <flux:label>Button Text</flux:label>
        <flux:input wire:model="blockData.button_text" placeholder="Buy Now" />
        <flux:description>Text displayed on the button that opens the modal</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Modal Headline</flux:label>
        <flux:input wire:model="blockData.modal_headline" placeholder="Complete Your Purchase" />
    </flux:field>

    <flux:field>
        <flux:label>Modal Description</flux:label>
        <flux:textarea wire:model="blockData.modal_description" placeholder="Enter your information below" rows="2" />
    </flux:field>

    <flux:field>
        <flux:label>Stripe Price ID</flux:label>
        <flux:input wire:model="blockData.stripe_price_id" placeholder="price_..." />
        <flux:description>The Stripe Price ID for the product/plan (e.g., price_1234567890)</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Cancel URL (Optional)</flux:label>
        <flux:input wire:model="blockData.cancel_url" placeholder="Leave empty to stay on page" />
        <flux:description>Where to redirect if payment is cancelled. Leave empty to return to this page.</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Form Fields (JSON)</flux:label>
        <flux:textarea wire:model="blockData.fields" rows="8" placeholder='[{"name": "name", "label": "Full Name", "type": "text", "required": true}]' />
        <flux:description>
            Define form fields to collect before checkout. Each field should have: name, label, type (text/email/tel/number), and required (true/false)
        </flux:description>
    </flux:field>

    <flux:callout variant="info">
        <strong>How it works:</strong> Users click the button, see a modal with your custom fields, submit the form, and are redirected to Stripe Checkout. After successful payment, they return to this same landing page with <code>?success=true</code> parameter. Add a <strong>Thank You block</strong> to this page to show the success message!
    </flux:callout>
</div>
