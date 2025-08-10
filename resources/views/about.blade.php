@extends('layouts.marketing')

@section('title', 'About Us - CollabConnect')
@section('description', 'Learn about CollabConnect\'s mission to transform local marketing by connecting businesses with authentic micro-influencers in Cincinnati and Dayton.')

@section('nav-cta')
<a href="/careers" class="btn-primary">
    Join Our Team
</a>
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 to-purple-700 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
            About CollabConnect
        </h1>
        <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
            We're building the future of local marketing, one authentic connection at a time.
        </p>
        <div class="inline-flex items-center px-4 py-2 bg-blue-100/20 rounded-full text-blue-100 text-sm font-semibold">
            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
            Founded in Springboro, Ohio
        </div>
    </div>
</section>

<!-- Mission Statement -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-8">
            Our Mission
        </h2>
        <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed mb-8">
            Local businesses deserve better than generic, distant advertising. We're creating a platform that connects them with authentic micro-influencers in their own communities—people who actually shop local, understand the culture, and can drive real foot traffic.
        </p>
        <div class="grid md:grid-cols-3 gap-8 mt-16">
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Local Focus</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We believe in the power of local communities and authentic connections that drive real business results.
                </p>
            </div>

            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Real Impact</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Every collaboration is designed to drive measurable results for businesses and meaningful opportunities for creators.
                </p>
            </div>

            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Community</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We support both businesses and creators, fostering relationships that benefit entire communities.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- The Story -->
<section class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                    Our Story
                </h2>
                <div class="space-y-6 text-gray-600 dark:text-gray-300">
                    <p class="text-lg leading-relaxed">
                        CollabConnect was born from a simple observation: local businesses were struggling with expensive, ineffective digital advertising while talented micro-influencers in the same communities had no way to connect with them.
                    </p>
                    <p class="leading-relaxed">
                        We saw coffee shops spending hundreds on Facebook ads that reached people three states away, while food bloggers with 5,000 local followers couldn't find collaboration opportunities. The disconnect was obvious, but the solution wasn't simple.
                    </p>
                    <p class="leading-relaxed">
                        Starting in Cincinnati and Dayton, we set out to build something different—a platform designed specifically for local markets that understands the unique dynamics of community-based marketing.
                    </p>
                    <p class="leading-relaxed">
                        Today, we're creating technology that makes these authentic connections possible, helping businesses reach their actual customers through creators who genuinely know and love their communities.
                    </p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Why Local Matters</h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                        <p class="text-gray-600 dark:text-gray-300">
                            <strong>73%</strong> of consumers are more likely to trust recommendations from local influencers
                        </p>
                    </div>
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                        <p class="text-gray-600 dark:text-gray-300">
                            <strong>8x</strong> higher engagement rates with micro-influencers vs. macro-influencers
                        </p>
                    </div>
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                        <p class="text-gray-600 dark:text-gray-300">
                            <strong>50%</strong> lower cost than traditional advertising with better ROI
                        </p>
                    </div>
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                        <p class="text-gray-600 dark:text-gray-300">
                            Local influencers drive <strong>real foot traffic</strong> and build lasting customer relationships
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Meet Our Founders -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Meet Our Founders
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Two passionate entrepreneurs bringing together business expertise and technical innovation to revolutionize local marketing.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 max-w-5xl mx-auto">
            <!-- Scott Bunch -->
            <div class="feature-card card-hover">
                <div class="text-center mb-8">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <img class="rounded-full" src="{{ Vite::asset('resources/images/scott-bunch.png') }}" alt="Scott Bunch">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Scott Bunch</h3>
                    <p class="text-lg text-blue-600 dark:text-blue-400 font-semibold mb-4">CEO & Co-Founder</p>
                </div>

                <div class="space-y-4 text-gray-600 dark:text-gray-300">
                    <p class="leading-relaxed">
                        Scott brings years of business development and marketing expertise to CollabConnect. His deep understanding of local business challenges and passion for community-driven solutions drives our mission to make authentic marketing accessible to all businesses.
                    </p>
                    <p class="leading-relaxed">
                        With a background in building relationships and scaling operations, Scott leads our strategic vision and partnerships, ensuring CollabConnect serves both businesses and creators with equal dedication.
                    </p>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm rounded-full">Business Development</span>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-sm rounded-full">Strategic Partnerships</span>
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-sm rounded-full">Local Marketing</span>
                    </div>
                </div>
            </div>

            <!-- Brian Retterer -->
            <div class="feature-card card-hover">
                <div class="text-center mb-8">
                    <div class="w-32 h-32 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                       <img class="rounded-full" src="{{ Vite::asset('resources/images/brian-retterer.png') }}" alt="Brian Retterer">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Brian Retterer</h3>
                    <p class="text-lg text-purple-600 dark:text-purple-400 font-semibold mb-4">CTO & Co-Founder</p>
                </div>

                <div class="space-y-4 text-gray-600 dark:text-gray-300">
                    <p class="leading-relaxed">
                        Brian is the technical visionary behind CollabConnect's platform. His expertise in full-stack development and passion for creating intuitive user experiences powers our smart matching algorithms and seamless collaboration tools.
                    </p>
                    <p class="leading-relaxed">
                        With a focus on scalable architecture and user-centered design, Brian ensures our platform grows with our community while maintaining the simplicity that makes local marketing accessible to everyone.
                    </p>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-sm rounded-full">Full-Stack Development</span>
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm rounded-full">Platform Architecture</span>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-sm rounded-full">Product Design</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                What Drives Us
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                These core values guide every decision we make and every feature we build.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Authenticity -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Authenticity</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We prioritize genuine connections over metrics, ensuring every collaboration feels natural and honest.
                </p>
            </div>

            <!-- Transparency -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Transparency</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    No hidden fees, no confusing terms. We believe in clear, honest communication with everyone we serve.
                </p>
            </div>

            <!-- Innovation -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Innovation</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We use cutting-edge technology to solve real problems, making complex processes simple and intuitive.
                </p>
            </div>

            <!-- Community -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Community</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Every feature we build strengthens local communities by fostering meaningful business relationships.
                </p>
            </div>

            <!-- Quality -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quality</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We focus on meaningful connections over quantity, ensuring every collaboration drives real value.
                </p>
            </div>

            <!-- Growth -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Continuous Growth</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We're always learning, improving, and evolving to better serve our community's changing needs.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-purple-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Ready to Transform Local Marketing?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Join us in building the future of authentic, community-driven marketing.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="/#beta-signup" class="btn-primary bg-white text-blue-600 hover:bg-blue-50 px-8 py-4 rounded-xl font-bold text-lg">
                Join the Beta
            </a>
            <a href="/careers" class="border-2 border-white text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-blue-600 transition-colors">
                Join Our Team
            </a>
        </div>
    </div>
</section>
@endsection