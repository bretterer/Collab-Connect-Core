<section class="relative py-20" style="background-color: {{ $data['background_color'] ?? '#ffffff' }};">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                    {{ $data['headline'] ?? 'Your Compelling Headline' }}
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    {{ $data['subheadline'] ?? 'Supporting text that explains your offer' }}
                </p>
                <a href="{{ $data['cta_url'] ?? '#' }}" class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-colors">
                    {{ $data['cta_text'] ?? 'Get Started' }}
                </a>
            </div>
            @if(!empty($data['image']))
                <div class="flex justify-center lg:justify-end">
                    <img src="{{ $data['image'] }}" alt="Hero Image" class="rounded-lg shadow-xl max-w-full h-auto">
                </div>
            @endif
        </div>
    </div>
</section>
