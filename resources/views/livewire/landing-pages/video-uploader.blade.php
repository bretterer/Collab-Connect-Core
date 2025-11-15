<div class="space-y-6">
    {{-- Upload Section --}}
    <div>
        <flux:field>
            <flux:label>Upload Video</flux:label>
            <flux:description>Upload a video file (max 100MB). Supported formats: MP4, WebM, MOV</flux:description>

            <div class="mt-2">
                @if($videoUrl)
                    <div class="space-y-4">
                        {{-- Video Preview --}}
                        <div class="relative rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                            <video
                                src="{{ $videoUrl }}"
                                controls
                                class="w-full h-auto"
                                style="max-height: 400px;"
                            >
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        {{-- Video Info --}}
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            <p>Video URL: <span class="font-mono text-xs">{{ $videoUrl }}</span></p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2">
                            <flux:button size="sm" variant="danger" wire:click="deleteVideo" icon="trash">
                                Delete Video
                            </flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="resetVideo" icon="arrow-path">
                                Upload New
                            </flux:button>
                        </div>
                    </div>
                @else
                    {{-- File Upload Input --}}
                    <div class="relative">
                        <input
                            type="file"
                            wire:model="video"
                            accept="video/mp4,video/webm,video/quicktime"
                            class="block w-full text-sm text-zinc-900 dark:text-zinc-100
                                   border border-zinc-300 dark:border-zinc-600 rounded-lg cursor-pointer
                                   bg-zinc-50 dark:bg-zinc-800 focus:outline-none
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-l-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-zinc-100 dark:file:bg-zinc-700
                                   file:text-zinc-700 dark:file:text-zinc-300
                                   hover:file:bg-zinc-200 dark:hover:file:bg-zinc-600"
                        />

                        @if($uploading)
                            <div class="mt-2">
                                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <flux:icon.arrow-path class="animate-spin" />
                                    Uploading video...
                                </div>
                            </div>
                        @endif
                    </div>

                    @error('video')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <div wire:loading wire:target="video" class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        Processing video upload...
                    </div>
                @endif
            </div>
        </flux:field>
    </div>

    {{-- Video Title (shown when video is uploaded) --}}
    @if($videoUrl)
        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
            <flux:field>
                <flux:label>Video Title</flux:label>
                <flux:description>Describe the video for accessibility (screen readers)</flux:description>
                <flux:input
                    wire:model.blur="videoTitle"
                    placeholder="e.g., Product demonstration video"
                />
            </flux:field>
        </div>
    @endif

    @if(session()->has('video-upload-success'))
        <flux:callout variant="success">
            {{ session('video-upload-success') }}
        </flux:callout>
    @endif
</div>
