<div
    x-data
    @update-url.window="window.history.replaceState({}, '', $event.detail.url)"
>
<form wire:submit="updateBusinessSettings" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Business Settings</flux:heading>
            <flux:text class="text-zinc-500">Manage your business profile, team members, and preferences</flux:text>
        </div>
        <div class="flex items-center gap-3">
            <flux:button type="button" variant="ghost" href="{{ route('dashboard') }}">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="check">
                Save Settings
            </flux:button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <flux:navbar class="mb-6 -mx-4 border-b border-gray-200 dark:border-gray-700">
        <flux:navbar.item
            wire:click="setActiveTab('profile')"
            icon="user"
            :current="$activeTab === 'profile'">
            Profile
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('branding')"
            icon="photo"
            :current="$activeTab === 'branding'">
            Branding
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('location')"
            icon="map-pin"
            :current="$activeTab === 'location'">
            Location
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('social')"
            icon="at-symbol"
            :current="$activeTab === 'social'">
            Social
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('campaigns')"
            icon="document-duplicate"
            :current="$activeTab === 'campaigns'">
            Campaign Defaults
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('team')"
            icon="user-group"
            :current="$activeTab === 'team'">
            Team
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('billing')"
            icon="credit-card"
            :current="$activeTab === 'billing'">
            Billing
        </flux:navbar.item>
    </flux:navbar>

    <!-- Main Content -->
    @if($activeTab !== 'billing')
    <flux:card>
            <!-- Profile Tab -->
            @if($activeTab === 'profile')
                <div class="space-y-8">
                    <!-- Business Username -->
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.at-symbol class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Business Username</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Set your unique public profile URL</flux:text>
                            </div>
                        </div>

                        <flux:field>
                            <flux:label>Business Username</flux:label>
                            <flux:input
                                type="text"
                                wire:model.live.debounce.500ms="username"
                                placeholder="mybusiness" />
                            <flux:error name="username" />
                            @if($username && !$errors->has('username'))
                                <div class="flex items-center gap-2 mt-2 text-sm text-green-600 dark:text-green-400">
                                    <flux:icon.check-circle class="w-4 h-4" />
                                    <span>{{ config('app.url') }}/business/{{ $username }} is available!</span>
                                </div>
                            @else
                                <flux:description class="mt-2">Your unique public profile URL will be: <span class="font-mono">{{ config('app.url') }}/business/{{ $username ?: 'your-username' }}</span></flux:description>
                            @endif
                        </flux:field>
                    </div>

                    <flux:separator />

                    {{-- Promote Profile --}}
                    <div class="flex items-center justify-between gap-6 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-xl flex items-center justify-center {{ $is_promoted ? 'bg-yellow-100 dark:bg-yellow-900/50' : 'bg-zinc-200 dark:bg-zinc-700' }} transition-colors duration-200">
                                    <flux:icon.bars-arrow-up class="h-6 w-6 {{ $is_promoted ? 'text-yellow-600 dark:text-yellow-400' : 'text-zinc-400 dark:text-zinc-500' }} transition-colors duration-200" />
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <flux:heading>Promote Profile</flux:heading>
                                </div>
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">Boost your profile by promoting it for 1 week in search results.</flux:text>
                            </div>
                        </div>
                        @if($is_promoted)
                            <flux:button
                                type="button"
                                variant="ghost"
                                disabled>
                                Currently Promoted until {{ $promotion_ends_at->format('M j, Y') }}
                            </flux:button>
                        @elseif ($promotion_credits <= 0)
                            <flux:modal.trigger name="purchaseCredits">
                                <flux:button
                                    type="button"
                                    variant="primary">
                                    Buy Promotion Credits
                                </flux:button>
                            </flux:modal.trigger>
                        @else
                        <flux:modal.trigger name="promoteProfile">
                            <flux:button
                                type="button"
                                variant="primary"
                                :disabled="$promotion_credits <= 0">
                                Promote ({{ $promotion_credits }} credits available)
                            </flux:button>
                        </flux:modal.trigger>
                        @endif
                    </div>

                    <flux:modal name="promoteProfile" class="max-w-lg">
                        <div class="p-6">
                            <flux:heading size="xl" class="mb-2">Promote Your Profile</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
                                Boost your visibility and attract more influencer partnerships.
                            </flux:text>

                            <div class="space-y-4">
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <flux:icon name="bars-arrow-up" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                                        <div>
                                            <flux:heading size="sm">1 Week Promotion</flux:heading>
                                            <flux:text class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                                Your profile will be highlighted in search results for 7 days.
                                            </flux:text>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Promotion ends</flux:text>
                                        <flux:text class="text-sm font-medium">End of day {{ now()->addDays(7)->format('M j, Y') }}</flux:text>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Credits to use</flux:text>
                                        <flux:text class="text-sm font-medium">1 credit</flux:text>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">Remaining after</flux:text>
                                        <flux:text class="text-sm font-medium">{{ $promotion_credits - 1 }} credits</flux:text>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <flux:modal.close>
                                    <flux:button type="button" variant="ghost">Cancel</flux:button>
                                </flux:modal.close>
                                <flux:button type="button" wire:click="promoteProfile" variant="primary">
                                    Promote Now
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>

                    <flux:modal name="purchaseCredits" class="max-w-lg">
                        <div class="p-6">
                            <flux:heading size="xl" class="mb-2">Purchase Promotion Credits</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
                                Promotion credits allow you to boost your profile visibility in search results for 1 week per credit.
                            </flux:text>

                            <div class="space-y-4">
                                @if($this->promoCreditPrice)
                                    <flux:field>
                                        <flux:label>Number of Credits</flux:label>
                                        <flux:select wire:model.live="creditQuantity">
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}">{{ $i }} credit{{ $i > 1 ? 's' : '' }}</option>
                                            @endfor
                                        </flux:select>
                                        <flux:description>Each credit promotes your profile for 1 week.</flux:description>
                                    </flux:field>

                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <flux:text class="text-sm text-gray-500 dark:text-gray-400">Price per credit</flux:text>
                                            <flux:text class="text-sm font-medium">${{ number_format($this->promoCreditPrice->unit_amount / 100, 2) }}</flux:text>
                                        </div>
                                        <div class="flex items-center justify-between mb-2">
                                            <flux:text class="text-sm text-gray-500 dark:text-gray-400">Quantity</flux:text>
                                            <flux:text class="text-sm font-medium">{{ $creditQuantity }}</flux:text>
                                        </div>
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                            <div class="flex items-center justify-between">
                                                <flux:text class="font-medium">Total</flux:text>
                                                <flux:text class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                    ${{ number_format(($this->promoCreditPrice->unit_amount * $creditQuantity) / 100, 2) }}
                                                </flux:text>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                        <div class="flex items-start gap-3">
                                            <flux:icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                                            <div>
                                                <flux:text class="text-sm text-blue-700 dark:text-blue-300">
                                                    Your default payment method will be charged immediately.
                                                </flux:text>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <div class="flex items-start gap-3">
                                            <flux:icon name="exclamation-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                                            <div>
                                                <flux:text class="text-sm text-yellow-700 dark:text-yellow-300">
                                                    Promotion credit pricing is currently unavailable. Please try again later.
                                                </flux:text>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <flux:modal.close>
                                    <flux:button variant="ghost">Cancel</flux:button>
                                </flux:modal.close>
                                <flux:button
                                    wire:click="purchasePromotionCredits"
                                    variant="primary"
                                    wire:loading.attr="disabled"
                                    :disabled="!$this->promoCreditPrice">
                                    <span wire:loading.remove wire:target="purchasePromotionCredits">Purchase Credits</span>
                                    <span wire:loading wire:target="purchasePromotionCredits">Processing...</span>
                                </flux:button>
                            </div>
                        </div>
                    </flux:modal>

                    <flux:modal name="addPaymentMethodFirst">
                        <div class="p-6">
                            <flux:heading size="xl" class="mb-2">Payment Method Required</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
                                You need to add a payment method before purchasing promotion credits.
                            </flux:text>

                            <div class="flex justify-end gap-3 mt-6">
                                <flux:modal.close>
                                    <flux:button variant="ghost">Cancel</flux:button>
                                </flux:modal.close>
                                <flux:modal.close>
                                    <flux:button
                                        wire:click="setActiveTab('billing')"
                                        variant="primary">
                                        Go to Billing
                                    </flux:button>
                                </flux:modal.close>
                            </div>
                        </div>
                    </flux:modal>

                    <flux:separator />

                    <!-- Basic Business Information -->
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.building-office class="h-5 w-5 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Basic Business Information</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Update your business contact details</flux:text>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <flux:field>
                                <flux:label>Business Name *</flux:label>
                                <flux:input
                                    type="text"
                                    wire:model="business_name"
                                    placeholder="Enter your business name"
                                    required />
                                <flux:error name="business_name" />
                            </flux:field>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>Business Email *</flux:label>
                                    <flux:input
                                        type="email"
                                        wire:model="business_email"
                                        placeholder="business@example.com"
                                        required />
                                    <flux:error name="business_email" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Phone Number *</flux:label>
                                    <flux:input
                                        type="tel"
                                        wire:model="phone_number"
                                        placeholder="(555) 123-4567"
                                        required />
                                    <flux:error name="phone_number" />
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>Website (Optional)</flux:label>
                                <flux:input
                                    type="url"
                                    wire:model="website"
                                    placeholder="https://example.com" />
                            </flux:field>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>Primary Contact Name *</flux:label>
                                    <flux:input
                                        type="text"
                                        wire:model="contact_name"
                                        placeholder="Primary contact person"
                                        required />
                                    <flux:error name="contact_name" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Contact Role *</flux:label>
                                    <flux:select
                                        wire:model="contact_role"
                                        variant="listbox"
                                        placeholder="Select your role"
                                        required>
                                        @foreach ($contactRoleOptions as $role)
                                        <flux:select.option value="{{ $role['value'] }}">{{ $role['label'] }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="contact_role" />
                                </flux:field>
                            </div>
                        </div>
                    </div>

                    <flux:separator />

                    <!-- Business Profile & Identity -->
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.document-text class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Business Profile & Identity</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Tell us more about your business</flux:text>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>Years in Business *</flux:label>
                                    <flux:select
                                        wire:model="years_in_business"
                                        variant="listbox"
                                        placeholder="Select years in business"
                                        required>
                                        @foreach ($yearsInBusinessOptions as $years)
                                        <flux:select.option value="{{ $years['value'] }}">{{ $years['label'] }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="years_in_business" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Company Size *</flux:label>
                                    <flux:select
                                        wire:model="company_size"
                                        variant="listbox"
                                        placeholder="Select company size"
                                        required>
                                        @foreach ($companySizeOptions as $size)
                                        <flux:select.option value="{{ $size['value'] }}">{{ $size['label'] }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="company_size" />
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>Business Type *</flux:label>
                                    <flux:select
                                        wire:model="business_type"
                                        variant="listbox"
                                        placeholder="Select business type"
                                        required>
                                        @foreach ($businessTypeOptions as $type)
                                        <flux:select.option value="{{ $type['value'] }}">{{ $type['label'] }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="business_type" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Primary Industry *</flux:label>
                                    <flux:select
                                        wire:model="industry"
                                        variant="listbox"
                                        placeholder="Select your primary industry"
                                        required>
                                        @foreach ($businessIndustryOptions as $industry)
                                        <flux:select.option value="{{ $industry['value'] }}">{{ $industry['label'] }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="industry" />
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label>Business Description *</flux:label>
                                <flux:textarea
                                    wire:model="business_description"
                                    rows="4"
                                    placeholder="Describe your business, what you do, and what makes you unique..."
                                    required />
                                <flux:error name="business_description" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Unique Value Proposition (Optional)</flux:label>
                                <flux:textarea
                                    wire:model="unique_value_proposition"
                                    rows="2"
                                    placeholder="What sets your business apart from competitors?" />
                            </flux:field>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Branding Tab -->
            @if($activeTab === 'branding')
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-pink-100 dark:bg-pink-900 rounded-lg flex items-center justify-center">
                                <flux:icon.photo class="h-5 w-5 text-pink-600 dark:text-pink-400" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <flux:heading>Business Images</flux:heading>
                            <flux:text class="text-sm text-zinc-500">Upload your logo and banner image</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Logo -->
                        <div>
                            <flux:label>Business Logo</flux:label>
                            <div class="mt-2">
                                @if($user->currentBusiness?->getLogoUrl())
                                <div class="mb-4">
                                    <img src="{{ $user->currentBusiness->getLogoUrl() }}" alt="Current business logo" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current business logo</p>
                                </div>
                                @endif

                                <div class="flex items-center justify-center w-full">
                                    <label for="business_logo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <flux:icon.cloud-arrow-up class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" />
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                        </div>
                                        <input id="business_logo" type="file" wire:model="business_logo" class="hidden" accept="image/*" />
                                    </label>
                                </div>

                                @if ($business_logo)
                                <div class="mt-4">
                                    <img src="{{ $business_logo->temporaryUrl() }}" alt="Logo preview" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                                    <p class="text-sm text-green-600 dark:text-green-400 mt-2">New logo ready to save</p>
                                </div>
                                @endif

                                @error('business_logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Business Banner -->
                        <div>
                            <flux:label>Business Banner</flux:label>
                            <div class="mt-2">
                                @if($user->currentBusiness?->getBannerImageUrl())
                                <div class="mb-4">
                                    <img src="{{ $user->currentBusiness->getBannerImageUrl() }}" alt="Current business banner" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current business banner</p>
                                </div>
                                @endif

                                <div class="flex items-center justify-center w-full">
                                    <label for="business_banner" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <flux:icon.cloud-arrow-up class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" />
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                        </div>
                                        <input id="business_banner" type="file" wire:model="business_banner" class="hidden" accept="image/*" />
                                    </label>
                                </div>

                                @if ($business_banner)
                                <div class="mt-4">
                                    <img src="{{ $business_banner->temporaryUrl() }}" alt="Banner preview" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                    <p class="text-sm text-green-600 dark:text-green-400 mt-2">New banner ready to save</p>
                                </div>
                                @endif

                                @error('business_banner') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Location Tab -->
            @if($activeTab === 'location')
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                <flux:icon.map-pin class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <flux:heading>Location & Target Demographics</flux:heading>
                            <flux:text class="text-sm text-zinc-500">Set your business location and target audience</flux:text>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <flux:field>
                            <flux:label>City</flux:label>
                            <flux:input
                                type="text"
                                wire:model="city"
                                placeholder="Your city" />
                        </flux:field>

                        <flux:field>
                            <flux:label>State</flux:label>
                            <flux:input
                                type="text"
                                wire:model="state"
                                placeholder="Your state" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Postal Code</flux:label>
                            <flux:input
                                type="text"
                                wire:model="postal_code"
                                placeholder="12345" />
                        </flux:field>
                    </div>

                    <flux:separator />

                    <!-- Target Gender -->
                    <div>
                        <flux:label>Target Gender (Optional)</flux:label>
                        <flux:text class="text-sm text-zinc-500 mb-3">Who is your target audience by gender?</flux:text>
                        @foreach($target_gender as $index => $gender)
                        <div class="flex items-center space-x-2 mt-2">
                            <flux:select
                                wire:model="target_gender.{{ $index }}"
                                variant="listbox"
                                placeholder="Select target gender"
                                class="flex-1">
                                <flux:select.option value="male">Male</flux:select.option>
                                <flux:select.option value="female">Female</flux:select.option>
                                <flux:select.option value="non-binary">Non-binary</flux:select.option>
                                <flux:select.option value="all">All Genders</flux:select.option>
                            </flux:select>
                            @if(count($target_gender) > 1)
                            <flux:button
                                type="button"
                                variant="danger"
                                size="sm"
                                wire:click="removeTargetGender({{ $index }})">
                                Remove
                            </flux:button>
                            @endif
                        </div>
                        @endforeach
                        <flux:button
                            type="button"
                            variant="ghost"
                            size="sm"
                            wire:click="addTargetGender"
                            class="mt-2">
                            + Add Target Gender
                        </flux:button>
                    </div>

                    <flux:separator />

                    <!-- Target Age Range -->
                    <div>
                        <flux:label>Target Age Range (Optional)</flux:label>
                        <flux:text class="text-sm text-zinc-500 mb-3">What age groups do you target?</flux:text>
                        @foreach($target_age_range as $index => $ageRange)
                        <div class="flex items-center space-x-2 mt-2">
                            <flux:select
                                wire:model="target_age_range.{{ $index }}"
                                variant="listbox"
                                placeholder="Select age range"
                                class="flex-1">
                                <flux:select.option value="13-17">13-17 (Gen Alpha/Late Gen Z)</flux:select.option>
                                <flux:select.option value="18-24">18-24 (Gen Z)</flux:select.option>
                                <flux:select.option value="25-34">25-34 (Millennials)</flux:select.option>
                                <flux:select.option value="35-44">35-44 (Millennials/Gen X)</flux:select.option>
                                <flux:select.option value="45-54">45-54 (Gen X)</flux:select.option>
                                <flux:select.option value="55-64">55-64 (Boomers)</flux:select.option>
                                <flux:select.option value="65+">65+ (Boomers+)</flux:select.option>
                            </flux:select>
                            @if(count($target_age_range) > 1)
                            <flux:button
                                type="button"
                                variant="danger"
                                size="sm"
                                wire:click="removeTargetAge({{ $index }})">
                                Remove
                            </flux:button>
                            @endif
                        </div>
                        @endforeach
                        <flux:button
                            type="button"
                            variant="ghost"
                            size="sm"
                            wire:click="addTargetAge"
                            class="mt-2">
                            + Add Age Range
                        </flux:button>
                    </div>
                </div>
            @endif

            <!-- Social Tab -->
            @if($activeTab === 'social')
                <div class="space-y-8">
                    <!-- Social Media Accounts -->
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.at-symbol class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Social Media Accounts</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Add your social media handles to showcase your online presence</flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Instagram Handle</flux:label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                                    <flux:input
                                        type="text"
                                        wire:model="instagram_handle"
                                        placeholder="username"
                                        class="rounded-l-none" />
                                </div>
                            </flux:field>

                            <flux:field>
                                <flux:label>Facebook Handle</flux:label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                                    <flux:input
                                        type="text"
                                        wire:model="facebook_handle"
                                        placeholder="username"
                                        class="rounded-l-none" />
                                </div>
                            </flux:field>

                            <flux:field>
                                <flux:label>TikTok Handle</flux:label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                                    <flux:input
                                        type="text"
                                        wire:model="tiktok_handle"
                                        placeholder="username"
                                        class="rounded-l-none" />
                                </div>
                            </flux:field>

                            <flux:field>
                                <flux:label>LinkedIn Handle</flux:label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-l-md">@</span>
                                    <flux:input
                                        type="text"
                                        wire:model="linkedin_handle"
                                        placeholder="username"
                                        class="rounded-l-none" />
                                </div>
                            </flux:field>
                        </div>
                    </div>

                    <flux:separator />

                    <!-- Business Goals & Platform Preferences -->
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.chart-bar class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Business Goals & Platform Preferences</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Define your influencer marketing objectives</flux:text>
                            </div>
                        </div>

                        <!-- Business Goals -->
                        <div class="mb-6">
                            <flux:label>Business Goals (Optional)</flux:label>
                            <flux:text class="text-sm text-zinc-500 mb-3">What are your primary business goals for influencer marketing?</flux:text>
                            @foreach($business_goals as $index => $goal)
                            <div class="flex items-center space-x-2 mt-2">
                                <flux:select
                                    wire:model="business_goals.{{ $index }}"
                                    variant="listbox"
                                    placeholder="Select a business goal"
                                    class="flex-1">
                                    <flux:select.option value="brand_awareness">Brand Awareness</flux:select.option>
                                    <flux:select.option value="product_promotion">Product Promotion</flux:select.option>
                                    <flux:select.option value="growth_scaling">Growth & Scaling</flux:select.option>
                                    <flux:select.option value="new_market_entry">New Market Entry</flux:select.option>
                                    <flux:select.option value="community_building">Community Building</flux:select.option>
                                    <flux:select.option value="customer_retention">Customer Retention</flux:select.option>
                                </flux:select>
                                @if(count($business_goals) > 1)
                                <flux:button
                                    type="button"
                                    variant="danger"
                                    size="sm"
                                    wire:click="removeBusinessGoal({{ $index }})">
                                    Remove
                                </flux:button>
                                @endif
                            </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addBusinessGoal"
                                class="mt-2">
                                + Add Goal
                            </flux:button>
                        </div>

                        <!-- Preferred Platforms -->
                        <div>
                            <flux:label>Preferred Platforms (Optional)</flux:label>
                            <flux:text class="text-sm text-zinc-500 mb-3">Which social media platforms do you want to focus on?</flux:text>
                            @foreach($platforms as $index => $platform)
                            <div class="flex items-center space-x-2 mt-2">
                                <flux:select
                                    wire:model="platforms.{{ $index }}"
                                    variant="listbox"
                                    placeholder="Select a platform"
                                    class="flex-1">
                                    @foreach ($socialPlatformOptions as $option)
                                    <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @if(count($platforms) > 1)
                                <flux:button
                                    type="button"
                                    variant="danger"
                                    size="sm"
                                    wire:click="removePlatform({{ $index }})">
                                    Remove
                                </flux:button>
                                @endif
                            </div>
                            @endforeach
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                wire:click="addPlatform"
                                class="mt-2">
                                + Add Platform
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Campaigns Tab -->
            @if($activeTab === 'campaigns')
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                <flux:icon.document-duplicate class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <flux:heading>Campaign Defaults</flux:heading>
                            <flux:text class="text-sm text-zinc-500">Pre-fill brand information for faster campaign creation</flux:text>
                        </div>
                    </div>

                    <flux:callout icon="information-circle" class="dark:bg-zinc-800 dark:text-zinc-300">
                        These defaults will auto-fill when creating new campaigns, saving you time. You can always edit them per campaign.
                    </flux:callout>

                    <div class="space-y-6">
                        <flux:heading>Brand Information Defaults</flux:heading>

                        <flux:field>
                            <flux:label>Default Brand Overview</flux:label>
                            <flux:editor
                                wire:model="default_brand_overview"
                                placeholder="Describe your brand for influencers - your mission, values, and what makes you unique..." />
                            <flux:description>This will auto-fill the Brand Overview field when creating campaigns</flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>Default Brand Story</flux:label>
                            <flux:editor
                                wire:model="default_brand_story"
                                placeholder="Share your brand's story - how you started, your journey, and what drives you..." />
                        </flux:field>

                        <flux:field>
                            <flux:label>Default Brand Guidelines</flux:label>
                            <flux:editor
                                wire:model="default_brand_guidelines"
                                placeholder="Standard brand guidelines for influencers - tone of voice, visual style, dos and don'ts..." />
                        </flux:field>

                        <flux:field>
                            <flux:label>Current Advertising Campaign</flux:label>
                            <flux:editor
                                wire:model="default_current_advertising_campaign"
                                placeholder="Describe any ongoing marketing initiatives influencers should know about..." />
                        </flux:field>

                        <flux:separator />

                        <flux:heading>Briefing Defaults (Optional)</flux:heading>
                        <flux:text class="text-zinc-500 mb-4">These help pre-fill the campaign briefing section</flux:text>

                        <flux:field>
                            <flux:label>Default Key Insights</flux:label>
                            <flux:editor
                                wire:model="default_key_insights"
                                placeholder="Insights about your target audience - who they are, what they care about..." />
                        </flux:field>

                        <flux:field>
                            <flux:label>Default Fan Motivator</flux:label>
                            <flux:editor
                                wire:model="default_fan_motivator"
                                placeholder="What motivates your fans/customers to engage with your brand..." />
                        </flux:field>

                        <flux:field>
                            <flux:label>Default Posting Restrictions</flux:label>
                            <flux:editor
                                wire:model="default_posting_restrictions"
                                placeholder="Standard content restrictions - topics to avoid, competitor mentions, etc..." />
                        </flux:field>
                    </div>
                </div>
            @endif

            <!-- Team Tab -->
            @if($activeTab === 'team')
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-teal-100 dark:bg-teal-900 rounded-lg flex items-center justify-center">
                                <flux:icon.user-group class="h-5 w-5 text-teal-600 dark:text-teal-400" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <flux:heading>Business Members</flux:heading>
                            <flux:text class="text-sm text-zinc-500">Add team members who can manage this business profile</flux:text>
                        </div>
                    </div>

                    {{-- Team Member Limit Info --}}
                    <livewire:components.subscription-limit-info
                        limit-key="{{ \App\Subscription\SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT }}"
                        action-text="Adding a team member"
                        credit-name="team member slot"
                    />

                    <!-- Invite form -->
                    <div class="flex flex-row items-center mb-6 space-x-2">
                        <flux:input
                            type="email"
                            wire:model="invite_email"
                            placeholder="Enter email to invite"
                            class="flex-1"
                            />
                        <flux:button
                            type="button"
                            variant="primary"
                            wire:click="sendInvite">
                            Send Invite
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        @foreach($user->currentBusiness->members as $member)
                        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <flux:avatar size="sm" name="{{ $member->name }}" />
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $member->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <flux:badge>{{ ucfirst($member->pivot->role) }}</flux:badge>
                                <flux:button variant="ghost" size="sm" wire:click="removeMember({{ $member->id }})">
                                    Remove
                                </flux:button>
                            </div>
                        </div>
                        @endforeach

                        @foreach($user->currentBusiness->pendingInvites as $invite)
                        <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $invite->email }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Invited on {{ $invite->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <flux:badge color="yellow">Pending</flux:badge>
                                <flux:button variant="ghost" size="sm" wire:click="rescindInvite({{ $invite->id }})">
                                    Rescind
                                </flux:button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
    </flux:card>
    @endif
</form>

<!-- Billing Tab - Outside form to prevent form submission conflicts -->
@if($activeTab === 'billing')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <flux:card>
            <div class="space-y-6">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 bg-emerald-100 dark:bg-emerald-900 rounded-lg flex items-center justify-center">
                            <flux:icon.credit-card class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <flux:heading>Billing & Subscription</flux:heading>
                        <flux:text class="text-sm text-zinc-500">Manage your subscription, payment methods, and billing history</flux:text>
                    </div>
                </div>

                <livewire:components.billing-manager
                    :billable="$user->currentBusiness"
                    :initial-section="$activeSubtab"
                    @section-changed="setBillingSubtab($event.detail.section)"
                />
            </div>
        </flux:card>
    </div>
@endif
</div>
