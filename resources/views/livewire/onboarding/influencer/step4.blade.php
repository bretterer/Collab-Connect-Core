<!-- Step 4: Location & Contact -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">4</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Location & Contact
        </flux:heading>
    </div>

    <flux:description>
        Your location helps businesses find local influencers. Your contact information will only be shared with businesses you match with.
    </flux:description>

    <!-- Address Information -->
    <div class="space-y-4">
        <flux:field>
            <flux:label>Address</flux:label>
            <flux:input
                wire:model="address"
                placeholder="Enter your street address (optional)"
            />
            <flux:error name="address" />
        </flux:field>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label>City</flux:label>
                <flux:input
                    wire:model="city"
                    placeholder="Enter your city"
                />
                <flux:error name="city" />
            </flux:field>

            <flux:field>
                <flux:label>State</flux:label>
                <flux:input
                    wire:model="state"
                    placeholder="Enter your state"
                />
                <flux:error name="state" />
            </flux:field>

            <flux:field>
                <flux:label>County</flux:label>
                <flux:input
                    wire:model="county"
                    placeholder="Enter your county (optional)"
                />
                <flux:error name="county" />
            </flux:field>

            <flux:field>
                <flux:label>Postal Code</flux:label>
                <flux:input
                    wire:model="postalCode"
                    placeholder="Enter your postal code"
                />
                <flux:error name="postalCode" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Phone Number</flux:label>
            <flux:input
                type="tel"
                wire:model="phoneNumber"
                placeholder="+1 (555) 123-4567"
            />
            <flux:error name="phoneNumber" />
            <flux:description>
                Used for campaign coordination with businesses you work with
            </flux:description>
        </flux:field>
    </div>
</div>