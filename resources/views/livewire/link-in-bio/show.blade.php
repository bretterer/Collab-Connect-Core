@section('title', ($headerSettings['displayName'] ?? 'Profile') . ' | ' . config('app.name'))
@section('description', $headerSettings['bio'] ?? '')

@push('styles')
<style>
    body {
        background-color: {{ $themeColor }};
        font-family: {{ $font === 'sans' ? 'ui-sans-serif, system-ui, sans-serif' : ($font === 'serif' ? 'ui-serif, Georgia, serif' : ($font === 'mono' ? 'ui-monospace, monospace' : $font)) }};
    }
</style>
@endpush

<div
    class="min-h-screen"
    style="background-color: {{ $themeColor }}"
    x-data="{
        themeColor: '{{ $themeColor }}',
        isLightBackground: false,
        init() {
            this.calculateContrast();
        },
        calculateContrast() {
            // Parse hex color
            let hex = this.themeColor.replace('#', '');
            if (hex.length === 3) {
                hex = hex.split('').map(c => c + c).join('');
            }
            const r = parseInt(hex.substr(0, 2), 16) / 255;
            const g = parseInt(hex.substr(2, 2), 16) / 255;
            const b = parseInt(hex.substr(4, 2), 16) / 255;

            // Calculate relative luminance (WCAG 2.0)
            const luminance = 0.2126 * this.sRGBtoLinear(r) + 0.7152 * this.sRGBtoLinear(g) + 0.0722 * this.sRGBtoLinear(b);

            // If luminance > 0.5, background is light, use dark text
            this.isLightBackground = luminance > 0.5;
        },
        sRGBtoLinear(c) {
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        },
        get textColor() {
            return this.isLightBackground ? 'text-gray-900' : 'text-white';
        },
        get textColorMuted() {
            return this.isLightBackground ? 'text-gray-700' : 'text-white/80';
        },
        get textColorSubtle() {
            return this.isLightBackground ? 'text-gray-600' : 'text-white/90';
        },
        get textColorFaint() {
            return this.isLightBackground ? 'text-gray-500' : 'text-white/60';
        },
        get iconBgColor() {
            return this.isLightBackground ? 'bg-black/10' : 'bg-white/20';
        },
        get iconBgHoverColor() {
            return this.isLightBackground ? 'bg-black/20' : 'bg-white/30';
        },
        get linkBgColor() {
            return this.isLightBackground ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)';
        },
        get borderColor() {
            return this.isLightBackground ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.3)';
        },
        get profileBorderClass() {
            return this.isLightBackground ? 'border-gray-900' : 'border-white';
        }
    }"
>
    {{-- Draft Mode Banner --}}
    @if($isOwner && $isDraft)
        <div class="bg-amber-500 text-amber-950">
            <div class="max-w-lg mx-auto px-4 py-3">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="text-sm font-medium">
                            Draft Preview — This page is only visible to you
                        </span>
                    </div>
                    <a
                        href="{{ route('link-in-bio.index') }}"
                        class="text-sm font-medium underline hover:no-underline whitespace-nowrap"
                    >
                        Edit Page
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-lg mx-auto px-4 py-8">
        {{-- Share Button --}}
        @if($headerSettings['showShareButton'] ?? true)
            <div class="flex justify-end mb-4">
                <button
                    x-on:click="
                        if (navigator.share) {
                            navigator.share({
                                title: '{{ $headerSettings['displayName'] ?? '' }}',
                                url: window.location.href
                            });
                        } else {
                            navigator.clipboard.writeText(window.location.href);
                            alert('Link copied to clipboard!');
                        }
                    "
                    :class="[iconBgColor, 'w-10 h-10 rounded-full flex items-center justify-center transition-colors hover:opacity-80']"
                    aria-label="Share this page"
                >
                    <svg class="w-5 h-5" :class="textColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Header Section (using shared Show view) --}}
        @include('livewire.link-in-bio.sections.header.show', [
            'settings' => $headerSettings,
            'profileImageUrl' => $influencer->getProfileImageUrl(),
        ])

        {{-- Links Section (using shared Show view) --}}
        @include('livewire.link-in-bio.sections.links.show', [
            'settings' => $linksSettings,
            'designSettings' => ['containerStyle' => $containerStyle],
            'isPreview' => false,
        ])

        {{-- Work With Me Section --}}
        @include('livewire.link-in-bio.sections.work-with-me.show', [
            'settings' => $workWithMeSettings,
            'designSettings' => ['containerStyle' => $containerStyle],
            'profileUrl' => route('influencer.profile', ['username' => $influencer->username ?? $influencer->user_id]),
        ])

        {{-- Footer / Branding --}}
        @include('livewire.link-in-bio.sections.footer.show')
    </div>
</div>
