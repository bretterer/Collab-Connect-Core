<div class="relative min-h-[400px] rounded-xl overflow-hidden">
    {{-- Background Image - Light Mode --}}
    <img
        src="{{ Vite::asset('resources/images/coming-soon/light.png') }}"
        alt=""
        class="absolute inset-0 w-full h-full object-cover dark:hidden"
    />

    {{-- Background Image - Dark Mode --}}
    <img
        src="{{ Vite::asset('resources/images/coming-soon/dark.png') }}"
        alt=""
        class="absolute inset-0 w-full h-full object-cover hidden dark:block"
    />

    {{-- Overlay for better text readability --}}
    <div class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm"></div>

    {{-- Content --}}
    <div class="relative z-10 flex items-center justify-center min-h-[400px] p-8">
        <div class="max-w-xl w-full">
            <flux:card class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md border border-gray-200 dark:border-gray-700 shadow-xl">
                <div class="text-center">
                    {{-- Icon --}}
                    @if($icon)
                        <div class="mb-6 flex justify-center">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                                <flux:icon :name="$icon" class="w-10 h-10 text-white" />
                            </div>
                        </div>
                    @endif

                    {{-- Title --}}
                    <flux:heading size="xl" class="text-gray-900 dark:text-white mb-3">
                        {{ $title }}
                    </flux:heading>

                    {{-- Expected Date Badge --}}
                    @if($expectedDate)
                        <div class="mb-4">
                            <flux:badge color="purple" size="sm">
                                Expected: {{ $expectedDate }}
                            </flux:badge>
                        </div>
                    @endif

                    {{-- Description --}}
                    <flux:text class="text-gray-600 dark:text-gray-300 mb-6">
                        {{ $description }}
                    </flux:text>

                    {{-- Features List --}}
                    @if(count($features) > 0)
                        <div class="mb-6">
                            <flux:text class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                                What to expect:
                            </flux:text>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-left">
                                @foreach($features as $feature)
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="check-circle" class="w-5 h-5 text-green-500 shrink-0" variant="solid" />
                                        <flux:text class="text-sm text-gray-700 dark:text-gray-300">{{ $feature }}</flux:text>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Notify Button --}}
                    @if($showNotifyButton)
                        <flux:button variant="primary" icon="bell" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700">
                            Notify Me When Available
                        </flux:button>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>
</div>
