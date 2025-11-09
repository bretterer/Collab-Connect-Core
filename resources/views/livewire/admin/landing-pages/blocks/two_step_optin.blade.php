<div class="space-y-4">
    <flux:field>
        <flux:label>Initial Button Text</flux:label>
        <flux:input wire:model="blockData.button_text" placeholder="Yes! I Want This" />
        <flux:description>The button text that users click to open the opt-in modal</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Modal Headline</flux:label>
        <flux:input wire:model="blockData.modal_headline" placeholder="Enter your email to continue" />
    </flux:field>

    <flux:field>
        <flux:label>Form Button Text</flux:label>
        <flux:input wire:model="blockData.form_button_text" placeholder="Get Instant Access" />
    </flux:field>

    <flux:field>
        <flux:label>Success Message</flux:label>
        <flux:input wire:model="blockData.success_message" placeholder="Check your email!" />
    </flux:field>
</div>
