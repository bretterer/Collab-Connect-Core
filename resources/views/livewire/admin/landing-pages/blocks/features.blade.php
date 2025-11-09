<div class="space-y-4">
    <flux:field>
        <flux:label>Section Title</flux:label>
        <flux:input wire:model="blockData.title" placeholder="Features" />
    </flux:field>

    <flux:field>
        <flux:label>Feature Items (JSON)</flux:label>
        <flux:textarea wire:model="blockData.items" placeholder='[{"icon": "star", "title": "Feature 1", "description": "Description"}]' rows="6" />
        <flux:description>Add feature items in JSON format with icon, title, and description</flux:description>
    </flux:field>
</div>
