@php
    // Support both Livewire component properties and @include with settings array
    $enabled = $enabled ?? ($settings['enabled'] ?? true);
    $items = $items ?? ($settings['items'] ?? []);
    $size = $size ?? ($settings['size'] ?? 'medium');
    $textAlign = $textAlign ?? ($settings['textAlign'] ?? 'center');
    $shadow = $shadow ?? ($settings['shadow'] ?? true);
    $outline = $outline ?? ($settings['outline'] ?? false);
    $layout = $layout ?? ($settings['layout'] ?? 'classic');
    $containerStyle = $containerStyle ?? ($designSettings['containerStyle'] ?? 'round');
    $isPreview = $isPreview ?? false;
@endphp

@if($enabled)
    {{-- Title and Subtitle --}}
    <div class="mb-6 text-center">
        @if(!empty($settings['title']))
            <h2 class="font-semibold {{ $size === 'large' ? 'text-2xl' : ($size === 'small' ? 'text-lg' : 'text-xl') }}" :class="textColor">
                {{ $settings['title'] }}
            </h2>
        @endif
        @if(!empty($settings['subtitle']))
            <p class="mt-2 {{ $size === 'large' ? 'text-lg' : ($size === 'small' ? 'text-sm' : 'text-base') }}" :class="textColorSubtle">
                {{ $settings['subtitle'] }}
            </p>
        @endif
    </div>

    @if(count($items) > 0)
    <div class="{{ $layout === 'grid' ? 'grid grid-cols-2 gap-3' : 'space-y-3' }} mb-8">
        @foreach($items as $link)
            @if($link['enabled'] && ($isPreview || $link['url']))
                @if($layout === 'grid')
                    {{-- Grid Layout --}}
                    @if($isPreview)
                    <div
                        class="flex flex-col items-center justify-center p-4 text-center transition-all aspect-square {{ $containerStyle === 'round' ? 'rounded-3xl' : ($containerStyle === 'square' ? 'rounded-xl' : 'rounded-none') }}"
                        :style="'background-color: ' + linkBgColor + '; {{ $shadow ? 'box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);' : '' }} ' + ({{ $outline ? 'true' : 'false' }} ? 'border: 1px solid ' + borderColor + ';' : '')"
                    >
                    @else
                    <a
                        href="{{ $link['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        data-track-link
                        data-link-index="{{ $loop->index }}"
                        data-link-title="{{ $link['title'] ?? 'Link' }}"
                        class="flex flex-col items-center justify-center p-4 text-center transition-all aspect-square hover:scale-[1.02] hover:opacity-90 {{ $containerStyle === 'round' ? 'rounded-3xl' : ($containerStyle === 'square' ? 'rounded-xl' : 'rounded-none') }}"
                        :style="'background-color: ' + linkBgColor + '; {{ $shadow ? 'box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);' : '' }} ' + ({{ $outline ? 'true' : 'false' }} ? 'border: 1px solid ' + borderColor + ';' : '')"
                    >
                    @endif
                        @if($link['icon'])
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-2" :class="iconBgColor">
                                @include('livewire.link-in-bio.sections.links.partials.icon', ['icon' => $link['icon'], 'size' => 'w-6 h-6'])
                            </div>
                        @endif
                        <span class="font-medium line-clamp-2 {{ $size === 'large' ? 'text-base' : ($size === 'small' ? 'text-xs' : 'text-sm') }}" :class="textColor">
                            {{ $link['title'] ?: 'Link Title' }}
                        </span>
                    @if($isPreview)
                    </div>
                    @else
                    </a>
                    @endif
                @else
                    {{-- Classic Layout --}}
                    @if($isPreview)
                    <div
                        class="w-full py-4 px-6 text-center transition-all {{ $containerStyle === 'round' ? 'rounded-full' : ($containerStyle === 'square' ? 'rounded-xl' : 'rounded-none') }}"
                        :style="'background-color: ' + linkBgColor + '; {{ $shadow ? 'box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);' : '' }} ' + ({{ $outline ? 'true' : 'false' }} ? 'border: 1px solid ' + borderColor + ';' : '')"
                    >
                    @else
                    <a
                        href="{{ $link['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        data-track-link
                        data-link-index="{{ $loop->index }}"
                        data-link-title="{{ $link['title'] ?? 'Link' }}"
                        class="block w-full py-4 px-6 text-center transition-all hover:scale-[1.02] hover:opacity-90 {{ $containerStyle === 'round' ? 'rounded-full' : ($containerStyle === 'square' ? 'rounded-xl' : 'rounded-none') }}"
                        :style="'background-color: ' + linkBgColor + '; {{ $shadow ? 'box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);' : '' }} ' + ({{ $outline ? 'true' : 'false' }} ? 'border: 1px solid ' + borderColor + ';' : '')"
                    >
                    @endif
                        <div class="flex items-center gap-3 {{ $textAlign === 'center' ? 'justify-center' : ($textAlign === 'left' ? 'justify-start' : 'justify-end') }}">
                            @if($link['icon'])
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="iconBgColor">
                                    @include('livewire.link-in-bio.sections.links.partials.icon', ['icon' => $link['icon'], 'size' => 'w-5 h-5'])
                                </div>
                            @endif
                            <span class="font-medium {{ $size === 'large' ? 'text-lg' : ($size === 'small' ? 'text-sm' : 'text-base') }}" :class="textColor">
                                {{ $link['title'] ?: 'Link Title' }}
                            </span>
                        </div>
                    @if($isPreview)
                    </div>
                    @else
                    </a>
                    @endif
                @endif
            @endif
        @endforeach
    </div>
    @endif
@endif
