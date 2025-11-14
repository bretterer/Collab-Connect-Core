@props(['blocks' => [], 'scale' => 'desktop'])

<div class="bg-white rounded-lg shadow-lg overflow-hidden h-full">
    <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="flex gap-1">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <span class="text-xs text-gray-600 ml-2">Preview</span>
        </div>
        <div class="flex gap-2" x-data="{ scale: @entangle('previewScale') }">
            <button
                @click="scale = 'mobile'"
                :class="scale === 'mobile' ? 'bg-blue-100 text-blue-600' : 'bg-white text-gray-600'"
                class="px-2 py-1 rounded text-xs hover:bg-gray-50 transition-colors"
                title="Mobile view"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </button>
            <button
                @click="scale = 'tablet'"
                :class="scale === 'tablet' ? 'bg-blue-100 text-blue-600' : 'bg-white text-gray-600'"
                class="px-2 py-1 rounded text-xs hover:bg-gray-50 transition-colors"
                title="Tablet view"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </button>
            <button
                @click="scale = 'desktop'"
                :class="scale === 'desktop' ? 'bg-blue-100 text-blue-600' : 'bg-white text-gray-600'"
                class="px-2 py-1 rounded text-xs hover:bg-gray-50 transition-colors"
                title="Desktop view"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="bg-gray-50 overflow-auto h-[calc(100%-44px)]" x-data="{ scale: @entangle('previewScale') }">
        <div class="flex justify-center p-4">
            <div
                class="bg-white shadow-lg transition-all duration-300"
                :class="{
                    'w-[375px]': scale === 'mobile',
                    'w-[768px]': scale === 'tablet',
                    'w-full': scale === 'desktop'
                }"
            >
                @if(count($blocks) > 0)
                    @foreach($blocks as $block)
                        <div class="relative group">
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                <span class="bg-gray-900 text-white text-xs px-2 py-1 rounded">
                                    @php
                                        $blockInstance = \App\LandingPages\BlockRegistry::get($block['type']);
                                        $label = $blockInstance ? $blockInstance::label() : $block['type'];
                                    @endphp
                                    {{ $label }}
                                </span>
                            </div>
                            @include('landing-pages.blocks.' . $block['type'] . '.render', ['data' => $block['data']])
                        </div>
                    @endforeach
                @else
                    <div class="min-h-[400px] flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm">Add blocks to see preview</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
