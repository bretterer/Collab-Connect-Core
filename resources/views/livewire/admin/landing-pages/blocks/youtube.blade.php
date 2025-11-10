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

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Player Options</h4>

        <div class="space-y-3">
            <div class="flex items-center justify-between py-2">
                <div>
                    <flux:label>Autoplay</flux:label>
                    <flux:description>Automatically start playing when the page loads</flux:description>
                </div>
                <flux:checkbox wire:model.live="blockData.autoplay" />
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                    <flux:label>Show Controls</flux:label>
                    <flux:description>Display video player controls</flux:description>
                </div>
                <flux:checkbox wire:model.live="blockData.controls" />
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                    <flux:label>Loop Video</flux:label>
                    <flux:description>Play the video in a continuous loop</flux:description>
                </div>
                <flux:checkbox wire:model.live="blockData.loop" />
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                    <flux:label>Mute</flux:label>
                    <flux:description>Start with audio muted</flux:description>
                </div>
                <flux:checkbox wire:model.live="blockData.mute" />
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                    <flux:label>Modest Branding</flux:label>
                    <flux:description>Minimize YouTube branding in the player</flux:description>
                </div>
                <flux:checkbox wire:model.live="blockData.modest_branding" />
            </div>
        </div>
    </div>

    <flux:field>
        <flux:label>Progress Bar Color</flux:label>
        <div class="flex gap-2">
            <flux:radio wire:model="blockData.color" value="red">Red (Default)</flux:radio>
            <flux:radio wire:model="blockData.color" value="white">White</flux:radio>
        </div>
        <flux:description>Color of the video progress bar</flux:description>
    </flux:field>
</div>
