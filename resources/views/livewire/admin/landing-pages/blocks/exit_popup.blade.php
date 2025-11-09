<div class="space-y-4">
    <flux:field>
        <flux:label>Headline</flux:label>
        <flux:input wire:model="blockData.headline" placeholder="Wait! Don't Leave Yet" />
    </flux:field>

    <flux:field>
        <flux:label>Content</flux:label>
        <flux:textarea wire:model="blockData.content" placeholder="Get a special offer before you go" rows="3" />
    </flux:field>

    <div class="grid grid-cols-2 gap-4">
        <flux:field>
            <flux:label>CTA Text</flux:label>
            <flux:input wire:model="blockData.cta_text" placeholder="Claim Offer" />
        </flux:field>

        <flux:field>
            <flux:label>CTA URL</flux:label>
            <flux:input wire:model="blockData.cta_url" placeholder="#" />
        </flux:field>
    </div>
</div>
