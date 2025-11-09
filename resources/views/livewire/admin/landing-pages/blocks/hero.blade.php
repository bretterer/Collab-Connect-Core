<div class="space-y-4">
    <flux:field>
        <flux:label>Headline</flux:label>
        <flux:input wire:model="blockData.headline" placeholder="Your Compelling Headline" />
    </flux:field>

    <flux:field>
        <flux:label>Subheadline</flux:label>
        <flux:textarea wire:model="blockData.subheadline" placeholder="Supporting text that explains your offer" rows="3" />
    </flux:field>

    <div class="grid grid-cols-2 gap-4">
        <flux:field>
            <flux:label>CTA Button Text</flux:label>
            <flux:input wire:model="blockData.cta_text" placeholder="Get Started" />
        </flux:field>

        <flux:field>
            <flux:label>CTA Button URL</flux:label>
            <flux:input wire:model="blockData.cta_url" placeholder="#" />
        </flux:field>
    </div>

    <flux:field>
        <flux:label>Image URL (Optional)</flux:label>
        <flux:input wire:model="blockData.image" placeholder="https://example.com/image.jpg" />
    </flux:field>

    <flux:field>
        <flux:label>Background Color</flux:label>
        <flux:input type="color" wire:model="blockData.background_color" />
    </flux:field>
</div>
