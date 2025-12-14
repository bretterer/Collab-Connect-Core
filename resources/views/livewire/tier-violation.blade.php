<div class="-ml-24 -mr-24 min-h-screen bg-red-950 flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center">
        {{-- Warning Icon --}}
        <div class="mx-auto w-24 h-24 bg-red-900 rounded-full flex items-center justify-center mb-8 animate-pulse">
            <flux:icon name="shield-exclamation" class="w-14 h-14 text-red-400" />
        </div>

        {{-- Title --}}
        <h1 class="text-4xl font-bold text-white mb-4">
            Access Violation Detected
        </h1>

        {{-- Message --}}
        <div class="bg-red-900/50 border border-red-800 rounded-xl p-6 mb-8">
            <p class="text-red-200 text-lg leading-relaxed">
                You attempted to access features not included in your subscription plan.
                This action has been logged and your session has been terminated for security purposes.
            </p>
        </div>

        {{-- Details --}}
        <div class="text-red-300 text-sm mb-8 space-y-2">
            <p>If you believe this was an error, please contact support.</p>
            <p>To access premium features, please upgrade your subscription.</p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white text-red-900 font-semibold rounded-lg hover:bg-red-100 transition-colors">
                <flux:icon name="arrow-left" class="w-5 h-5 mr-2" />
                Return to Login
            </a>
            <a href="mailto:{{ config('collabconnect.support_email') }}" class="inline-flex items-center justify-center px-6 py-3 bg-red-800 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors border border-red-700">
                <flux:icon name="envelope" class="w-5 h-5 mr-2" />
                Contact Support
            </a>
        </div>

        {{-- Footer --}}
        <p class="mt-12 text-red-500 text-xs">
            Incident logged at {{ now()->format('Y-m-d H:i:s T') }}
        </p>
    </div>
</div>
