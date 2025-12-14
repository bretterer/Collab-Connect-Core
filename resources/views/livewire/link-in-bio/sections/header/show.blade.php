@php
    // Support both Livewire component properties and @include with settings array
    $enabled = $enabled ?? ($settings['enabled'] ?? true);
    $profilePictureShape = $profilePictureShape ?? ($settings['profilePictureShape'] ?? 'round');
    $profilePictureSize = $profilePictureSize ?? ($settings['profilePictureSize'] ?? 100);
    $profilePictureBorder = $profilePictureBorder ?? ($settings['profilePictureBorder'] ?? true);
    $displayName = $displayName ?? ($settings['displayName'] ?? '');
    $displayNameSize = $displayNameSize ?? ($settings['displayNameSize'] ?? 'medium');
    $location = $location ?? ($settings['location'] ?? '');
    $bio = $bio ?? ($settings['bio'] ?? '');
    $headerFormat = $headerFormat ?? ($settings['headerFormat'] ?? 'vertical');
    $showShareButton = $showShareButton ?? ($settings['showShareButton'] ?? true);
    $influencer = $influencer ?? null;
    $profileImageUrl = $influencer?->getProfileImageUrl() ?? ($profileImageUrl ?? null);
@endphp

@if($enabled)
    @if($headerFormat === 'card')
        {{-- Card Format: Horizontal layout with card background --}}
        <div class="mb-8 p-5 rounded-2xl" :style="'background-color: ' + linkBgColor">
            <div class="flex items-center gap-4">
                {{-- Profile Picture --}}
                <div
                    class="overflow-hidden shrink-0 {{ $profilePictureShape === 'round' ? 'rounded-full' : 'rounded-xl' }}"
                    :class="{ 'border-4': {{ $profilePictureBorder ? 'true' : 'false' }}, [profileBorderClass]: {{ $profilePictureBorder ? 'true' : 'false' }} }"
                    style="width: 80px; height: 80px;"
                >
                    @if($profileImageUrl)
                        <img
                            src="{{ $profileImageUrl }}"
                            alt="{{ $displayName }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center" :class="iconBgColor">
                            <svg class="w-1/2 h-1/2 opacity-70" :class="textColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    {{-- Display Name --}}
                    <h1 class="font-bold truncate {{ $displayNameSize === 'large' ? 'text-2xl' : ($displayNameSize === 'small' ? 'text-base' : 'text-xl') }}" :class="textColor">
                        {{ $displayName ?: 'Your Name' }}
                    </h1>

                    {{-- Location --}}
                    @if($location)
                        <div class="flex items-center gap-1 mt-1" :class="textColorMuted">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm truncate">{{ $location }}</span>
                        </div>
                    @endif

                    {{-- Bio --}}
                    @if($bio)
                        <p class="mt-2 text-sm line-clamp-2" :class="textColorSubtle">{{ $bio }}</p>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Vertical Format: Centered stacked layout (default) --}}
        <div class="text-center mb-8" style="background-color: transparent;">
            {{-- Profile Picture --}}
            <div class="flex justify-center mb-4">
                <div
                    class="overflow-hidden {{ $profilePictureShape === 'round' ? 'rounded-full' : 'rounded-xl' }}"
                    :class="{ 'border-4': {{ $profilePictureBorder ? 'true' : 'false' }}, [profileBorderClass]: {{ $profilePictureBorder ? 'true' : 'false' }} }"
                    style="width: {{ $profilePictureSize }}px; height: {{ $profilePictureSize }}px;"
                >
                    @if($profileImageUrl)
                        <img
                            src="{{ $profileImageUrl }}"
                            alt="{{ $displayName }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center" :class="iconBgColor">
                            <svg class="w-1/2 h-1/2 opacity-70" :class="textColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Display Name --}}
            <h1 class="font-bold {{ $displayNameSize === 'large' ? 'text-3xl' : ($displayNameSize === 'small' ? 'text-lg' : 'text-2xl') }}" :class="textColor">
                {{ $displayName ?: 'Your Name' }}
            </h1>

            {{-- Location --}}
            @if($location)
                <div class="flex items-center justify-center gap-1 mt-2" :class="textColorMuted">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $location }}</span>
                </div>
            @endif

            {{-- Bio --}}
            @if($bio)
                <p class="mt-3 max-w-sm mx-auto" :class="textColorSubtle">{{ $bio }}</p>
            @endif
        </div>
    @endif
@endif
