<div>
    @if($thumbnailUrl)
        <div class="space-y-3">
            {{-- Thumbnail Preview --}}
            <div class="relative rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700 max-h-48">
                <img
                    src="{{ $thumbnailUrl }}"
                    alt="Video thumbnail"
                    class="w-full h-auto object-cover"
                />
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <flux:button size="sm" variant="danger" wire:click="deleteThumbnail" icon="trash">
                    Delete Thumbnail
                </flux:button>
            </div>
        </div>
    @else
        {{-- File Upload Input --}}
        <div>
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

            <div wire:loading wire:target="photo" class="mt-2 flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                <flux:icon.arrow-path class="animate-spin" />
                Uploading thumbnail...
            </div>

            @error('photo')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </div>
    @endif
</div>
