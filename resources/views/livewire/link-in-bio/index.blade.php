<div>
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Page Header --}}
    <div class="mb-6">
        <flux:heading size="xl">Link in Bio</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400">Welcome to your public page dashboard.</flux:text>
    </div>

    {{-- Username Required Warning --}}
    @if(!$this->hasUsername())
        <flux:callout color="amber" icon="exclamation-triangle" class="mb-6">
            <flux:callout.heading>Username required</flux:callout.heading>
            <flux:callout.text>
                You need to set a username in your profile before you can use your Link in Bio page.
                <flux:link href="{{ url('/influencer/settings') }}" class="underline font-medium">Update your profile</flux:link>
            </flux:callout.text>
        </flux:callout>
    @endif

    {{-- Public URL Card --}}
    <flux:card class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    @if($isPublished)
                        <flux:badge color="green" size="sm">Published</flux:badge>
                    @else
                        <flux:badge color="zinc" size="sm">Draft</flux:badge>
                    @endif
                </div>
                @if($this->hasUsername())
                    <flux:text class="text-gray-600 dark:text-gray-400">
                        @if($isPublished)
                            Your public profile is live. Share this link with brands or put it in your social media profiles.
                        @else
                            Your page is not published yet. Enable publishing to make it visible to others.
                        @endif
                    </flux:text>
                    <flux:link href="{{ $this->getPublicUrl() }}" target="_blank" class="mt-2 text-blue-600 dark:text-blue-400 underline block">
                        {{ $this->getPublicUrl() }}
                    </flux:link>
                @else
                    <flux:text class="text-gray-500 dark:text-gray-400">
                        Set a username to get your public link.
                    </flux:text>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <flux:switch wire:model.live="isPublished" wire:change="togglePublish" label="Publish" :disabled="!$this->hasUsername()" />
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="clipboard"
                    :disabled="!$this->hasUsername()"
                    x-on:click="navigator.clipboard.writeText('{{ $this->getPublicUrl() }}'); $flux.toast('Link copied!')"
                >
                    Copy
                </flux:button>
                <flux:button
                    :href="$this->hasUsername() ? $this->getPublicUrl() : null"
                    target="_blank"
                    variant="outline"
                    size="sm"
                    icon="eye"
                    :disabled="!$this->hasUsername()"
                >
                    View
                </flux:button>
                <flux:button
                    href="{{ route('link-in-bio.analytics') }}"
                    wire:navigate
                    variant="outline"
                    size="sm"
                    icon="chart-bar"
                >
                    Analytics
                    @if(!auth()->user()?->influencer?->hasFeatureAccess('analytics_advanced'))
                        <flux:badge size="sm" color="amber" class="ml-1">Elite</flux:badge>
                    @endif
                </flux:button>
            </div>
        </div>
    </flux:card>

    {{-- Main Two-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Left Column: Settings --}}
        <div class="space-y-4">
            {{-- Design Section --}}
            <flux:card class="p-0 overflow-hidden">
                <flux:accordion transition>
                    <flux:accordion.item>
                        <flux:accordion.heading class="py-4 px-4">
                            <div class="flex items-center gap-3 w-full">
                                <flux:icon name="paint-brush" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                <span class="font-semibold text-gray-900 dark:text-white">Design</span>
                                @if(!$this->hasCustomizationAccess)
                                    <x-upgrade-badge tier="{{ $this->requiredTierForCustomization }}" size="xs" class="ml-auto" />
                                @endif
                            </div>
                        </flux:accordion.heading>
                        <flux:accordion.content>
                            <x-tier-locked
                                :locked="!$this->hasCustomizationAccess"
                                :required-tier="$this->requiredTierForCustomization"
                                :current-tier="$this->currentTier"
                                title="Elite Feature"
                                description="Customize your page design with the Elite plan."
                                overlay-style="blur"
                            >
                                <div class="space-y-6 p-4 pt-0">
                                    {{-- Color Picker --}}
                                    <div>
                                        <flux:label class="mb-3">Theme Color</flux:label>
                                        <div class="flex gap-2 flex-wrap">
                                            @foreach(['#f97316', '#000000', '#3b82f6', '#22c55e', '#8b5cf6', '#dc2626', '#f5f5f4', '#eab308'] as $color)
                                                <button
                                                    type="button"
                                                    wire:click="$set('themeColor', '{{ $color }}')"
                                                    class="w-10 h-10 rounded-full border-2 transition-all {{ $themeColor === $color ? 'border-gray-900 dark:border-white ring-2 ring-offset-2 ring-gray-400' : 'border-gray-200 dark:border-gray-600' }}"
                                                    style="background-color: {{ $color }}"
                                                ></button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Container Style --}}
                                    <div>
                                        <flux:label class="mb-3">Container Style</flux:label>
                                        <div class="grid grid-cols-3 gap-3">
                                            @foreach(['square' => 'Square', 'round' => 'Round', 'full' => 'Full Width'] as $value => $label)
                                                <button
                                                    type="button"
                                                    wire:click="$set('containerStyle', '{{ $value }}')"
                                                    class="px-4 py-2 border rounded-lg text-sm transition-all {{ $containerStyle === $value ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                                >
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Font Selection --}}
                                    <div>
                                        <flux:label class="mb-3">Font</flux:label>
                                        <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                            @foreach(['sans' => 'Sans', 'serif' => 'Serif', 'mono' => 'Mono', 'georgia' => 'Georgia', 'arial' => 'Arial', 'times' => 'Times'] as $value => $label)
                                                <button
                                                    type="button"
                                                    wire:click="$set('font', '{{ $value }}')"
                                                    class="px-3 py-2 border rounded-lg text-sm transition-all {{ $font === $value ? 'border-gray-900 dark:border-white bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600' }}"
                                                    style="font-family: {{ $value === 'sans' ? 'ui-sans-serif, system-ui' : ($value === 'serif' ? 'ui-serif, Georgia' : ($value === 'mono' ? 'ui-monospace, monospace' : $value)) }}"
                                                >
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </x-tier-locked>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>
            </flux:card>

            {{-- Header Section --}}
            <livewire:link-in-bio.sections.header.index :settings="$headerSettings" key="header-section" />

            {{-- Links Section --}}
            <livewire:link-in-bio.sections.links.index :settings="$linksSettings" key="links-section" />

            {{-- Work With Me Section --}}
            <livewire:link-in-bio.sections.work-with-me.index :settings="$workWithMeSettings" key="work-with-me-section" />

            {{-- Join Referral Section (Elite only, requires referral enrollment) --}}
            <livewire:link-in-bio.sections.join-referral.index :settings="$joinReferralSettings" key="join-referral-section" />

            {{-- Footer Section --}}
            <livewire:link-in-bio.sections.footer.index :settings="$footerSettings" key="footer-section" />

            {{-- Save Button --}}
            <flux:button variant="primary" class="w-full" wire:click="save">Save Changes</flux:button>
        </div>

        {{-- Right Column: Live Preview --}}
        <div class="lg:sticky lg:top-8 lg:self-start">
            <div class="flex justify-center">
                {{-- Phone Frame --}}
                <div class="relative">
                    {{-- Phone Bezel --}}
                    <div class="w-[320px] h-[650px] bg-gray-900 dark:bg-black rounded-[3rem] p-3 shadow-2xl">
                        {{-- Phone Notch --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-7 bg-gray-900 dark:bg-black rounded-b-2xl z-10"></div>

                        {{-- Phone Screen --}}
                        <div
                            wire:key="phone-preview-{{ $themeColor }}-{{ $font }}"
                            class="w-full h-full rounded-[2.25rem] overflow-hidden overflow-y-auto ring-2 ring-gray-700 dark:ring-gray-800 scrollbar-hide"
                            style="background-color: {{ $themeColor }}; font-family: {{ $font === 'sans' ? 'ui-sans-serif, system-ui' : ($font === 'serif' ? 'ui-serif, Georgia' : ($font === 'mono' ? 'ui-monospace, monospace' : $font)) }}; scrollbar-width: none; -ms-overflow-style: none;"
                            x-data="{
                                themeColor: '{{ $themeColor }}',
                                isLightBackground: false,
                                init() {
                                    this.calculateContrast();
                                    this.$watch('themeColor', () => this.calculateContrast());
                                },
                                calculateContrast() {
                                    let hex = this.themeColor.replace('#', '');
                                    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
                                    const r = parseInt(hex.substr(0, 2), 16) / 255;
                                    const g = parseInt(hex.substr(2, 2), 16) / 255;
                                    const b = parseInt(hex.substr(4, 2), 16) / 255;
                                    const toLinear = c => c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
                                    const luminance = 0.2126 * toLinear(r) + 0.7152 * toLinear(g) + 0.0722 * toLinear(b);
                                    this.isLightBackground = luminance > 0.5;
                                },
                                get textColor() { return this.isLightBackground ? 'text-gray-900' : 'text-white'; },
                                get textColorMuted() { return this.isLightBackground ? 'text-gray-700' : 'text-white/80'; },
                                get textColorSubtle() { return this.isLightBackground ? 'text-gray-600' : 'text-white/90'; },
                                get textColorFaint() { return this.isLightBackground ? 'text-gray-500' : 'text-white/60'; },
                                get iconBgColor() { return this.isLightBackground ? 'bg-black/10' : 'bg-white/20'; },
                                get iconBgHoverColor() { return this.isLightBackground ? 'bg-black/20' : 'bg-white/30'; },
                                get linkBgColor() { return this.isLightBackground ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)'; },
                                get borderColor() { return this.isLightBackground ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.3)'; },
                                get profileBorderClass() { return this.isLightBackground ? 'border-gray-900' : 'border-white'; }
                            }"
                        >
                            {{-- Preview Content --}}
                            <div class="p-6 pt-10">
                                {{-- Share Button --}}
                                @if($headerSettings['showShareButton'] ?? true)
                                    <div class="flex justify-end mb-4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="iconBgColor">
                                            <svg class="w-4 h-4" :class="textColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                {{-- Header Section (using shared Show view) --}}
                                <div wire:key="header-preview-{{ $headerSettings['headerFormat'] ?? 'vertical' }}">
                                    @include('livewire.link-in-bio.sections.header.show', [
                                        'settings' => $headerSettings,
                                        'profileImageUrl' => auth()->user()?->influencer?->getProfileImageUrl(),
                                    ])
                                </div>

                                {{-- Links Section (using shared Show view) --}}
                                @include('livewire.link-in-bio.sections.links.show', [
                                    'settings' => $linksSettings,
                                    'designSettings' => ['containerStyle' => $containerStyle],
                                    'isPreview' => true,
                                ])

                                {{-- Work With Me Section --}}
                                @include('livewire.link-in-bio.sections.work-with-me.show', [
                                    'settings' => $workWithMeSettings,
                                    'designSettings' => ['containerStyle' => $containerStyle],
                                    'profileUrl' => route('influencer.profile', ['username' => auth()->user()?->influencer?->username ?: auth()->user()?->influencer?->user_id ?: 'profile']),
                                ])

                                {{-- Join Referral Section --}}
                                @if(auth()->user()?->referralEnrollment)
                                    @include('livewire.link-in-bio.sections.join-referral.show', [
                                        'settings' => $joinReferralSettings,
                                        'designSettings' => ['containerStyle' => $containerStyle],
                                        'referralUrl' => url('/r/' . auth()->user()->referralEnrollment->code),
                                    ])
                                @endif

                                {{-- Footer/Branding --}}
                                @include('livewire.link-in-bio.sections.footer.show')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
