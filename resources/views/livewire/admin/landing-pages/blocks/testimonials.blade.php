<div class="space-y-4">
    <flux:field>
        <flux:label>Section Title</flux:label>
        <flux:input wire:model="blockData.title" placeholder="What Our Customers Say" />
    </flux:field>

    <flux:field>
        <flux:label>Testimonial Items (JSON)</flux:label>
        <flux:textarea wire:model="blockData.items" placeholder='[{"name": "John Doe", "role": "CEO, Company", "content": "Great product!", "image": ""}]' rows="6" />
        <flux:description>Add testimonials in JSON format with name, role, content, and optional image URL</flux:description>
    </flux:field>
</div>
