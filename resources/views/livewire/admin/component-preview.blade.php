<div class="space-y-12">
    <div>
        <flux:heading size="xl" class="mb-2">Component Preview</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400">
            Visual preview of the Coming Soon and Feature Gate components.
        </flux:text>
    </div>

    {{-- Coming Soon - Basic --}}
    <section>
        <flux:heading class="mb-4">Coming Soon - Basic</flux:heading>
        <livewire:components.coming-soon />
    </section>

    {{-- Coming Soon - With Features --}}
    <section>
        <flux:heading class="mb-4">Coming Soon - With Features & Expected Date</flux:heading>
        <livewire:components.coming-soon
            title="Advanced Analytics"
            description="Get deeper insights into your campaign performance with our upcoming analytics dashboard."
            :features="[
                'Real-time performance tracking',
                'Engagement rate analysis',
                'Audience demographics',
                'ROI calculations',
                'Export reports',
                'Competitor benchmarking'
            ]"
            icon="chart-bar"
            expected-date="Q1 2025"
            :show-notify-button="true"
        />
    </section>

    {{-- Coming Soon - Different Icon --}}
    <section>
        <flux:heading class="mb-4">Coming Soon - Custom Icon & Messaging</flux:heading>
        <livewire:components.coming-soon
            title="AI-Powered Matching"
            description="Our intelligent matching algorithm will help you find the perfect influencer partnerships automatically."
            :features="[
                'Smart influencer recommendations',
                'Automatic campaign matching',
                'Performance predictions',
                'Brand safety scoring'
            ]"
            icon="sparkles"
            expected-date="Coming 2025"
            :show-notify-button="true"
        />
    </section>

    {{-- Subscription Prompt --}}
    <section>
        <flux:heading class="mb-4">Subscription Prompt (For Reference)</flux:heading>
        <livewire:components.subscription-prompt
            variant="purple"
            heading="Upgrade to Pro"
            description="Unlock advanced features and take your campaigns to the next level."
            :features="[
                'Unlimited campaigns',
                'Priority support',
                'Advanced analytics',
                'Custom branding'
            ]"
            button-text="View Plans"
        />
    </section>

    {{-- Feature Gate - Forced Coming Soon State --}}
    <section>
        <flux:heading class="mb-4">Feature Gate - Coming Soon (Forced)</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-4">
            This shows the Coming Soon state via the FeatureGate component.
        </flux:text>
        <livewire:components.feature-gate
            force-state="coming_soon"
            title="Team Collaboration"
            description="Work together with your team on campaigns with real-time collaboration tools."
            :features="[
                'Shared workspaces',
                'Role-based permissions',
                'Activity tracking',
                'Comments & mentions'
            ]"
            icon="user-group"
            expected-date="Q2 2025"
            :show-notify-button="true"
        />
    </section>

    {{-- Feature Gate - Forced Subscription Required State --}}
    <section>
        <flux:heading class="mb-4">Feature Gate - Subscription Required (Forced)</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-4">
            This shows the Subscription Required state via the FeatureGate component.
        </flux:text>
        <livewire:components.feature-gate
            force-state="subscription_required"
            subscription-heading="Pro Feature"
            subscription-description="This advanced feature is available on our Pro and Enterprise plans."
            :subscription-features="[
                'Detailed analytics',
                'Priority matching',
                'Dedicated support',
                'API access'
            ]"
            subscription-variant="purple"
            subscription-button-text="Upgrade Now"
        />
    </section>

    {{-- Feature Gate - Forced Accessible State --}}
    <section>
        <flux:heading class="mb-4">Feature Gate - Accessible (Forced)</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-4">
            When accessible, the FeatureGate renders its slot content.
        </flux:text>
        <livewire:components.feature-gate force-state="accessible">
            <flux:card>
                <flux:heading class="mb-2">Premium Feature Unlocked!</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400">
                    This is the actual content that shows when the user has access to the feature.
                    You would put your real component content here.
                </flux:text>
            </flux:card>
        </livewire:components.feature-gate>
    </section>
</div>
