<flux:dropdown position="bottom start">
    <flux:editor.button icon="paint-brush" tooltip="Text Color">
        <span class="text-xs">A</span>
    </flux:editor.button>

    <flux:menu class="w-40">
        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().unsetColor().run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300 bg-white"></div>
                <span>Default</span>
            </div>
        </flux:menu.item>

        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().setColor('#ef4444').run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: #ef4444"></div>
                <span>Red</span>
            </div>
        </flux:menu.item>

        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().setColor('#f97316').run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: #f97316"></div>
                <span>Orange</span>
            </div>
        </flux:menu.item>

        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().setColor('#eab308').run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: #eab308"></div>
                <span>Yellow</span>
            </div>
        </flux:menu.item>

        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().setColor('#22c55e').run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: #22c55e"></div>
                <span>Green</span>
            </div>
        </flux:menu.item>

        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().setColor('#3b82f6').run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: #3b82f6"></div>
                <span>Blue</span>
            </div>
        </flux:menu.item>

        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().setColor('#a855f7').run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: #a855f7"></div>
                <span>Purple</span>
            </div>
        </flux:menu.item>

        <flux:menu.item x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().setColor('#ec4899').run()">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded border border-gray-300" style="background-color: #ec4899"></div>
                <span>Pink</span>
            </div>
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
