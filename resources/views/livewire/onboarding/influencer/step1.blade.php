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

    <!-- Username -->
    <div class="mb-2">
        <flux:field>
            <flux:label>Username (Optional)</flux:label>
            <flux:input
                wire:model.live.debounce.500ms="username"
                placeholder="yourusername"
            />
            <flux:error name="username" />
            @if($username && !$errors->has('username'))
                <div class="flex items-center gap-2 mt-2 text-sm text-green-600 dark:text-green-400">
                    <flux:icon.check-circle class="w-4 h-4" />
                    <span class="font-mono">{{ config('app.url') }}/influencer/{{ $username }}</span> is available!
                </div>
            @else
                <flux:description class="mt-2">Your unique public profile URL will be: <span class="font-mono">{{ config('app.url') }}/influencer/{{ $username ?: 'your-username' }}</span></flux:description>
            @endif
        </flux:field>
    </div>

    <flux:separator />

    <!-- Bio -->
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

    <flux:separator />

    <!-- Profile Images -->
    <div>
        <flux:heading class="text-gray-800 dark:text-gray-200 mb-4">
            Profile Images (Optional)
        </flux:heading>
        <flux:description class="mb-4">
            Add photos to help businesses recognize you. You can always add or change these later in your settings.
        </flux:description>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Profile Image -->
            <div>
                <flux:label>Profile Image</flux:label>
                <div class="mt-2">
                    <div class="flex items-center justify-center w-full">
                        <label for="profileImage" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <flux:icon.cloud-arrow-up class="w-8 h-8 mb-2 text-gray-500 dark:text-gray-400" />
                                <p class="text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Upload profile photo</span></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                            </div>
                            <input id="profileImage" type="file" wire:model="profileImage" class="hidden" accept="image/*" />
                        </label>
                    </div>

                    @if ($profileImage)
                    <div class="mt-4 text-center">
                        <img src="{{ $profileImage->temporaryUrl() }}" alt="Profile preview" class="w-24 h-24 mx-auto rounded-full object-cover border-4 border-white shadow-lg">
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">Ready to upload</p>
                    </div>
                    @endif

                    @error('profileImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Banner Image -->
            <div>
                <flux:label>Banner Image</flux:label>
                <div class="mt-2">
                    <div class="flex items-center justify-center w-full">
                        <label for="bannerImage" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <flux:icon.cloud-arrow-up class="w-8 h-8 mb-2 text-gray-500 dark:text-gray-400" />
                                <p class="text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Upload banner</span></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                            </div>
                            <input id="bannerImage" type="file" wire:model="bannerImage" class="hidden" accept="image/*" />
                        </label>
                    </div>

                    @if ($bannerImage)
                    <div class="mt-4">
                        <img src="{{ $bannerImage->temporaryUrl() }}" alt="Banner preview" class="w-full h-20 rounded-lg object-cover border-4 border-white shadow-lg">
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2 text-center">Ready to upload</p>
                    </div>
                    @endif

                    @error('bannerImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>
</div>
