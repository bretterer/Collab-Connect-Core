<!-- Progress Bar -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div class="text-center">
            <img class="block h-8 w-auto mx-auto dark:hidden"
                 src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
                 alt="CollabConnect Logo" />
            <img class="hidden h-8 w-auto mx-auto dark:block"
                 src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
                 alt="CollabConnect Logo" />
        </div>
        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <span>Step {{ $currentStep }} of {{ $this->getTotalSteps() }}</span>
        </div>
    </div>

    <div class="mt-4">
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                 style="width: {{ ($currentStep / $this->getTotalSteps()) * 100 }}%"></div>
        </div>
    </div>
</div>