<div>
    <!-- Floating Feedback Bug Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button 
            wire:click="openModal"
            class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 group"
            title="Send Feedback">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <!-- Tooltip -->
            <div class="absolute right-full mr-3 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-sm px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                Report Bug / Send Feedback
            </div>
        </button>
    </div>

    <!-- Feedback Modal -->
    <div 
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" x-on:click="show = false"></div>

        <!-- Modal content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-6"
            >
                <!-- Modal Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Send Feedback</h3>
                        <button 
                            wire:click="closeModal"
                            class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form wire:submit="submit" class="space-y-6">
                    <!-- Feedback Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            What type of feedback are you providing?
                        </label>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach(\App\Enums\FeedbackType::cases() as $feedbackType)
                                <label class="relative flex items-start p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors {{ $type === $feedbackType->value ? 'ring-2 ring-blue-600 border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600' }}">
                                    <input 
                                        type="radio" 
                                        wire:model.live="type" 
                                        value="{{ $feedbackType->value }}"
                                        class="sr-only"
                                    >
                                    <div class="flex items-start space-x-3">
                                        <svg class="h-6 w-6 {{ $type === $feedbackType->value ? 'text-blue-600' : 'text-gray-400' }} flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feedbackType->icon() }}" />
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $feedbackType->label() }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $feedbackType->description() }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($type === $feedbackType->value)
                                        <div class="absolute top-4 right-4">
                                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subject
                        </label>
                        <input 
                            wire:model="subject" 
                            id="subject"
                            type="text"
                            placeholder="Brief summary of your feedback"
                            class="block w-full px-3 py-3 text-base rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white dark:placeholder-gray-400"
                        >
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Message
                        </label>
                        <textarea 
                            wire:model="message" 
                            id="message"
                            rows="6"
                            placeholder="Provide details about your feedback..."
                            class="block w-full px-3 py-3 text-base rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white dark:placeholder-gray-400 resize-vertical"
                        ></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Screenshot Section -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Screenshot (Optional)
                            </label>
                            <button 
                                type="button" 
                                id="captureScreenshot"
                                class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                            >
                                📸 Capture Screenshot
                            </button>
                        </div>
                        
                        <div id="screenshotPreview" class="hidden mt-2 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-center">
                            <img id="screenshotImage" class="max-w-full h-auto rounded-lg mx-auto mb-2">
                            <button 
                                type="button" 
                                id="removeScreenshot"
                                class="text-sm text-red-600 hover:text-red-700"
                            >
                                Remove Screenshot
                            </button>
                        </div>
                        
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Screenshots help us understand the issue better. Only the visible page content will be captured.
                        </p>
                    </div>
                </form>

                <!-- Modal Footer -->
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <button 
                        type="button"
                        wire:click="closeModal"
                        class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        wire:click="submit" 
                        wire:loading.attr="disabled"
                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                    >
                        <span wire:loading.remove>Send Feedback</span>
                        <span wire:loading>Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Screenshot Capture -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const captureBtn = document.getElementById('captureScreenshot');
            const screenshotPreview = document.getElementById('screenshotPreview');
            const screenshotImage = document.getElementById('screenshotImage');
            const removeBtn = document.getElementById('removeScreenshot');

            if (captureBtn) {
                captureBtn.addEventListener('click', async function() {
                    try {
                        // Show loading state
                        captureBtn.textContent = '📷 Capturing...';
                        captureBtn.disabled = true;
                        
                        // Use html2canvas to capture screenshot
                        if (typeof html2canvas !== 'undefined') {
                            // Hide the feedback modal temporarily for a cleaner screenshot
                            const modal = document.querySelector('[x-data*="showModal"]');
                            if (modal) {
                                modal.style.display = 'none';
                            }
                            
                            // Wait a moment for the modal to hide
                            await new Promise(resolve => setTimeout(resolve, 100));
                            
                            const canvas = await html2canvas(document.body, {
                                height: window.innerHeight,
                                width: window.innerWidth,
                                useCORS: true,
                                allowTaint: false,
                                scale: 0.5, // Reduce scale to improve performance and reduce errors
                                backgroundColor: '#ffffff',
                                scrollX: 0,
                                scrollY: 0,
                                ignoreElements: function(element) {
                                    // Skip elements that might cause issues
                                    return element.classList.contains('ignore-screenshot') ||
                                           element.tagName === 'SCRIPT' ||
                                           element.tagName === 'STYLE' ||
                                           (element.style && element.style.display === 'none');
                                },
                                onclone: function(clonedDoc) {
                                    // Remove problematic elements from the clone
                                    const problematicElements = clonedDoc.querySelectorAll('script, style, link[rel="stylesheet"]');
                                    problematicElements.forEach(el => el.remove());
                                    
                                    // Fix any malformed SVG paths
                                    const svgPaths = clonedDoc.querySelectorAll('svg path');
                                    svgPaths.forEach(path => {
                                        const d = path.getAttribute('d');
                                        if (d && d.includes('515.356')) {
                                            // This seems to be a malformed path, remove it
                                            path.remove();
                                        }
                                    });
                                }
                            });
                            
                            // Show the modal again
                            if (modal) {
                                modal.style.display = '';
                            }
                            
                            const dataURL = canvas.toDataURL('image/png', 0.8);
                            
                            // Show preview
                            screenshotImage.src = dataURL;
                            screenshotPreview.classList.remove('hidden');
                            
                            // Send to Livewire
                            @this.set('screenshotData', dataURL);
                            
                            // Success feedback
                            captureBtn.textContent = '✅ Screenshot Captured';
                            setTimeout(() => {
                                captureBtn.textContent = '📸 Capture Screenshot';
                            }, 2000);
                            
                        } else {
                            alert('Screenshot functionality is not available. Please install html2canvas library.');
                        }
                    } catch (error) {
                        console.error('Error capturing screenshot:', error);
                        
                        // Show the modal again if it was hidden
                        const modal = document.querySelector('[x-data*="showModal"]');
                        if (modal) {
                            modal.style.display = '';
                        }
                        
                        // More specific error messages
                        let errorMessage = 'Unable to capture screenshot. ';
                        if (error.message.includes('oklch')) {
                            errorMessage += 'Your browser may not support some modern CSS features used on this page.';
                        } else if (error.message.includes('path')) {
                            errorMessage += 'There was an issue with some graphics on the page.';
                        } else {
                            errorMessage += 'Please try again or proceed without a screenshot.';
                        }
                        
                        alert(errorMessage);
                        
                        captureBtn.textContent = '❌ Capture Failed';
                        setTimeout(() => {
                            captureBtn.textContent = '📸 Capture Screenshot';
                        }, 3000);
                    } finally {
                        captureBtn.disabled = false;
                    }
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    screenshotPreview.classList.add('hidden');
                    screenshotImage.src = '';
                    @this.set('screenshotData', null);
                });
            }
        });
    </script>
</div>