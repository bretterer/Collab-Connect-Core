<flux:card class="mb-8 bg-gradient-to-br {{ $gradients['bg'] }} border-2 {{ $gradients['border'] }}">
    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
        <!-- Icon -->
        <div class="flex-shrink-0">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br {{ $gradients['icon'] }} flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1">
            <flux:heading size="lg" class="text-gray-900 dark:text-white mb-2">
                {{ $heading }}
            </flux:heading>
            <flux:text class="text-gray-700 dark:text-gray-300 mb-4">
                {{ $description }}
            </flux:text>

            <!-- Features List -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-6">
                @foreach($features as $feature)
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <flux:text class="text-sm text-gray-700 dark:text-gray-300">{{ $feature }}</flux:text>
                    </div>
                @endforeach
            </div>

            <!-- CTA Button -->
            <flux:button
                href="{{ route('billing') }}"
                wire:navigate
                variant="primary"
                icon="sparkles"
                class="bg-gradient-to-r {{ $gradients['button'] }}"
            >
                {{ $buttonText }}
            </flux:button>
        </div>
    </div>
</flux:card>
