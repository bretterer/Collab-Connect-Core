<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.show', $user) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Update user account information and settings.
        </p>
    </div>

    <!-- Edit Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form wire:submit="save" class="px-4 py-5 sm:p-6">
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input type="text"
                               wire:model="name"
                               id="name"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email Address
                    </label>
                    <div class="mt-1">
                        <input type="email"
                               wire:model="email"
                               id="email"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Account Type -->
                <div>
                    <label for="accountType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Account Type
                    </label>
                    <div class="mt-1">
                        <select wire:model="accountType"
                                id="accountType"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                            @foreach($this->getAccountTypeOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('accountType')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <strong>Warning:</strong> Changing the account type may affect the user's access to features and existing data.
                    </p>
                </div>

                <!-- Access Admin Panel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Access Admin Panel
                    </label>
                    <div class="mt-1 flex items-center">
                        <input type="checkbox"
                               wire:model="allowAdminAccess"
                               id="allowAdminAccess"
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" />
                        <label for="allowAdminAccess" class="ml-2 block text-sm text-gray-900 dark:text-white">
                            Allow this user to access the admin panel
                        </label>
                    </div>
                </div>

                <!-- Current Profile Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Current Profile Status
                    </label>
                    <div class="mt-1">
                        @if($user->hasCompletedOnboarding())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                Profile Complete
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                Profile Incomplete
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Email Verification Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email Verification Status
                    </label>
                    <div class="mt-1">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Verified on {{ $user->email_verified_at->format('M j, Y') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Not Verified
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Member Since -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Member Since
                    </label>
                    <div class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $user->created_at->format('F j, Y g:i A') }} ({{ $user->created_at->diffForHumans() }})
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.show', $user) }}">
                        <flux:button variant="ghost">Cancel</flux:button>
                    </a>
                </div>
                <div class="flex items-center space-x-3">
                    <flux:button type="submit" variant="primary">
                        Save Changes
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</div>
