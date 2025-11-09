<div class="space-y-4">
    <flux:field>
        <flux:label>HTML</flux:label>
        <flux:textarea wire:model="blockData.html" placeholder="<div>Your HTML here</div>" rows="6" />
    </flux:field>

    <flux:field>
        <flux:label>CSS (Optional)</flux:label>
        <flux:textarea wire:model="blockData.css" placeholder=".my-class { color: blue; }" rows="4" />
    </flux:field>

    <flux:field>
        <flux:label>JavaScript (Optional)</flux:label>
        <flux:textarea wire:model="blockData.js" placeholder="console.log('Hello');" rows="4" />
    </flux:field>
</div>
