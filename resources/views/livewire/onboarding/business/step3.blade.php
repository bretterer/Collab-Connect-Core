<!-- Step 3: Collaboration Goals -->
<div>
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Collaboration Goals</h2>

    <div class="space-y-6">
        <!-- Collaboration Goals -->
        <flux:field>
            <flux:label>What are your primary goals for using CollabConnect? (Select all that apply)</flux:label>
            <flux:select wire:model="collaborationGoals" variant="listbox" multiple placeholder="Choose goals...">
                @foreach($collaborationGoalOptions as $goal)
                    <flux:select.option value="{{ $goal->value }}">{{ $goal->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="collaborationGoals" />
        </flux:field>


        <!-- Campaign Types -->
        <flux:field>
            <flux:label>What types of campaigns do you anticipate running? (Select all that apply)</flux:label>
            <flux:select wire:model="campaignTypes" variant="listbox" multiple placeholder="Choose campaign types...">
                @foreach($campaignTypeOptions as $type)
                    <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="campaignTypes" />
        </flux:field>

    </div>
</div>