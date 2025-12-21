@extends('layouts.marketing')

@section('title', 'Privacy Policy - CollabConnect')
@section('description', 'CollabConnect\'s privacy policy. Learn how we protect your data and respect your privacy.')

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
            Privacy Policy
        </h1>
        <p class="text-xl text-blue-100 mb-4">
            Last updated: January 1, 2025
        </p>
        <p class="text-lg text-blue-100">
            Your privacy is important to us. This policy explains how CollabConnect collects, uses, and protects your personal information.
        </p>
    </div>
</section>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Table of Contents -->
        <div class="lg:w-1/4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 mb-8 sticky top-24">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Table of Contents</h3>
                <nav class="space-y-1">
                    <a href="#overview" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">1. Overview</a>
                    <a href="#information-we-collect" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">2. Information We Collect</a>
                    <a href="#how-we-use-information" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">3. How We Use Information</a>
                    <a href="#information-sharing" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">4. Information Sharing</a>
                    <a href="#data-security" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">5. Data Security</a>
                    <a href="#cookies" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">6. Cookies & Analytics</a>
                    <a href="#meta-pixel" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">7. Meta (Facebook) Pixel</a>
                    <a href="#your-rights" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">8. Your Rights</a>
                    <a href="#children" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">9. Children's Privacy</a>
                    <a href="#international" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">10. International Users</a>
                    <a href="#changes" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">11. Policy Changes</a>
                    <a href="#contact" class="block px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">12. Contact Us</a>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <div class="lg:w-3/4">
            <!-- Overview -->
            <section id="overview" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Overview</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    CollabConnect ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services.
                </p>
                <p class="text-gray-600 dark:text-gray-300">
                    By using CollabConnect, you consent to the data practices described in this policy. If you do not agree with the practices described in this policy, please do not use our services.
                </p>
            </section>

            <!-- Information We Collect -->
            <section id="information-we-collect" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. Information We Collect</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Personal Information</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We collect information you provide directly to us, including:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li>Name, email address, and contact information</li>
                    <li>Business information (for business accounts)</li>
                    <li>Social media account details and follower counts (for influencer accounts)</li>
                    <li>Profile pictures and bio information</li>
                    <li>Campaign preferences and interests</li>
                    <li>Communication records with us</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Automatically Collected Information</h3>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li>Device information (browser type, operating system)</li>
                    <li>Usage data (pages visited, time spent, clicks)</li>
                    <li>IP address and location data</li>
                    <li>Cookies and similar tracking technologies</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Third-Party Information</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We may receive information about you from third-party services you connect to your account, such as social media platforms for verification purposes.
                </p>
            </section>

            <!-- How We Use Information -->
            <section id="how-we-use-information" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. How We Use Information</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">We use the information we collect to:</p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
                    <li>Provide and improve our platform services</li>
                    <li>Match businesses with relevant influencers</li>
                    <li>Process transactions and manage subscriptions</li>
                    <li>Send notifications about campaigns and platform updates</li>
                    <li>Provide customer support and respond to inquiries</li>
                    <li>Analyze usage patterns to improve user experience</li>
                    <li>Prevent fraud and ensure platform security</li>
                    <li>Comply with legal obligations</li>
                    <li>Send marketing communications (with your consent)</li>
                </ul>
            </section>

            <!-- Information Sharing -->
            <section id="information-sharing" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. Information Sharing</h2>
                
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">With Other Users</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    When you create a profile, certain information (name, business details, social media metrics) may be visible to other users for collaboration purposes.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">With Service Providers</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We share information with trusted service providers who help us operate our platform, including payment processors, email services, and analytics providers.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Legal Requirements</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We may disclose information if required by law, court order, or to protect the rights and safety of our users and platform.
                </p>

                <p class="text-gray-600 dark:text-gray-300">
                    <strong>We do not sell your personal information to third parties.</strong>
                </p>
            </section>

            <!-- Data Security -->
            <section id="data-security" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. Data Security</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
                    <li>Encryption of data in transit and at rest</li>
                    <li>Regular security audits and updates</li>
                    <li>Access controls and authentication requirements</li>
                    <li>Secure hosting infrastructure</li>
                    <li>Employee training on data protection</li>
                </ul>
            </section>

            <!-- Cookies & Analytics -->
            <section id="cookies" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Cookies & Analytics</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We use cookies and similar technologies to enhance your experience on our platform:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li><strong>Essential Cookies:</strong> Required for basic platform functionality</li>
                    <li><strong>Analytics Cookies:</strong> Help us understand how you use our platform</li>
                    <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                    <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements (with consent)</li>
                </ul>
                <p class="text-gray-600 dark:text-gray-300">
                    You can control cookies through your browser settings, though disabling certain cookies may affect platform functionality.
                </p>
            </section>

            <!-- Meta (Facebook) Pixel -->
            <section id="meta-pixel" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">7. Meta (Facebook) Pixel</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We use Meta Pixel (formerly Facebook Pixel) to measure the effectiveness of our advertising and understand how users interact with our platform. This technology helps us improve our services and deliver more relevant advertisements.
                </p>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">What Data is Collected</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    When you interact with our platform, the Meta Pixel may collect the following types of data:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li><strong>User Actions (Events):</strong> Registration, subscription, payment completions, page views, search queries, and form submissions</li>
                    <li><strong>Device Information:</strong> Browser type, operating system, device type, and screen resolution</li>
                    <li><strong>Technical Data:</strong> IP address (used for geographic targeting), browser cookies, and pixel identifiers</li>
                    <li><strong>Page Information:</strong> URLs visited and referrer data</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Events We Track</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We use Meta Pixel to track the following standard events to optimize our advertising:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li><strong>Lead:</strong> When users begin onboarding or open application modals</li>
                    <li><strong>CompleteRegistration:</strong> When users complete account registration</li>
                    <li><strong>InitiateCheckout:</strong> When users start a payment or subscription flow</li>
                    <li><strong>Subscribe:</strong> When users successfully subscribe to a plan</li>
                    <li><strong>Purchase:</strong> When users complete one-time payments</li>
                    <li><strong>StartTrial:</strong> When users begin a free trial period</li>
                    <li><strong>ViewContent:</strong> When users view campaigns, profiles, or other content</li>
                    <li><strong>Search:</strong> When users search for campaigns or users</li>
                    <li><strong>SubmitApplication:</strong> When influencers apply to campaigns</li>
                    <li><strong>Contact:</strong> When users submit contact forms</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">How This Data is Used</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Data collected by Meta Pixel is used to:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li>Measure the effectiveness of our Facebook and Instagram advertisements</li>
                    <li>Create custom audiences for advertising purposes</li>
                    <li>Optimize ad delivery to reach users most likely to take action</li>
                    <li>Understand user behavior to improve our platform</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Opting Out</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    You can control how Meta uses your data for advertising through:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li><strong>Facebook Settings:</strong> Adjust your ad preferences at <a href="https://www.facebook.com/settings/?tab=ads" class="text-blue-600 dark:text-blue-400 hover:underline" target="_blank" rel="noopener noreferrer">facebook.com/settings/?tab=ads</a></li>
                    <li><strong>Digital Advertising Alliance:</strong> Opt out at <a href="https://optout.aboutads.info" class="text-blue-600 dark:text-blue-400 hover:underline" target="_blank" rel="noopener noreferrer">optout.aboutads.info</a></li>
                    <li><strong>Browser Settings:</strong> Block third-party cookies or use browser extensions that block tracking pixels</li>
                </ul>
                <p class="text-gray-600 dark:text-gray-300">
                    For more information about how Meta processes your data, please review <a href="https://www.facebook.com/privacy/policy/" class="text-blue-600 dark:text-blue-400 hover:underline" target="_blank" rel="noopener noreferrer">Meta's Privacy Policy</a>.
                </p>
            </section>

            <!-- Your Rights -->
            <section id="your-rights" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">8. Your Rights</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">You have the right to:</p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
                    <li><strong>Rectification:</strong> Correct inaccurate or incomplete information</li>
                    <li><strong>Erasure:</strong> Request deletion of your personal information</li>
                    <li><strong>Portability:</strong> Receive your data in a machine-readable format</li>
                    <li><strong>Restriction:</strong> Limit how we process your information</li>
                    <li><strong>Objection:</strong> Object to certain types of processing</li>
                    <li><strong>Withdraw Consent:</strong> Withdraw consent for optional data processing</li>
                </ul>
                <p class="text-gray-600 dark:text-gray-300">
                    To exercise these rights, please contact us at <a href="mailto:privacy@collabconnect.com" class="text-blue-600 dark:text-blue-400 hover:underline">privacy@collabconnect.com</a>.
                </p>
            </section>

            <!-- Children's Privacy -->
            <section id="children" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">9. Children's Privacy</h2>
                <p class="text-gray-600 dark:text-gray-300">
                    CollabConnect is not intended for use by children under 13. We do not knowingly collect personal information from children under 13. If we become aware that a child under 13 has provided us with personal information, we will delete such information immediately.
                </p>
            </section>

            <!-- International Users -->
            <section id="international" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">10. International Users</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    CollabConnect is based in the United States. If you are accessing our services from outside the US, please be aware that your information may be transferred to, stored, and processed in the US.
                </p>
                <p class="text-gray-600 dark:text-gray-300">
                    By using our services, you consent to the transfer of your information to the US and the processing of your information in accordance with this Privacy Policy.
                </p>
            </section>

            <!-- Policy Changes -->
            <section id="changes" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">11. Policy Changes</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    We may update this Privacy Policy from time to time. We will notify you of any material changes by:
                </p>
                <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 mb-4">
                    <li>Posting the updated policy on our website</li>
                    <li>Sending an email notification to registered users</li>
                    <li>Displaying a prominent notice on our platform</li>
                </ul>
                <p class="text-gray-600 dark:text-gray-300">
                    Your continued use of our services after any changes constitutes acceptance of the updated Privacy Policy.
                </p>
            </section>

            <!-- Contact -->
            <section id="contact" class="feature-card">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">12. Contact Us</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    If you have questions about this Privacy Policy or our data practices, please contact us:
                </p>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-gray-600 dark:text-gray-300">
                        <strong>Email:</strong> <a href="mailto:privacy@collabconnect.com" class="text-blue-600 dark:text-blue-400 hover:underline">privacy@collabconnect.com</a><br>
                        <strong>General Contact:</strong> <a href="mailto:hello@collabconnect.com" class="text-blue-600 dark:text-blue-400 hover:underline">hello@collabconnect.com</a><br>
                        <strong>Address:</strong> CollabConnect, Cincinnati & Dayton, Ohio<br>
                        <strong>Response Time:</strong> We will respond to privacy inquiries within 30 days
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection