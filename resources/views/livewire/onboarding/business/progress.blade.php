<!-- Progress Section -->
<div class="mb-10">
    <flux:card class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <flux:heading class="text-gray-800 dark:text-gray-200">
                Step {{ $currentStep }} of {{ $maxSteps }}: {{ $steps[$currentStep]['title'] ?? 'Step' }}
            </flux:heading>
            <flux:badge color="blue">{{ round(($currentStep / $maxSteps) * 100) }}% Complete</flux:badge>
        </div>

        <!-- Enhanced Progress Bar -->
        <div class="relative">
            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-3 rounded-full transition-all duration-500 ease-out"
                     style="width: {{ round(($currentStep / $maxSteps) * 100) }}%"></div>
            </div>
        </div>
    </flux:card>
</div>