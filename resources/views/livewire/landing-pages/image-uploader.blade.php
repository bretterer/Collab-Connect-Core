<div class="space-y-6">
    {{-- Upload Section --}}
    <div>
        <flux:field>
            <flux:label>Upload Image</flux:label>
            <flux:description>Upload an image (max 10MB). Supported formats: JPG, PNG, GIF, WebP</flux:description>

            <div class="mt-2">
                @if($imageUrl)
                    <div class="space-y-4">
                        {{-- Image Preview --}}
                        <div class="relative rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $imageAlt ?: 'Uploaded image' }}"
                                class="w-full h-auto"
                                style="
                                    filter:
                                        brightness({{ 1 + ($brightness / 100) }})
                                        contrast({{ 1 + ($contrast / 100) }})
                                        blur({{ $blur }}px);
                                "
                            />
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2">
                            <flux:button size="sm" variant="danger" wire:click="deleteImage" icon="trash">
                                Delete Image
                            </flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="resetImage" icon="arrow-path">
                                Upload New
                            </flux:button>
                        </div>
                    </div>
                @else
                    {{-- File Upload Input --}}
                    <div class="relative">
                        <input
                            type="file"
                            wire:model="photo"
                            accept="image/*"
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
                                    Uploading...
                                </div>
                            </div>
                        @endif
                    </div>

                    @error('photo')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <div wire:loading wire:target="photo" class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        Processing image...
                    </div>
                @endif
            </div>
        </flux:field>
    </div>

    {{-- Image Adjustments (only show if image is uploaded) --}}
    @if($imageUrl)
        <div class="space-y-4 border-t border-zinc-200 dark:border-zinc-700 pt-6">
            <flux:heading size="sm">Image Adjustments</flux:heading>

            {{-- Alt Text --}}
            <flux:field>
                <flux:label>Alt Text</flux:label>
                <flux:description>Describe the image for accessibility</flux:description>
                <flux:input
                    wire:model.blur="imageAlt"
                    placeholder="e.g., Product photo showing..."
                />
            </flux:field>

            {{-- Brightness --}}
            <flux:field>
                <flux:label>Brightness: {{ $brightness }}</flux:label>
                <input
                    type="range"
                    wire:model.live="brightness"
                    min="-100"
                    max="100"
                    step="1"
                    class="w-full h-2 bg-zinc-200 dark:bg-zinc-700 rounded-lg appearance-none cursor-pointer"
                />
            </flux:field>

            {{-- Contrast --}}
            <flux:field>
                <flux:label>Contrast: {{ $contrast }}</flux:label>
                <input
                    type="range"
                    wire:model.live="contrast"
                    min="-100"
                    max="100"
                    step="1"
                    class="w-full h-2 bg-zinc-200 dark:bg-zinc-700 rounded-lg appearance-none cursor-pointer"
                />
            </flux:field>

            {{-- Saturation --}}
            <flux:field>
                <flux:label>Saturation: {{ $saturation }}</flux:label>
                <input
                    type="range"
                    wire:model.live="saturation"
                    min="-100"
                    max="100"
                    step="1"
                    class="w-full h-2 bg-zinc-200 dark:bg-zinc-700 rounded-lg appearance-none cursor-pointer"
                />
            </flux:field>

            {{-- Blur --}}
            <flux:field>
                <flux:label>Blur: {{ $blur }}</flux:label>
                <input
                    type="range"
                    wire:model.live="blur"
                    min="0"
                    max="100"
                    step="1"
                    class="w-full h-2 bg-zinc-200 dark:bg-zinc-700 rounded-lg appearance-none cursor-pointer"
                />
            </flux:field>

            {{-- Apply Adjustments Button --}}
            <flux:button
                variant="primary"
                wire:click="updateAdjustments"
                class="w-full"
            >
                Apply Adjustments
            </flux:button>
        </div>
    @endif

    @if(session()->has('image-upload-success'))
        <flux:callout variant="success">
            {{ session('image-upload-success') }}
        </flux:callout>
    @endif
</div>
