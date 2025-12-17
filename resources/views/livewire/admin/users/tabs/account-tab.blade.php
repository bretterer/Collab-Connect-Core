<flux:card>
    <form wire:submit="save">
        <div class="space-y-6">
            <!-- Name -->
            <flux:field>
                <flux:label>Full Name</flux:label>
                <flux:input wire:model="name" />
                <flux:error name="name" />
            </flux:field>

            <!-- Email -->
            <flux:field>
                <flux:label>Email Address</flux:label>
                <flux:input type="email" wire:model="email" />
                <flux:error name="email" />
            </flux:field>

            <!-- Account Type -->
            <flux:field>
                <flux:label>Account Type</flux:label>
                <flux:select wire:model="accountType">
                    @foreach($this->getAccountTypeOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="accountType" />
                <flux:description>
                    <span class="text-yellow-600 dark:text-yellow-400 font-medium">Warning:</span> Changing the account type may affect the user's access to features and existing data.
                </flux:description>
            </flux:field>

            <!-- Access Admin Panel -->
            <div class="flex items-center space-x-3">
                <flux:checkbox wire:model="allowAdminAccess" />
                <flux:label>Allow this user to access the admin panel</flux:label>
            </div>

            <flux:separator />

            <!-- Current Profile Status -->
            <div>
                <flux:label>Current Profile Status</flux:label>
                <div class="mt-2">
                    @if($user->hasCompletedOnboarding())
                        <flux:badge color="green" size="lg">Profile Complete</flux:badge>
                    @else
                        <flux:badge color="yellow" size="lg">Profile Incomplete</flux:badge>
                    @endif
                </div>
            </div>

            <!-- Email Verification Status -->
            <div>
                <flux:label>Email Verification Status</flux:label>
                <div class="mt-2">
                    @if($user->email_verified_at)
                        <div class="flex items-center space-x-2">
                            <flux:badge color="green" size="lg">
                                <flux:icon name="check-circle" class="w-4 h-4" />
                                Verified on {{ $user->email_verified_at->format('M j, Y') }}
                            </flux:badge>
                        </div>
                    @else
                        <flux:badge color="red" size="lg">
                            <flux:icon name="exclamation-triangle" class="w-4 h-4" />
                            Not Verified
                        </flux:badge>
                    @endif
                </div>
            </div>

            <!-- Member Since -->
            <div>
                <flux:label>Member Since</flux:label>
                <flux:text class="mt-2">
                    {{ $user->created_at->format('F j, Y g:i A') }} ({{ $user->created_at->diffForHumans() }})
                </flux:text>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <flux:button variant="ghost" href="{{ route('admin.users.show', $user) }}">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Save Changes
            </flux:button>
        </div>
    </form>
</flux:card>
