<div>
    {{-- Public Beta Banner --}}
    @if($betaBanner && $betaVisible)
        @php
            $betaTypeStyles = match($betaBanner['type'] ?? 'info') {
                'success' => [
                    'bg' => 'bg-green-50 dark:bg-green-900/20',
                    'border' => 'border-green-200 dark:border-green-800',
                    'text' => 'text-green-800 dark:text-green-200',
                    'icon' => 'text-green-400',
                    'iconPath' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                ],
                'error' => [
                    'bg' => 'bg-red-50 dark:bg-red-900/20',
                    'border' => 'border-red-200 dark:border-red-800',
                    'text' => 'text-red-800 dark:text-red-200',
                    'icon' => 'text-red-400',
                    'iconPath' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'
                ],
                'warning' => [
                    'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                    'border' => 'border-yellow-200 dark:border-yellow-800',
                    'text' => 'text-yellow-800 dark:text-yellow-200',
                    'icon' => 'text-yellow-400',
                    'iconPath' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'
                ],
                'info' => [
                    'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                    'border' => 'border-blue-200 dark:border-blue-800',
                    'text' => 'text-blue-800 dark:text-blue-200',
                    'icon' => 'text-blue-400',
                    'iconPath' => 'M11.25 11.25l.041-.020a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'
                ],
                default => [
                    'bg' => 'bg-gray-50 dark:bg-gray-900/20',
                    'border' => 'border-gray-200 dark:border-gray-800',
                    'text' => 'text-gray-800 dark:text-gray-200',
                    'icon' => 'text-gray-400',
                    'iconPath' => 'M11.25 11.25l.041-.020a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'
                ]
            };
        @endphp
        
        <div class="mb-6" 
             x-data="{ showBeta: true }" 
             x-show="showBeta"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            
            <div class="rounded-lg {{ $betaTypeStyles['bg'] }} {{ $betaTypeStyles['border'] }} border-l-4 p-4 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 {{ $betaTypeStyles['icon'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $betaTypeStyles['iconPath'] }}" />
                        </svg>
                    </div>
                    
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium {{ $betaTypeStyles['text'] }}">
                            {{ $betaBanner['message'] }}
                        </p>
                    </div>
                    
                    @if($betaBanner['closable'] ?? false)
                        <div class="ml-auto flex-shrink-0">
                            <button wire:click="closeBeta" 
                                    x-on:click="showBeta = false"
                                    class="inline-flex rounded-md p-1.5 {{ $betaTypeStyles['text'] }} hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Session Flash Banner --}}
    @if($banner && $visible)
        @php
            $typeStyles = match($banner['type'] ?? 'info') {
                'success' => [
                    'bg' => 'bg-green-50 dark:bg-green-900/20',
                    'border' => 'border-green-200 dark:border-green-800',
                    'text' => 'text-green-800 dark:text-green-200',
                    'icon' => 'text-green-400',
                    'iconPath' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                ],
                'error' => [
                    'bg' => 'bg-red-50 dark:bg-red-900/20',
                    'border' => 'border-red-200 dark:border-red-800',
                    'text' => 'text-red-800 dark:text-red-200',
                    'icon' => 'text-red-400',
                    'iconPath' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'
                ],
                'warning' => [
                    'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                    'border' => 'border-yellow-200 dark:border-yellow-800',
                    'text' => 'text-yellow-800 dark:text-yellow-200',
                    'icon' => 'text-yellow-400',
                    'iconPath' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'
                ],
                'info' => [
                    'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                    'border' => 'border-blue-200 dark:border-blue-800',
                    'text' => 'text-blue-800 dark:text-blue-200',
                    'icon' => 'text-blue-400',
                    'iconPath' => 'M11.25 11.25l.041-.020a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'
                ],
                default => [
                    'bg' => 'bg-gray-50 dark:bg-gray-900/20',
                    'border' => 'border-gray-200 dark:border-gray-800',
                    'text' => 'text-gray-800 dark:text-gray-200',
                    'icon' => 'text-gray-400',
                    'iconPath' => 'M11.25 11.25l.041-.020a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'
                ]
            };
        @endphp
        
        <div class="mb-6" 
             x-data="{ show: true }" 
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            
            <div class="rounded-lg {{ $typeStyles['bg'] }} {{ $typeStyles['border'] }} border-l-4 p-4 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 {{ $typeStyles['icon'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeStyles['iconPath'] }}" />
                        </svg>
                    </div>
                    
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium {{ $typeStyles['text'] }}">
                            {{ $banner['message'] }}
                        </p>
                    </div>
                    
                    @if($banner['closable'] ?? false)
                        <div class="ml-auto flex-shrink-0">
                            <button wire:click="close" 
                                    x-on:click="show = false"
                                    class="inline-flex rounded-md p-1.5 {{ $typeStyles['text'] }} hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>