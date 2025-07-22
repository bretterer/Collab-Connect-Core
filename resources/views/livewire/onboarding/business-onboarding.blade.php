<div>
    @include('livewire.onboarding.shared.progress-bar')

    <div class="space-y-6">
        @if($currentStep === 1)
            @include('livewire.onboarding.business.step1')
        @elseif($currentStep === 2)
            @include('livewire.onboarding.business.step2')
        @elseif($currentStep === 3)
            @include('livewire.onboarding.business.step3')
        @endif

        @include('livewire.onboarding.shared.navigation')

    </div>
</div>
