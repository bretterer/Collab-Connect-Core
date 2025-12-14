<!-- Step 3: Branding -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-pink-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">3</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Brand Your Profile
        </flux:heading>
    </div>

    <flux:description>
        Upload your business logo and banner to make your profile stand out and help influencers recognize your brand.
    </flux:description>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Business Logo -->
        <div>
            <flux:label>Business Logo</flux:label>
            <div class="mt-2">
                <div class="flex items-center justify-center w-full">
                    <label for="businessLogo" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <flux:icon.cloud-arrow-up class="w-10 h-10 mb-4 text-gray-500 dark:text-gray-400" />
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload logo</span></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                        </div>
                        <input id="businessLogo" type="file" wire:model="businessLogo" class="hidden" accept="image/*" />
                    </label>
                </div>

                @if ($businessLogo)
                <div class="mt-4 text-center">
                    <img src="{{ $businessLogo->temporaryUrl() }}" alt="Logo preview" class="w-32 h-32 mx-auto rounded-lg object-cover border-4 border-white shadow-lg">
                    <p class="text-sm text-green-600 dark:text-green-400 mt-2">Logo ready to upload</p>
                </div>
                @endif

                @error('businessLogo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <flux:description class="mt-2">
                Your logo will appear on your profile and in search results.
            </flux:description>
        </div>

        <!-- Business Banner -->
        <div>
            <flux:label>Business Banner</flux:label>
            <div class="mt-2">
                <div class="flex items-center justify-center w-full">
                    <label for="businessBanner" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <flux:icon.cloud-arrow-up class="w-10 h-10 mb-4 text-gray-500 dark:text-gray-400" />
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload banner</span></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                        </div>
                        <input id="businessBanner" type="file" wire:model="businessBanner" class="hidden" accept="image/*" />
                    </label>
                </div>

                @if ($businessBanner)
                <div class="mt-4">
                    <img src="{{ $businessBanner->temporaryUrl() }}" alt="Banner preview" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                    <p class="text-sm text-green-600 dark:text-green-400 mt-2 text-center">Banner ready to upload</p>
                </div>
                @endif

                @error('businessBanner') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <flux:description class="mt-2">
                Your banner appears at the top of your business profile page.
            </flux:description>
        </div>
    </div>

    <flux:callout variant="info" icon="information-circle" class="mt-6">
        <flux:callout.heading>Tip</flux:callout.heading>
        <flux:callout.text>You can skip this step and add branding later from your Business Settings.</flux:callout.text>
    </flux:callout>
</div>
