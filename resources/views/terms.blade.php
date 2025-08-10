@extends('layouts.marketing')

@section('title', 'Terms of Service - CollabConnect')
@section('description', 'CollabConnect\'s terms of service. Learn about the terms and conditions for using our platform.')

@section('nav-cta')
<a href="/contact" class="btn-primary">
    Contact Us
</a>
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 to-purple-700 text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Terms of Service
        </h1>
        <p class="text-xl text-blue-100 mb-4">
            Last updated: January 1, 2025
        </p>
        <p class="text-lg text-blue-100">
            These terms govern your use of CollabConnect and outline the rights and responsibilities of all users.
        </p>
    </div>
</section>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Table of Contents -->
        <div class="lg:w-1/4">
            <div class="content-section sticky top-24">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Table of Contents</h3>
                <nav class="space-y-1">
                    <a href="#acceptance" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">1. Acceptance of Terms</a>
                    <a href="#description" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">2. Service Description</a>
                    <a href="#eligibility" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">3. Eligibility</a>
                    <a href="#accounts" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">4. User Accounts</a>
                    <a href="#conduct" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">5. User Conduct</a>
                    <a href="#content" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">6. Content & IP</a>
                    <a href="#campaigns" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">7. Campaign Terms</a>
                    <a href="#payments" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">8. Payments & Fees</a>
                    <a href="#privacy" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">9. Privacy</a>
                    <a href="#disclaimers" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">10. Disclaimers</a>
                    <a href="#limitation" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">11. Limitation of Liability</a>
                    <a href="#indemnification" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">12. Indemnification</a>
                    <a href="#termination" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">13. Termination</a>
                    <a href="#dispute" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">14. Dispute Resolution</a>
                    <a href="#general" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">15. General Provisions</a>
                    <a href="#contact" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">16. Contact Information</a>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <div class="lg:w-3/4">
            <!-- Acceptance of Terms -->
            <section id="acceptance" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Acceptance of Terms</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    By accessing or using CollabConnect ("the Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to all terms, you may not access or use the Service.
                </p>
                <p class="text-gray-600 dark:text-gray-300">
                    These Terms constitute a legally binding agreement between you and CollabConnect. We reserve the right to modify these Terms at any time, and your continued use of the Service constitutes acceptance of any modifications.
                </p>
            </section>

            <!-- Service Description -->
            <section id="description" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. Service Description</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    CollabConnect is a platform that connects local businesses with micro-influencers for collaborative marketing campaigns. Our services include:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
                    <li>Campaign creation and management tools for businesses</li>
                    <li>Influencer discovery and matching algorithms</li>
                    <li>Communication and collaboration features</li>
                    <li>Payment processing and campaign tracking</li>
                    <li>Performance analytics and reporting</li>
                </ul>
            </section>

            <!-- Eligibility -->
            <section id="eligibility" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. Eligibility</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">To use CollabConnect, you must:</p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li>Be at least 18 years old</li>
                    <li>Have the legal capacity to enter into contracts</li>
                    <li>Not be prohibited from using our services under applicable law</li>
                    <li>Provide accurate and complete information during registration</li>
                </ul>
                <p class="text-gray-600 dark:text-gray-300">
                    By using our Service, you represent and warrant that you meet these eligibility requirements.
                </p>
            </section>

            <!-- User Accounts -->
            <section id="accounts" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. User Accounts</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Account Registration</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    You must create an account to access most features of our Service. You agree to provide accurate, current, and complete information and to keep your account information updated.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Account Security</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must notify us immediately of any unauthorized use.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Account Types</h3>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
                    <li><strong>Business Accounts:</strong> For businesses seeking influencer partnerships</li>
                    <li><strong>Influencer Accounts:</strong> For content creators and micro-influencers</li>
                    <li><strong>Admin Accounts:</strong> For CollabConnect staff and moderators</li>
                </ul>
            </section>

            <!-- User Conduct -->
            <section id="conduct" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. User Conduct</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Acceptable Use</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">You agree to use our Service only for lawful purposes and in accordance with these Terms.</p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Prohibited Activities</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">You may not:</p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
                    <li>Violate any applicable laws or regulations</li>
                    <li>Infringe on intellectual property rights</li>
                    <li>Post false, misleading, or defamatory content</li>
                    <li>Engage in harassment, bullying, or discriminatory behavior</li>
                    <li>Attempt to gain unauthorized access to our systems</li>
                    <li>Use automated tools to scrape or harvest data</li>
                    <li>Circumvent our payment systems or fees</li>
                    <li>Create fake accounts or misrepresent your identity</li>
                    <li>Engage in spam or unsolicited communications</li>
                </ul>
            </section>

            <!-- Content & Intellectual Property -->
            <section id="content" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Content & Intellectual Property</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">User Content</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    You retain ownership of content you create and post on our platform. By posting content, you grant CollabConnect a non-exclusive, worldwide, royalty-free license to use, display, and distribute your content in connection with our Service.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Platform Content</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Our platform, including its design, functionality, and original content, is owned by CollabConnect and protected by intellectual property laws.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Content Standards</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    All content must comply with our community guidelines and applicable laws. We reserve the right to remove content that violates these standards.
                </p>
            </section>

            <!-- Campaign Terms -->
            <section id="campaigns" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">7. Campaign Terms</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Campaign Creation</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Businesses can create campaigns with specific requirements, deliverables, and compensation terms. All campaign details must be accurate and complete.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Influencer Applications</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Influencers can apply to campaigns that match their profile and interests. Applications should demonstrate genuine interest and relevant experience.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Campaign Execution</h3>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
                    <li>Both parties must fulfill their obligations as outlined in the campaign agreement</li>
                    <li>Communication should be professional and respectful</li>
                    <li>Deliverables must meet the specified requirements and deadlines</li>
                    <li>All content must comply with FTC disclosure guidelines</li>
                </ul>
            </section>

            <!-- Payments & Fees -->
            <section id="payments" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">8. Payments & Fees</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Subscription Fees</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    CollabConnect operates on a subscription-based model. Fees are charged monthly or annually based on your chosen plan.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Campaign Payments</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We facilitate payments between businesses and influencers but do not take commission on campaign fees. Payment terms are set by mutual agreement between parties.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Refund Policy</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Subscription fees are non-refundable except as required by law. Campaign-specific payment disputes should be resolved between the parties involved.
                </p>
            </section>

            <!-- Privacy -->
            <section id="privacy" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">9. Privacy</h2>
                <p class="text-gray-600 dark:text-gray-300">
                    Your privacy is important to us. Our collection and use of personal information is governed by our <a href="/privacy" class="text-blue-600 dark:text-blue-400 hover:underline">Privacy Policy</a>, which is incorporated into these Terms by reference.
                </p>
            </section>

            <!-- Disclaimers -->
            <section id="disclaimers" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">10. Disclaimers</h2>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 rounded-lg mb-4">
                    <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">IMPORTANT DISCLAIMERS:</p>
                    <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 text-sm">
                        <li>THE SERVICE IS PROVIDED "AS IS" WITHOUT WARRANTIES OF ANY KIND</li>
                        <li>WE DO NOT GUARANTEE CAMPAIGN SUCCESS OR SPECIFIC RESULTS</li>
                        <li>WE ARE NOT RESPONSIBLE FOR USER-TO-USER INTERACTIONS OR AGREEMENTS</li>
                        <li>THIRD-PARTY INTEGRATIONS ARE PROVIDED WITHOUT WARRANTY</li>
                    </ul>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                    CollabConnect is a platform that facilitates connections between businesses and influencers. We do not guarantee the quality, safety, or legality of campaigns, user conduct, or content created through our platform.
                </p>
            </section>

            <!-- Limitation of Liability -->
            <section id="limitation" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">11. Limitation of Liability</h2>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 rounded-lg mb-4">
                    <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2 uppercase">Liability Limitation:</p>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        TO THE MAXIMUM EXTENT PERMITTED BY LAW, COLLABCONNECT SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING BUT NOT LIMITED TO LOSS OF PROFITS, DATA, OR BUSINESS OPPORTUNITIES.
                    </p>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                    Our total liability to you for any claims arising from your use of the Service shall not exceed the amount you paid us in the 12 months preceding the claim.
                </p>
            </section>

            <!-- Indemnification -->
            <section id="indemnification" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">12. Indemnification</h2>
                <p class="text-gray-600 dark:text-gray-300">
                    You agree to indemnify, defend, and hold harmless CollabConnect from and against any claims, damages, losses, costs, or expenses arising from your use of the Service, violation of these Terms, or infringement of any third-party rights.
                </p>
            </section>

            <!-- Termination -->
            <section id="termination" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">13. Termination</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">User Termination</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    You may terminate your account at any time by contacting us or using account deletion features in your settings.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Our Right to Terminate</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We may suspend or terminate your access to the Service at any time for violations of these Terms, illegal activity, or other reasons we deem appropriate.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Effect of Termination</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Upon termination, your right to use the Service will cease immediately. Provisions of these Terms that by their nature should survive termination will remain in effect.
                </p>
            </section>

            <!-- Dispute Resolution -->
            <section id="dispute" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">14. Dispute Resolution</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Informal Resolution</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Before pursuing formal legal action, you agree to first contact us at <a href="mailto:legal@collabconnect.com" class="text-blue-600 dark:text-blue-400 hover:underline">legal@collabconnect.com</a> to resolve any disputes informally.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Governing Law</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    These Terms are governed by the laws of the State of Ohio, without regard to conflict of law principles.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Jurisdiction</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Any legal proceedings arising from these Terms will be subject to the exclusive jurisdiction of the courts in Ohio.
                </p>
            </section>

            <!-- General Provisions -->
            <section id="general" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">15. General Provisions</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Entire Agreement</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    These Terms, together with our Privacy Policy, constitute the entire agreement between you and CollabConnect.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Severability</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    If any provision of these Terms is found to be unenforceable, the remaining provisions will remain in full force and effect.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Assignment</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    You may not assign your rights under these Terms without our written consent. We may assign our rights to any affiliate or successor entity.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Updates to Terms</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We may update these Terms periodically. Material changes will be communicated through email or prominent platform notices. Continued use constitutes acceptance of updated Terms.
                </p>
            </section>

            <!-- Contact Information -->
            <section id="contact" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">16. Contact Information</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    For questions about these Terms of Service, please contact us:
                </p>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-gray-600 dark:text-gray-300">
                        <strong>Legal Inquiries:</strong> <a href="mailto:legal@collabconnect.com" class="text-blue-600 dark:text-blue-400 hover:underline">legal@collabconnect.com</a><br>
                        <strong>General Support:</strong> <a href="mailto:hello@collabconnect.com" class="text-blue-600 dark:text-blue-400 hover:underline">hello@collabconnect.com</a><br>
                        <strong>Address:</strong> CollabConnect, Cincinnati & Dayton, Ohio<br>
                        <strong>Response Time:</strong> We will respond to legal inquiries within 7 business days
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection