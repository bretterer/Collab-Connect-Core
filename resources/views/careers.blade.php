@extends('layouts.marketing')

@section('title', 'Careers - CollabConnect')
@section('description', 'Join the CollabConnect team. Learn about our culture, values, and future opportunities in the local influencer marketing space.')

@section('nav-cta')
<a href="/contact" class="btn-primary">
    Contact Us
</a>
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 to-purple-700 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Join Our Mission
        </h1>
        <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
            Help us transform local marketing by connecting businesses with authentic micro-influencers in their communities.
        </p>
        <div class="inline-flex items-center px-4 py-2 bg-blue-100/20 rounded-full text-blue-100 text-sm font-semibold">
            <span class="w-2 h-2 bg-orange-400 rounded-full mr-2"></span>
            Currently Building Our Team • Cincinnati & Dayton
        </div>
    </div>
</section>

<!-- Current Status -->
<section class="py-16 bg-yellow-50 dark:bg-yellow-900/20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-yellow-200 dark:border-yellow-800">
            <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                No Current Openings
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 mb-6 max-w-2xl mx-auto">
                We're currently in beta and building our core platform. While we don't have any open positions right now, we're always interested in connecting with talented people who share our vision.
            </p>
            <a href="mailto:careers@collabconnect.app" class="btn-primary inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Get in Touch
            </a>
        </div>
    </div>
</section>

<!-- About Us -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                About CollabConnect
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                We're building the future of local influencer marketing, starting right here in Ohio.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Our Mission</h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                    Local businesses deserve better than generic, distant advertising. We're creating a platform that connects them with authentic micro-influencers in their own communities—people who actually shop local, understand the culture, and can drive real foot traffic.
                </p>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    By focusing exclusively on local markets and building smart matching technology, we're making influencer marketing accessible and effective for businesses of all sizes.
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-8">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Why We Started CollabConnect</h4>
                <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Local businesses were struggling with expensive, ineffective digital advertising
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Micro-influencers had no way to connect with local businesses
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Existing platforms were too complex and expensive for local markets
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        We saw the power of authentic, local recommendations firsthand
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Our Values
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                These principles guide everything we do and the team we're building.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Local First -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Local First</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We believe in the power of local communities and authentic connections that drive real business results.
                </p>
            </div>

            <!-- Transparency -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Transparency</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    No hidden fees, no confusing pricing. We believe in honest, straightforward business practices.
                </p>
            </div>

            <!-- Innovation -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Innovation</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We use smart technology to solve real problems, making complex marketing simple and accessible.
                </p>
            </div>

            <!-- Community -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Community</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We support both businesses and creators, fostering relationships that benefit entire communities.
                </p>
            </div>

            <!-- Quality -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quality</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We prioritize meaningful connections over quantity, ensuring every collaboration drives real value.
                </p>
            </div>

            <!-- Growth Mindset -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Growth Mindset</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We're always learning, iterating, and improving. Mistakes are opportunities to get better.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Future Opportunities -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Future Opportunities
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                As we grow, we'll be looking for talented people in these areas:
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-12">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Engineering & Product</h3>
                <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-sm">
                    <li>• Full-stack developers (Laravel, React)</li>
                    <li>• Mobile developers (React Native)</li>
                    <li>• Machine learning engineers</li>
                    <li>• DevOps engineers</li>
                    <li>• Product designers</li>
                </ul>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Business & Marketing</h3>
                <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-sm">
                    <li>• Community managers</li>
                    <li>• Sales development representatives</li>
                    <li>• Customer success managers</li>
                    <li>• Content creators</li>
                    <li>• Marketing specialists</li>
                </ul>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Operations</h3>
                <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-sm">
                    <li>• Business operations</li>
                    <li>• Data analysts</li>
                    <li>• Finance specialists</li>
                    <li>• Legal & compliance</li>
                    <li>• People operations</li>
                </ul>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Market Expansion</h3>
                <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-sm">
                    <li>• Market research analysts</li>
                    <li>• Local market specialists</li>
                    <li>• Partnership managers</li>
                    <li>• Regional community builders</li>
                    <li>• Brand ambassadors</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-purple-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Interested in Joining Us?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Even though we don't have open positions right now, we'd love to hear from passionate people who share our vision for local marketing.
        </p>
        
        <div class="max-w-lg mx-auto">
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                
                <p class="text-blue-100 mb-6">
                    Send us your resume and tell us why you're excited about local influencer marketing. We'll keep your information on file for future opportunities.
                </p>
                
                <a href="mailto:careers@collabconnect.app?subject=Interest%20in%20Future%20Opportunities" 
                   class="w-full btn-primary bg-white text-blue-600 hover:bg-blue-50 px-8 py-4 rounded-xl font-bold text-lg inline-flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    careers@collabconnect.app
                </a>
                
                <p class="text-blue-100 text-sm mt-4">
                    We'll reach out when positions open that match your skills and interests.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection