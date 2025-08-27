<!-- Step 2: Business Profile & Identity -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">2</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Business Profile & Identity
        </flux:heading>
    </div>

    <!-- Business Type & Industry -->
    <div class="space-y-6">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            What type of business are you?
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label>Business Type</flux:label>
                <flux:select wire:model="businessType" placeholder="Select your business type">
                    @foreach(\App\Enums\BusinessType::cases() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="businessType" />
            </flux:field>

            <flux:field>
                <flux:label>Industry</flux:label>
                <flux:select wire:model="industry" placeholder="Choose your industry">
                    @foreach(\App\Enums\BusinessIndustry::cases() as $industry)
                        <flux:select.option value="{{ $industry->value }}">{{ $industry->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="industry" />
            </flux:field>
        </div>
    </div>

    <!-- Business Description -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Tell us about your business
        </flux:heading>

        <flux:field>
            <flux:label>What do you do?</flux:label>
            <flux:textarea
                wire:model="businessDescription"
                placeholder="Describe what your business does, your products or services..."
                rows="3"
            />
            <flux:description>
                Help influencers understand your business and what you offer.
            </flux:description>
            <flux:error name="businessDescription" />
        </flux:field>

        <flux:field>
            <flux:label>What makes your business special?</flux:label>
            <flux:textarea
                wire:model="uniqueValueProposition"
                placeholder="What sets you apart from competitors? Your unique selling points..."
                rows="3"
            />
            <flux:description>
                Highlight what makes your brand unique and appealing to influencers.
            </flux:description>
            <flux:error name="uniqueValueProposition" />
        </flux:field>
    </div>

    <!-- Social Media Accounts (Optional) -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Business Social Media (Optional)
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            Link your existing business social media accounts to build credibility.
        </flux:description>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Instagram Handle</flux:label>
                <flux:input
                    wire:model="instagramHandle"
                    placeholder="@yourbusiness"
                />
                <flux:error name="instagramHandle" />
            </flux:field>

            <flux:field>
                <flux:label>Facebook Page</flux:label>
                <flux:input
                    wire:model="facebookHandle"
                    placeholder="@yourbusiness"
                />
                <flux:error name="facebookHandle" />
            </flux:field>

            <flux:field>
                <flux:label>TikTok Handle</flux:label>
                <flux:input
                    wire:model="tiktokHandle"
                    placeholder="@yourbusiness"
                />
                <flux:error name="tiktokHandle" />
            </flux:field>

            <flux:field>
                <flux:label>LinkedIn Page</flux:label>
                <flux:input
                    wire:model="linkedinHandle"
                    placeholder="@yourbusiness"
                />
                <flux:error name="linkedinHandle" />
            </flux:field>
        </div>
    </div>
</div>