<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        {{-- Video Upload --}}
        @livewire('landing-pages.video-uploader', [
            'videoUrl' => $data['video_url'] ?? '',
            'videoTitle' => $data['video_title'] ?? '',
            'posterUrl' => $data['poster_url'] ?? '',
            'propertyPrefix' => $propertyPrefix,
        ], key('video-uploader-' . ($data['id'] ?? uniqid())))

        {{-- Thumbnail Upload --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Custom Thumbnail (Optional)</h3>
            <flux:description class="mb-4">Upload a custom thumbnail image that shows before the video plays. A play button will be centered over it.</flux:description>

            @livewire('landing-pages.thumbnail-uploader', [
                'thumbnailUrl' => $data['thumbnail_url'] ?? '',
                'propertyPrefix' => $propertyPrefix,
            ], key('thumbnail-uploader-' . ($data['id'] ?? uniqid())))
        </div>

        {{-- Player Settings --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Player Settings</h3>
            <div class="space-y-4">
                <flux:field>
                    <flux:label>
                        <flux:checkbox wire:model="{{ $propertyPrefix }}.autoplay" />
                        Autoplay
                    </flux:label>
                    <flux:description>Video starts playing automatically (will be muted on most browsers)</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>
                        <flux:checkbox wire:model="{{ $propertyPrefix }}.loop" />
                        Loop
                    </flux:label>
                    <flux:description>Video restarts automatically when it ends</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>
                        <flux:checkbox wire:model="{{ $propertyPrefix }}.muted" />
                        Muted
                    </flux:label>
                    <flux:description>Start with sound muted (required for autoplay on most browsers)</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>
                        <flux:checkbox wire:model="{{ $propertyPrefix }}.controls" />
                        Show Controls
                    </flux:label>
                    <flux:description>Display player controls (customize which controls below)</flux:description>
                </flux:field>

                {{-- Control Customization (only when controls are enabled) --}}
                @if($data['controls'] ?? true)
                    <div class="ml-6 space-y-3 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                        <flux:heading size="sm">Which Controls to Show</flux:heading>

                        <flux:field>
                            <flux:label>
                                <flux:checkbox wire:model="{{ $propertyPrefix }}.show_play_button" />
                                Play/Pause Button
                            </flux:label>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                <flux:checkbox wire:model="{{ $propertyPrefix }}.show_progress" />
                                Progress Bar
                            </flux:label>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                <flux:checkbox wire:model="{{ $propertyPrefix }}.show_current_time" />
                                Current Time
                            </flux:label>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                <flux:checkbox wire:model="{{ $propertyPrefix }}.show_duration" />
                                Duration
                            </flux:label>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                <flux:checkbox wire:model="{{ $propertyPrefix }}.show_mute" />
                                Mute Button
                            </flux:label>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                <flux:checkbox wire:model="{{ $propertyPrefix }}.show_volume" />
                                Volume Slider
                            </flux:label>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                <flux:checkbox wire:model="{{ $propertyPrefix }}.show_fullscreen" />
                                Fullscreen Button
                            </flux:label>
                        </flux:field>
                    </div>
                @endif
            </div>
        </div>

        {{-- Layout Settings --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Layout</h3>
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Video Width</flux:label>
                    <flux:description>How wide the video player should be</flux:description>
                    <div class="flex gap-4">
                        <flux:radio.group wire:model="{{ $propertyPrefix }}.width">
                            <flux:radio value="full" label="Full Width" />
                            <flux:radio value="large" label="Large (80%)" />
                            <flux:radio value="medium" label="Medium (60%)" />
                            <flux:radio value="small" label="Small (40%)" />
                        </flux:radio.group>
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Video Alignment</flux:label>
                    <flux:description>How to align the video player</flux:description>
                    <div class="flex gap-4">
                        <flux:radio.group wire:model="{{ $propertyPrefix }}.alignment">
                            <flux:radio value="left" label="Left" />
                            <flux:radio value="center" label="Center" />
                            <flux:radio value="right" label="Right" />
                        </flux:radio.group>
                    </div>
                </flux:field>
            </div>
        </div>
    </div>

    @script
    <script>
        // Listen for video upload events from the uploader component
        Livewire.on('videoUploaded', (event) => {
            const data = event[0];
            @this.set('{{ $propertyPrefix }}.video_url', data.videoUrl);
        });

        Livewire.on('videoTitleUpdated', (event) => {
            const data = event[0];
            @this.set('{{ $propertyPrefix }}.video_title', data.videoTitle);
        });

        Livewire.on('videoDeleted', () => {
            @this.set('{{ $propertyPrefix }}.video_url', '');
            @this.set('{{ $propertyPrefix }}.video_title', '');
        });

        // Listen for thumbnail upload events
        Livewire.on('thumbnailUploaded', (event) => {
            const data = event[0];
            @this.set('{{ $propertyPrefix }}.thumbnail_url', data.thumbnailUrl);
        });

        Livewire.on('thumbnailDeleted', () => {
            @this.set('{{ $propertyPrefix }}.thumbnail_url', '');
        });
    </script>
    @endscript
</x-landing-page-block.editor>
