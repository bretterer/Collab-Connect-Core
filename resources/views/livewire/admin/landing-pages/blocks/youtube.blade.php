<div class="space-y-4">
    <flux:field>
        <flux:label>YouTube Video ID or URL</flux:label>
        <flux:input wire:model="blockData.video_id" placeholder="dQw4w9WgXcQ or https://www.youtube.com/watch?v=dQw4w9WgXcQ" required />
        <flux:description>Enter either the video ID or the full YouTube URL</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Aspect Ratio</flux:label>
        <flux:select wire:model="blockData.aspect_ratio">
            <option value="16/9">16:9 (Widescreen)</option>
            <option value="4/3">4:3 (Standard)</option>
            <option value="1/1">1:1 (Square)</option>
            <option value="21/9">21:9 (Ultra-wide)</option>
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>Max Width</flux:label>
        <flux:select wire:model="blockData.max_width">
            <option value="full">Full Width</option>
            <option value="large">Large (1280px)</option>
            <option value="medium">Medium (768px)</option>
            <option value="small">Small (640px)</option>
        </flux:select>
    </flux:field>
</div>
