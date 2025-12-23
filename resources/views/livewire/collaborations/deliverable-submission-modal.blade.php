<div>
    <flux:modal wire:model="showModal" class="max-w-xl">
        <div class="p-6">
            <flux:heading size="lg" class="mb-2">Submit Deliverable</flux:heading>
            @if($deliverable)
                <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
                    Submit your {{ $deliverable->deliverable_type->label() }} for review
                </flux:text>
            @endif

            <form wire:submit="submit" class="space-y-6">
                <!-- Post URL -->
                <div>
                    <flux:input
                        wire:model="postUrl"
                        label="Post URL"
                        type="url"
                        placeholder="https://instagram.com/p/..."
                        required
                        description="Link to the published content"
                    />
                    @error('postUrl')
                        <flux:text size="sm" class="text-red-600 dark:text-red-400 mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <flux:textarea
                        wire:model="notes"
                        label="Notes (Optional)"
                        placeholder="Add any notes about your submission..."
                        rows="3"
                    />
                    @error('notes')
                        <flux:text size="sm" class="text-red-600 dark:text-red-400 mt-1">{{ $message }}</flux:text>
                    @enderror
                </div>

                <!-- Screenshots Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Screenshots (Optional)
                    </label>
                    <flux:text size="sm" class="text-gray-500 dark:text-gray-400 mb-3">
                        Upload screenshots of analytics or engagement metrics. Max 10MB per image.
                    </flux:text>

                    <div
                        x-data="{ isDragging: false }"
                        @dragenter.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @dragover.prevent
                        @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                        class="relative"
                    >
                        <input
                            type="file"
                            wire:model="screenshots"
                            multiple
                            accept="image/*"
                            x-ref="fileInput"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        />
                        <div
                            :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                            class="border-2 border-dashed rounded-lg p-6 text-center transition-colors"
                        >
                            <flux:icon name="cloud-arrow-up" class="w-10 h-10 text-gray-400 mx-auto mb-3" />
                            <flux:text class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium text-blue-600 dark:text-blue-400">Click to upload</span>
                                or drag and drop
                            </flux:text>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400 mt-1">
                                PNG, JPG, GIF up to 10MB each
                            </flux:text>
                        </div>
                    </div>

                    @error('screenshots.*')
                        <flux:text size="sm" class="text-red-600 dark:text-red-400 mt-2">{{ $message }}</flux:text>
                    @enderror

                    <!-- Upload Progress -->
                    <div wire:loading wire:target="screenshots" class="mt-3">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </div>
                    </div>

                    <!-- Preview Uploaded Files -->
                    @if(count($screenshots) > 0)
                        <div class="mt-4 grid grid-cols-3 gap-3">
                            @foreach($screenshots as $index => $screenshot)
                                <div class="relative group">
                                    <img
                                        src="{{ $screenshot->temporaryUrl() }}"
                                        alt="Screenshot preview"
                                        class="w-full h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                                    />
                                    <button
                                        type="button"
                                        wire:click="removeScreenshot({{ $index }})"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                    >
                                        <flux:icon name="x-mark" class="w-4 h-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <flux:button type="button" wire:click="closeModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="paper-airplane">
                        Submit for Review
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
