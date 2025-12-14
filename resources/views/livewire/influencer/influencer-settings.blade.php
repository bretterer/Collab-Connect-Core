<div
    x-data
    @update-url.window="window.history.replaceState({}, '', $event.detail.url)"
>
<form wire:submit="updateInfluencerSettings" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Your Profile</flux:heading>
            <flux:text class="text-zinc-500">Manage your creator profile, social accounts, and preferences</flux:text>
        </div>
        <div class="flex items-center gap-3">
            @if($username)
                <flux:button href="{{ route('influencer.profile', $username) }}" variant="ghost" icon="eye">
                    Preview
                </flux:button>
            @endif
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
            wire:click="setActiveTab('account')"
            icon="cog-6-tooth"
            :current="$activeTab === 'account'">
            Account Settings
            @if($this->tabNeedsAttention['account'])
                <span class="ml-1 inline-block w-1.5 h-1.5 bg-red-500 rounded-full"></span>
            @endif
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('match')"
            icon="sparkles"
            :current="$activeTab === 'match'">
            Match Profile
            @if($this->tabNeedsAttention['match'])
                <span class="ml-1 inline-block w-1.5 h-1.5 bg-red-500 rounded-full"></span>
            @endif
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('social')"
            icon="share"
            :current="$activeTab === 'social'">
            Social Profiles
            @if($this->tabNeedsAttention['social'])
                <span class="ml-1 inline-block w-1.5 h-1.5 bg-red-500 rounded-full"></span>
            @endif
        </flux:navbar.item>
        <flux:navbar.item
            wire:click="setActiveTab('portfolio')"
            icon="photo"
            :current="$activeTab === 'portfolio'">
            Portfolio
            @if($this->tabNeedsAttention['portfolio'])
                <span class="ml-1 inline-block w-1.5 h-1.5 bg-red-500 rounded-full"></span>
            @endif
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
            {{-- Account Settings Tab --}}
            @if($activeTab === 'account')
                <div class="space-y-8">
                    {{-- Campaign Active Toggle --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.play class="h-5 w-5 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Campaign Active</flux:heading>
                                <flux:text class="text-sm text-zinc-500">If you would like to take a break from working with brands you can pause your account using the toggle below.</flux:text>
                            </div>
                        </div>
                        <flux:switch wire:model.live="is_campaign_active" label="Active" />
                    </div>

                    <flux:separator />

                    {{-- Invitations Active Toggle --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.envelope class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Invitations Active</flux:heading>
                                <flux:text class="text-sm text-zinc-500">If you want to opt out of receiving invitations for work opportunities from brands.</flux:text>
                            </div>
                        </div>
                        <flux:switch wire:model.live="is_accepting_invitations" label="Active" />
                    </div>

                    <flux:separator />

                    {{-- Username --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.at-symbol class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Profile Username</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Set your unique public profile URL</flux:text>
                            </div>
                        </div>

                        <flux:field>
                            <flux:label>Username</flux:label>
                            <flux:input
                                type="text"
                                wire:model.live.debounce.500ms="username"
                                placeholder="yourusername" />
                            <flux:error name="username" />
                            @if($username && !$errors->has('username'))
                                <div class="flex items-center gap-2 mt-2 text-sm text-green-600 dark:text-green-400">
                                    <flux:icon.check-circle class="w-4 h-4" />
                                    <span>{{ config('app.url') }}/influencer/{{ $username }} is available!</span>
                                </div>
                            @else
                                <flux:description class="mt-2">Your unique public profile URL will be: <span class="font-mono">{{ config('app.url') }}/influencer/{{ $username ?: 'your-username' }}</span></flux:description>
                            @endif
                        </flux:field>
                    </div>

                    <flux:separator />

                    {{-- Profile Images --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-pink-100 dark:bg-pink-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.photo class="h-5 w-5 text-pink-600 dark:text-pink-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Profile Images</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Upload your profile photo and banner image</flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Profile Image -->
                            <div>
                                <flux:label>Profile Image</flux:label>
                                <div class="mt-2">
                                    @if($user->influencer?->getProfileImageUrl())
                                    <div class="mb-4">
                                        <img src="{{ $user->influencer->getProfileImageUrl() }}" alt="Current profile image" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current profile image</p>
                                    </div>
                                    @endif

                                    <div class="flex items-center justify-center w-full">
                                        <label for="profile_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <flux:icon.cloud-arrow-up class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" />
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                            </div>
                                            <input id="profile_image" type="file" wire:model="profile_image" class="hidden" accept="image/*" />
                                        </label>
                                    </div>

                                    @if ($profile_image)
                                    <div class="mt-4">
                                        <img src="{{ $profile_image->temporaryUrl() }}" alt="Profile preview" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">New profile image ready to save</p>
                                    </div>
                                    @endif

                                    @error('profile_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Banner Image -->
                            <div>
                                <flux:label>Banner Image</flux:label>
                                <div class="mt-2">
                                    @if($user->influencer?->getBannerImageUrl())
                                    <div class="mb-4">
                                        <img src="{{ $user->influencer->getBannerImageUrl() }}" alt="Current banner image" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Current banner image</p>
                                    </div>
                                    @endif

                                    <div class="flex items-center justify-center w-full">
                                        <label for="banner_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <flux:icon.cloud-arrow-up class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" />
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP (MAX. 5MB)</p>
                                            </div>
                                            <input id="banner_image" type="file" wire:model="banner_image" class="hidden" accept="image/*" />
                                        </label>
                                    </div>

                                    @if ($banner_image)
                                    <div class="mt-4">
                                        <img src="{{ $banner_image->temporaryUrl() }}" alt="Banner preview" class="w-full h-24 rounded-lg object-cover border-4 border-white shadow-lg">
                                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">New banner image ready to save</p>
                                    </div>
                                    @endif

                                    @error('banner_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <flux:separator />

                    {{-- Location & Contact Information --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.map-pin class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Location & Contact Information</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Your location helps match you with local businesses (35% of match score)</flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <flux:field>
                                <flux:label>City *</flux:label>
                                <flux:input type="text" wire:model="city" placeholder="Your city" required />
                                <flux:error name="city" />
                            </flux:field>

                            <flux:field>
                                <flux:label>State *</flux:label>
                                <flux:input type="text" wire:model="state" placeholder="Your state" required />
                                <flux:error name="state" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Postal Code *</flux:label>
                                <flux:input type="text" wire:model="postal_code" placeholder="12345" required />
                                <flux:error name="postal_code" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>Phone Number *</flux:label>
                            <flux:input type="tel" wire:model="phone_number" placeholder="(555) 123-4567" required />
                            <flux:error name="phone_number" />
                        </flux:field>
                    </div>
                </div>
            @endif

            {{-- Match Profile Tab --}}
            @if($activeTab === 'match')
                <div class="space-y-8">
                    {{-- About Yourself --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.user class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Tell us about yourself</flux:heading>
                                <flux:text class="text-sm text-zinc-500">The more interesting and relevant information you provide, the higher the chances of being approved by the brands.</flux:text>
                            </div>
                        </div>

                        <flux:editor
                            wire:model="about_yourself"
                            placeholder="What makes you special? Make this detailed."
                            toolbar="bold italic | bullet ordered | link" />
                        <flux:error name="about_yourself" />
                    </div>

                    <flux:separator />

                    {{-- Passions --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-pink-100 dark:bg-pink-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.heart class="h-5 w-5 text-pink-600 dark:text-pink-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Highlight your passions</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Share more about your unique interests, hobbies, and experiences. The more detailed and captivating your story is, the greater your chances of resonating with brands.</flux:text>
                            </div>
                        </div>

                        <flux:editor
                            wire:model="passions"
                            placeholder="What are you really into? Make this detailed."
                            toolbar="bold italic | bullet ordered | link" />
                        <flux:error name="passions" />
                    </div>

                    <flux:separator />

                    {{-- Creator Account / Industry --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.sparkles class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Your creator account</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Select the industry and content types that best describe you. This affects 35% of your match score.</flux:text>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <flux:field>
                                <flux:label>Primary Industry</flux:label>
                                <flux:select wire:model="primary_industry" variant="listbox" placeholder="Select your primary industry">
                                    @foreach ($businessIndustryOptions as $option)
                                        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <flux:pillbox
                                wire:model.live="content_types"
                                label="Content Types"
                                description="What types of content do you create? Select up to 3."
                                placeholder="Select content types..."
                                multiple>
                                @foreach ($campaignTypeOptions as $option)
                                    <flux:pillbox.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:pillbox.option>
                                @endforeach
                            </flux:pillbox>

                            <flux:field>
                                <flux:label>Lead time for creating content (in days) *</flux:label>
                                <flux:input type="number" wire:model="typical_lead_time_days" placeholder="7" min="1" max="365" required />
                                <flux:description>How much advance notice do you typically need for a collaboration?</flux:description>
                                <flux:error name="typical_lead_time_days" />
                            </flux:field>
                        </div>
                    </div>

                    <flux:separator />

                    {{-- Collaboration / Deliverables --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.video-camera class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Collaboration</flux:heading>
                                <flux:text class="text-sm text-zinc-500">What types of content can you deliver for brands?</flux:text>
                            </div>
                        </div>

                        <flux:pillbox
                            wire:model="deliverable_types"
                            label="Deliverable Types"
                            description="What types of content can you create for brands?"
                            placeholder="Select deliverables..."
                            multiple>
                            @foreach ($deliverableTypeOptions as $option)
                                <flux:pillbox.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:pillbox.option>
                            @endforeach
                        </flux:pillbox>
                    </div>

                    <flux:separator />

                    {{-- Payment / Compensation --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.currency-dollar class="h-5 w-5 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Payment</flux:heading>
                                <flux:text class="text-sm text-zinc-500">How would you like to be compensated for collaborations? This affects 10% of your match score.</flux:text>
                            </div>
                        </div>

                        <flux:pillbox
                            wire:model.live="compensation_types"
                            label="Preferred Compensation Types"
                            description="How would you like to be compensated? Select up to 3."
                            placeholder="Select compensation types..."
                            multiple>
                            @foreach ($compensationTypeOptions as $option)
                                <flux:pillbox.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:pillbox.option>
                            @endforeach
                        </flux:pillbox>
                    </div>

                    <flux:separator />

                    {{-- Brands Preferences --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <flux:icon.building-storefront class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:heading>Brands that you like</flux:heading>
                                <flux:text class="text-sm text-zinc-500">Let us know what kind of brands are interesting to you, so that we give you more accurate recommendations.</flux:text>
                            </div>
                        </div>

                        <flux:pillbox
                            wire:model="preferred_business_types"
                            label="Preferred Business Types"
                            description="What types of businesses do you prefer to work with? Select up to 2."
                            placeholder="Select business types..."
                            multiple>
                            @foreach ($businessTypeOptions as $option)
                                <flux:pillbox.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:pillbox.option>
                            @endforeach
                        </flux:pillbox>
                    </div>
                </div>
            @endif

            {{-- Social Profiles Tab --}}
            @if($activeTab === 'social')
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                <flux:icon.share class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <flux:heading>Social Media Accounts</flux:heading>
                            <flux:text class="text-sm text-zinc-500">Connect your social media accounts to showcase your reach</flux:text>
                        </div>
                    </div>

                    @foreach($social_accounts as $platform => $account)
                        @php $platformEnum = \App\Enums\SocialPlatform::from($platform) @endphp
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $platformEnum->getStyleClasses() }}">
                                    {!! $platformEnum->svg('w-5 h-5') !!}
                                </div>
                                <div>
                                    <flux:heading>{{ $platformEnum->label() }} Account</flux:heading>
                                    <flux:text class="text-sm text-zinc-500">Manage your {{ $platformEnum->label() }} connection.</flux:text>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>Username</flux:label>
                                    <flux:input
                                        type="text"
                                        wire:model="social_accounts.{{ $platform }}.username"
                                        placeholder="Username (without @)" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>Follower count</flux:label>
                                    <flux:input
                                        type="number"
                                        wire:model="social_accounts.{{ $platform }}.followers"
                                        placeholder="Follower count" />
                                </flux:field>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Portfolio Tab --}}
            @if($activeTab === 'portfolio')
                <div class="space-y-6">
                    <livewire:components.coming-soon
                        title="Professional Portfolio"
                        description="Showcase your best work to brands and increase your chances of landing collaborations."
                        icon="photo"
                        :features="[
                            'Upload videos and photos',
                            'Highlight your best content',
                            'Show previous brand collaborations',
                            'Stand out to potential partners',
                        ]"
                    />
                </div>
            @endif
    </flux:card>
    @endif
</form>

{{-- Billing Tab - Outside form to prevent form submission conflicts --}}
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
                    :billable="$user->influencer"
                    :initial-section="$activeSubtab"
                    @section-changed="setBillingSubtab($event.detail.section)"
                />
            </div>
        </flux:card>
    </div>
@endif
</div>
