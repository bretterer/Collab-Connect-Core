<div class="space-y-4">
    <flux:field>
        <flux:label>Section Title</flux:label>
        <flux:input wire:model="blockData.title" placeholder="Frequently Asked Questions" />
    </flux:field>

    <flux:field>
        <flux:label>FAQ Items (JSON)</flux:label>
        <flux:textarea wire:model="blockData.items" placeholder='[{"question": "Question 1?", "answer": "Answer 1"}]' rows="6" />
        <flux:description>Add FAQ items in JSON format with question and answer</flux:description>
    </flux:field>
</div>
