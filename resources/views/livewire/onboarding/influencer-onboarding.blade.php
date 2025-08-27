<!-- Background gradient -->
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900">
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
                Set Up Your Influencer Profile
            </flux:heading>
            <flux:subheading class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Let's create your influencer profile to connect with amazing brands and campaigns
            </flux:subheading>
        </div>

        @include('livewire.onboarding.influencer.progress', [
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
                <flux:card class="p-6 bg-gradient-to-br from-purple-500 to-pink-600 text-white border-0 shadow-lg">
                    <flux:heading class="text-white mb-4">
                        💡 Quick Tips
                    </flux:heading>
                    <div class="space-y-3">
                        @foreach($currentStepData['tips'] as $tip)
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-white rounded-full mt-2 flex-shrink-0"></div>
                                <p class="text-sm text-purple-50">{{ $tip }}</p>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
                @endif
            </div>

            <!-- Main Form -->
            <div class="xl:col-span-2">
                <flux:card class="p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl">
                    @include('livewire.onboarding.influencer.' . $currentStepData['component'])

                    @include('livewire.onboarding.influencer.navigation')
                </flux:card>
            </div>
        </div>
    </div>
</div>
