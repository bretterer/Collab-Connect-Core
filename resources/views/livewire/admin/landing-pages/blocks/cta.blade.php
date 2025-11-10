<div class="space-y-6" x-data="{ activeTab: 'content' }">
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button
                type="button"
                @click="activeTab = 'content'"
                :class="activeTab === 'content' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
                Content
            </button>
            <button
                type="button"
                @click="activeTab = 'button'"
                :class="activeTab === 'button' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
                Button
            </button>
            <button
                type="button"
                @click="activeTab = 'layout'"
                :class="activeTab === 'layout' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
                Layout
            </button>
            <button
                type="button"
                @click="activeTab = 'background'"
                :class="activeTab === 'background' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
                Background
            </button>
        </nav>
    </div>

    <!-- Content Tab -->
    <div x-show="activeTab === 'content'" class="space-y-4">
        <flux:field>
            <flux:label>Headline</flux:label>
            <flux:input wire:model="blockData.headline" placeholder="Ready to get started?" />
        </flux:field>

        <flux:field>
            <flux:label>Subheadline</flux:label>
            <flux:textarea wire:model="blockData.subheadline" placeholder="Join thousands of satisfied customers" rows="3" />
        </flux:field>

        <flux:field>
            <flux:label>Text</flux:label>
            <flux:textarea wire:model="blockData.text" placeholder="Additional text content (optional)" rows="4" />
        </flux:field>

        <!-- Width Slider -->
        <flux:field>
            <flux:label>Width (Columns)</flux:label>
            <div class="flex items-center gap-2">
                <div class="flex gap-1">
                    @for($i = 1; $i <= 12; $i++)
                        <button
                            type="button"
                            wire:click="$set('blockData.width', {{ $i }})"
                            class="w-4 h-8 rounded {{ (int)($blockData['width'] ?? 12) >= $i ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-700' }}"
                        ></button>
                    @endfor
                </div>
                <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">{{ $blockData['width'] ?? 12 }} columns</span>
            </div>
        </flux:field>
    </div>

    <!-- Button Tab -->
    <div x-show="activeTab === 'button'" class="space-y-4">
        <flux:field>
            <flux:label>Button Text</flux:label>
            <flux:input wire:model="blockData.button_text" placeholder="Get Started Now" />
        </flux:field>

        <flux:field>
            <flux:label>Button Action</flux:label>
            <flux:select wire:model.live="blockData.button_action">
                <option value="url">Go to URL</option>
                <option value="landing_page">Go to Landing Page</option>
                <option value="two_step_optin">Open Two Step Optin Popup</option>
            </flux:select>
        </flux:field>

        @if(($blockData['button_action'] ?? 'url') === 'url')
            <flux:field>
                <flux:label>Button URL</flux:label>
                <flux:input wire:model="blockData.button_url" placeholder="https://example.com" />
            </flux:field>

            <flux:field>
                <flux:label>Open In New Tab</flux:label>
                <div class="flex items-center gap-2">
                    <flux:switch wire:model.boolean="blockData.button_new_tab" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ ($blockData['button_new_tab'] ?? false) ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </flux:field>
        @endif

        @if(($blockData['button_action'] ?? 'url') === 'landing_page')
            <flux:field>
                <flux:label>Select Landing Page</flux:label>
                <flux:select wire:model="blockData.landing_page_id">
                    <option value="">Choose a landing page...</option>
                    @foreach($publishedLandingPages as $page)
                        <option value="{{ $page->id }}">{{ $page->title }}</option>
                    @endforeach
                </flux:select>
                <flux:description>Only published landing pages are shown</flux:description>
            </flux:field>

            <flux:field>
                <flux:label>Open In New Tab</flux:label>
                <div class="flex items-center gap-2">
                    <flux:switch wire:model.boolean="blockData.button_new_tab" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ ($blockData['button_new_tab'] ?? false) ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </flux:field>
        @endif

        @if(($blockData['button_action'] ?? 'url') === 'two_step_optin')
            <flux:field>
                <flux:description>This will open the two-step optin popup configured for this landing page</flux:description>
            </flux:field>
        @endif

        <flux:field>
            <flux:label>Button Background Color</flux:label>
            <div class="flex items-center gap-2">
                <input type="color" wire:model.live="blockData.button_bg_color" class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600" />
                <flux:input wire:model="blockData.button_bg_color" placeholder="#DF4D42" class="flex-1" />
            </div>
        </flux:field>

        <flux:field>
            <flux:label>Button Text Color</flux:label>
            <div class="flex items-center gap-2">
                <input type="color" wire:model.live="blockData.button_text_color" class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600" />
                <flux:input wire:model="blockData.button_text_color" placeholder="#FFFFFF" class="flex-1" />
            </div>
        </flux:field>

        <flux:field>
            <flux:label>Button Width</flux:label>
            <div class="space-y-2">
                <label class="flex items-center gap-2">
                    <input type="radio" wire:model="blockData.button_width" value="full" class="text-blue-600" />
                    <span class="text-sm">Full</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" wire:model="blockData.button_width" value="auto" class="text-blue-600" />
                    <span class="text-sm">Auto</span>
                </label>
            </div>
        </flux:field>

        <flux:field>
            <flux:label>Button Style</flux:label>
            <div class="space-y-2">
                <label class="flex items-center gap-2">
                    <input type="radio" wire:model="blockData.button_style" value="solid" class="text-blue-600" />
                    <span class="text-sm">Solid</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" wire:model="blockData.button_style" value="outline" class="text-blue-600" />
                    <span class="text-sm">Outline</span>
                </label>
            </div>
        </flux:field>

        <flux:field>
            <flux:label>Button Size</flux:label>
            <div class="space-y-2">
                <label class="flex items-center gap-2">
                    <input type="radio" wire:model="blockData.button_size" value="small" class="text-blue-600" />
                    <span class="text-sm">Small</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" wire:model="blockData.button_size" value="medium" class="text-blue-600" />
                    <span class="text-sm">Medium</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" wire:model="blockData.button_size" value="large" class="text-blue-600" />
                    <span class="text-sm">Large</span>
                </label>
            </div>
        </flux:field>

        <flux:field>
            <flux:label>Border Radius (px)</flux:label>
            <div class="flex items-center gap-4">
                <input type="range" min="0" max="50" wire:model.live="blockData.button_border_radius" class="flex-1" />
                <flux:input type="number" wire:model="blockData.button_border_radius" placeholder="0" class="w-20" />
            </div>
        </flux:field>
    </div>

    <!-- Layout Tab -->
    <div x-show="activeTab === 'layout'" class="space-y-6">
        <!-- Desktop Layout -->
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>Desktop Layout</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Hide Block</flux:label>
                            <flux:switch wire:model.boolean="blockData.desktop_hide" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Text Alignment</flux:label>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    wire:click="$set('blockData.desktop_text_align', 'left')"
                                    class="flex-1 px-4 py-2 border rounded {{ ($blockData['desktop_text_align'] ?? 'center') === 'left' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600' }}"
                                >
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"></path>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('blockData.desktop_text_align', 'center')"
                                    class="flex-1 px-4 py-2 border rounded {{ ($blockData['desktop_text_align'] ?? 'center') === 'center' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600' }}"
                                >
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"></path>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('blockData.desktop_text_align', 'right')"
                                    class="flex-1 px-4 py-2 border rounded {{ ($blockData['desktop_text_align'] ?? 'center') === 'right' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600' }}"
                                >
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"></path>
                                    </svg>
                                </button>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>Inside Spacing (Padding)</flux:label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <flux:label class="text-xs">Top</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_padding_top" placeholder="0" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Left</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_padding_left" placeholder="0" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Right</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_padding_right" placeholder="0" />
                                </div>
                                <div class="col-start-2">
                                    <flux:label class="text-xs">Bottom</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_padding_bottom" placeholder="0" />
                                </div>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>Outside Spacing (Margin)</flux:label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <flux:label class="text-xs">Top</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_margin_top" placeholder="0" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Left</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_margin_left" placeholder="0" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Right</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_margin_right" placeholder="0" />
                                </div>
                                <div class="col-start-2">
                                    <flux:label class="text-xs">Bottom</flux:label>
                                    <flux:input type="number" wire:model="blockData.desktop_margin_bottom" placeholder="0" />
                                </div>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>Make Flush</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:switch wire:model.boolean="blockData.desktop_make_flush" />
                                <span class="text-sm text-gray-600 dark:text-gray-400">Place this block on its own row</span>
                            </div>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>

        <!-- Mobile Layout -->
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>Mobile Layout</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Hide Block</flux:label>
                            <flux:switch wire:model.boolean="blockData.mobile_hide" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Mobile Text Alignment</flux:label>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    wire:click="$set('blockData.mobile_text_align', 'left')"
                                    class="flex-1 px-4 py-2 border rounded {{ ($blockData['mobile_text_align'] ?? 'center') === 'left' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600' }}"
                                >
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"></path>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('blockData.mobile_text_align', 'center')"
                                    class="flex-1 px-4 py-2 border rounded {{ ($blockData['mobile_text_align'] ?? 'center') === 'center' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600' }}"
                                >
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"></path>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    wire:click="$set('blockData.mobile_text_align', 'right')"
                                    class="flex-1 px-4 py-2 border rounded {{ ($blockData['mobile_text_align'] ?? 'center') === 'right' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600' }}"
                                >
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"></path>
                                    </svg>
                                </button>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>Inside Spacing (Padding)</flux:label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <flux:label class="text-xs">Top</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_padding_top" placeholder="0" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Left</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_padding_left" placeholder="0" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Right</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_padding_right" placeholder="0" />
                                </div>
                                <div class="col-start-2">
                                    <flux:label class="text-xs">Bottom</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_padding_bottom" placeholder="0" />
                                </div>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>Outside Spacing (Margin)</flux:label>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <flux:label class="text-xs">Top</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_margin_top" placeholder="-12" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Left</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_margin_left" placeholder="0" />
                                </div>
                                <div>
                                    <flux:label class="text-xs">Right</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_margin_right" placeholder="0" />
                                </div>
                                <div class="col-start-2">
                                    <flux:label class="text-xs">Bottom</flux:label>
                                    <flux:input type="number" wire:model="blockData.mobile_margin_bottom" placeholder="0" />
                                </div>
                            </div>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    <!-- Background Tab -->
    <div x-show="activeTab === 'background'" class="space-y-4">
        <flux:field>
            <flux:label>Block Background Color</flux:label>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    @if(($blockData['background_color'] ?? '#f9fafb') !== 'transparent')
                        <input
                            type="color"
                            wire:model.live="blockData.background_color"
                            class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600"
                        />
                    @else
                        <div class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700"></div>
                    @endif
                    <flux:input
                        wire:model="blockData.background_color"
                        placeholder="#f9fafb or transparent"
                        class="flex-1"
                    />
                </div>
                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        @change="$wire.set('blockData.background_color', $el.checked ? 'transparent' : '#f9fafb')"
                        {{ ($blockData['background_color'] ?? '#f9fafb') === 'transparent' ? 'checked' : '' }}
                        class="rounded text-blue-600"
                    />
                    <span class="text-sm text-gray-600 dark:text-gray-400">Transparent background</span>
                </label>
            </div>
        </flux:field>

        <flux:field>
            <flux:label>Border Type</flux:label>
            <flux:select wire:model="blockData.border_type">
                <option value="none">None</option>
                <option value="solid">Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
            </flux:select>
        </flux:field>

        @if(($blockData['border_type'] ?? 'none') !== 'none')
            <flux:field>
                <flux:label>Border Width (px)</flux:label>
                <div class="flex items-center gap-4">
                    <input type="range" min="0" max="20" wire:model.live="blockData.border_width" class="flex-1" />
                    <flux:input type="number" wire:model="blockData.border_width" placeholder="5" class="w-20" />
                </div>
            </flux:field>

            <flux:field>
                <flux:label>Border Color</flux:label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="blockData.border_color" class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600" />
                    <flux:input wire:model="blockData.border_color" placeholder="#CAAE51" class="flex-1" />
                </div>
            </flux:field>

            <flux:field>
                <flux:label>Border Radius (px)</flux:label>
                <div class="flex items-center gap-4">
                    <input type="range" min="0" max="50" wire:model.live="blockData.border_radius" class="flex-1" />
                    <flux:input type="number" wire:model="blockData.border_radius" placeholder="4" class="w-20" />
                </div>
            </flux:field>
        @endif

        <flux:field>
            <flux:label>Box Shadow</flux:label>
            <flux:select wire:model="blockData.box_shadow">
                <option value="none">None</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
            </flux:select>
        </flux:field>
    </div>
</div>
