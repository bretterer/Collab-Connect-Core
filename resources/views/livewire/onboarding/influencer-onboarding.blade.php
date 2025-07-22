<div>
    @include('livewire.onboarding.shared.progress-bar')

    <div class="space-y-6">
        @if ($currentStep === 1)
            @include('livewire.onboarding.influencer.step1')
        @elseif($currentStep === 2)
            @include('livewire.onboarding.influencer.step2')
        @endif

        @include('livewire.onboarding.shared.navigation')
    </div>
</div>
