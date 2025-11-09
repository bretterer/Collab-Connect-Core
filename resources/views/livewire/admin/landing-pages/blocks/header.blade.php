<div class="space-y-4">
    <flux:field>
        <flux:label>Logo URL</flux:label>
        <flux:input wire:model="blockData.logo" placeholder="https://example.com/logo.png" />
    </flux:field>

    <flux:field>
        <flux:label>Navigation Items (JSON)</flux:label>
        <flux:textarea wire:model="blockData.navigation" placeholder='[{"label": "Home", "url": "/"}, {"label": "About", "url": "/about"}]' rows="4" />
        <flux:description>Add navigation items in JSON format</flux:description>
    </flux:field>
</div>
