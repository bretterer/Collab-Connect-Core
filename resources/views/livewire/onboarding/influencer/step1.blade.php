<!-- Step 1: Basic Information -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">1</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Basic Information
        </flux:heading>
    </div>

    <flux:field>
        <flux:label>Short Bio</flux:label>
        <flux:textarea
            wire:model="bio"
            placeholder="Tell us about yourself, your content style, and what makes you unique as an influencer. Keep it engaging and authentic!"
            rows="4"
        />
        <flux:error name="bio" />
        <flux:description>
            This bio will be shown to businesses when they view your profile. Make it compelling!
        </flux:description>
    </flux:field>
</div>