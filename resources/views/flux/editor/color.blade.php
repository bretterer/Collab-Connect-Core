<div x-data="{ showPicker: false, currentColor: '#000000' }">
    <flux:editor.button
        icon="paint-brush"
        tooltip="Text Color"
        x-on:click="showPicker = !showPicker"
    >
        <span class="text-xs">A</span>
    </flux:editor.button>

    <div
        x-show="showPicker"
        x-on:click.outside="showPicker = false"
        class="absolute mt-2 p-3 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
        style="display: none;"
    >
        <div class="space-y-3">
            <!-- Color Picker -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Choose Color</label>
                <input
                    type="color"
                    x-model="currentColor"
                    class="w-full h-10 rounded cursor-pointer"
                    x-on:input="$el.closest('[data-flux-editor]').editor.chain().focus().setColor($event.target.value).run()"
                />
            </div>

            <!-- Quick Colors -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Colors</label>
                <div class="grid grid-cols-5 gap-2">
                    <button
                        type="button"
                        x-on:click="$el.closest('[data-flux-editor]').editor.chain().focus().unsetColor().run(); currentColor = '#000000'; showPicker = false"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-zinc-900 hover:scale-110 transition-transform"
                        title="Reset"
                    >
                        <svg class="w-4 h-4 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#000000'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#000000').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #000000"
                        title="Black"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#ef4444'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#ef4444').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #ef4444"
                        title="Red"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#f97316'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#f97316').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #f97316"
                        title="Orange"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#eab308'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#eab308').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #eab308"
                        title="Yellow"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#22c55e'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#22c55e').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #22c55e"
                        title="Green"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#3b82f6'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#3b82f6').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #3b82f6"
                        title="Blue"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#6366f1'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#6366f1').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #6366f1"
                        title="Indigo"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#a855f7'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#a855f7').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #a855f7"
                        title="Purple"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#ec4899'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#ec4899').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #ec4899"
                        title="Pink"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#ffffff'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#ffffff').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #ffffff"
                        title="White"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#d1d5db'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#d1d5db').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #d1d5db"
                        title="Light Gray"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#6b7280'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#6b7280').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #6b7280"
                        title="Gray"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#374151'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#374151').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #374151"
                        title="Dark Gray"
                    ></button>

                    <button
                        type="button"
                        x-on:click="currentColor = '#14b8a6'; $el.closest('[data-flux-editor]').editor.chain().focus().setColor('#14b8a6').run()"
                        class="w-8 h-8 rounded border-2 border-gray-300 dark:border-gray-600 hover:scale-110 transition-transform"
                        style="background-color: #14b8a6"
                        title="Teal"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</div>
