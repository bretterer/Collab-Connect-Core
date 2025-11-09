<div class="space-y-4">
    <flux:field>
        <flux:label>Copyright Text</flux:label>
        <flux:input wire:model="blockData.copyright" placeholder="© 2025 Your Company" />
    </flux:field>

    <flux:field>
        <flux:label>Links (JSON)</flux:label>
        <flux:textarea wire:model="blockData.links" placeholder='[{"label": "Privacy", "url": "/privacy"}, {"label": "Terms", "url": "/terms"}]' rows="4" />
        <flux:description>Add footer links in JSON format</flux:description>
    </flux:field>
</div>
