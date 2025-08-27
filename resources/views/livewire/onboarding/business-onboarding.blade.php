<!-- Background gradient -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900">
    <div class="max-w-5xl mx-auto px-4 py-8">

        <!-- Header with Logo -->
        <div class="text-center mb-12">
            <div class="flex justify-center mb-6">
                <img
                    src="{{ Vite::asset('resources/images/CollabConnect.svg') }}"
                    alt="CollabConnect Logo"
                    class="h-20 w-auto dark:hidden"
                />
                <img
                    src="{{ Vite::asset('resources/images/CollabConnectDark.svg') }}"
                    alt="CollabConnect Logo"
                    class="h-20 w-auto hidden dark:block"
                />
            </div>

            <flux:heading size="xl" class="text-gray-900 dark:text-white mb-4">
                Set Up Your Business Profile
            </flux:heading>
            <flux:subheading class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Let's create your business profile to connect with the perfect influencers for your brand
            </flux:subheading>
        </div>

        @include('livewire.onboarding.business.progress', [
            'currentStep' => $currentStep,
            'steps' => $steps,
            'maxSteps' => $maxSteps
        ])

        <!-- Main Form Container -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            <!-- Side Panel -->
            <div class="xl:col-span-1 space-y-6">
                @if (!empty($currentStepData['tips']))
                <!-- Tips Card -->
                <flux:card class="p-6 bg-gradient-to-br from-blue-500 to-cyan-600 text-white border-0 shadow-lg">
                    <flux:heading class="text-white mb-4">
                        💡 Quick Tips
                    </flux:heading>
                    <div class="space-y-3">
                        @foreach($currentStepData['tips'] as $tip)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-white rounded-full mt-2 flex-shrink-0"></div>
                                <p class="text-sm text-blue-50">{{ $tip }}</p>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
                @endif
            </div>

            <!-- Main Form -->
            <div class="xl:col-span-2">
                <flux:card class="p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl">
                    @include('livewire.onboarding.business.' . $currentStepData['component'])

                    @include('livewire.onboarding.business.navigation')
                </flux:card>
            </div>
        </div>
    </div>
</div>