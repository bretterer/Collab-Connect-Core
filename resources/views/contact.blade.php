@extends('layouts.marketing')

@section('title', 'Contact Us - CollabConnect')
@section('description', 'Get in touch with CollabConnect. We\'re here to help businesses and influencers connect in Cincinnati and Dayton.')

@section('nav-cta')
<button onclick="smoothScrollTo('contact-form')" class="btn-primary">
    Get in Touch
</button>
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 to-purple-700 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Let's Connect
        </h1>
        <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
            Have questions about CollabConnect? Want to learn more about our platform? We'd love to hear from you.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="smoothScrollTo('contact-form')" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                Send Us a Message
            </button>
            <a href="mailto:hello@collabconnect.com" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                hello@collabconnect.com
            </a>
        </div>
    </div>
</section>

<!-- Contact Options -->
<section class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Multiple Ways to Reach Us
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Choose the method that works best for you. We're here to help with any questions about our platform.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <!-- Email -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Email Support</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Get detailed answers to your questions</p>
                <a href="mailto:hello@collabconnect.com" class="text-blue-600 hover:text-blue-700 font-medium">hello@collabconnect.com</a>
            </div>

            <!-- Location -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Our Location</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Serving businesses and creators in</p>
                <p class="text-gray-800 dark:text-gray-200 font-medium">Cincinnati & Dayton, Ohio</p>
            </div>

            <!-- Response Time -->
            <div class="feature-card card-hover text-center">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Quick Response</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">We typically respond within</p>
                <p class="text-gray-800 dark:text-gray-200 font-medium">24 hours</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section id="contact-form" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Send Us a Message
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Fill out the form below and we'll get back to you as soon as possible.
            </p>
        </div>

        @if(session('success'))
            <div class="max-w-2xl mx-auto mb-6 p-4 bg-green-100 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-2xl mx-auto mb-6 p-4 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg">
                <ul class="text-red-800 dark:text-red-200">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="max-w-2xl mx-auto" method="POST" action="{{ route('contact.store') }}">
            @csrf
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                    <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                           class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}"
                           class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                </div>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                <input type="email" id="email" name="email" required value="{{ old('email') }}"
                       class="input-field w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            </div>

            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                <select id="subject" name="subject" required
                        class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                    <option value="">Please select a topic</option>
                    <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                    <option value="business" {{ old('subject') == 'business' ? 'selected' : '' }}>Business Partnership</option>
                    <option value="influencer" {{ old('subject') == 'influencer' ? 'selected' : '' }}>Influencer Questions</option>
                    <option value="technical" {{ old('subject') == 'technical' ? 'selected' : '' }}>Technical Support</option>
                    <option value="beta" {{ old('subject') == 'beta' ? 'selected' : '' }}>Beta Program</option>
                    <option value="media" {{ old('subject') == 'media' ? 'selected' : '' }}>Press & Media</option>
                    <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                <textarea id="message" name="message" rows="6" required
                          class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                          placeholder="Tell us more about your inquiry...">{{ old('message') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-start space-x-3">
                    <input type="checkbox" name="newsletter" value="1" {{ old('newsletter') ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        I'd like to receive updates about CollabConnect's launch and new features.
                    </span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full btn-primary text-lg py-4">
                Send Message
            </button>
        </form>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Frequently Asked Questions
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Quick answers to common questions about CollabConnect.
            </p>
        </div>

        <div class="space-y-6">
            <div class="feature-card card-hover">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">When will CollabConnect launch?</h3>
                <p class="text-gray-600 dark:text-gray-300">We're currently in beta development and planning to launch in early 2025. Join our beta crew to get early access!</p>
            </div>
            
            <div class="feature-card card-hover">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Is CollabConnect only for Cincinnati and Dayton?</h3>
                <p class="text-gray-600 dark:text-gray-300">We're starting with Cincinnati and Dayton to perfect our local approach, but we plan to expand to other cities based on demand.</p>
            </div>
            
            <div class="feature-card card-hover">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">How much will it cost to use CollabConnect?</h3>
                <p class="text-gray-600 dark:text-gray-300">We're working on affordable subscription plans for both businesses and influencers. No commission fees - just transparent monthly pricing.</p>
            </div>
            
            <div class="feature-card card-hover">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">What makes CollabConnect different?</h3>
                <p class="text-gray-600 dark:text-gray-300">We focus exclusively on local connections with authentic micro-influencers, smart matching algorithms, and a commission-free model.</p>
            </div>
        </div>
    </div>
</section>
@endsection