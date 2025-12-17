<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <flux:heading level="1" size="xl">System Settings</flux:heading>
        <flux:text class="mt-2">
            Configure platform settings, subscription options, and administrative controls.
        </flux:text>
    </div>

    <!-- Tabs -->
    <flux:tab.group wire:model.live="activeTab">
        <flux:tabs class="mb-6">
            <flux:tab name="subscription">Subscription</flux:tab>
            <flux:tab name="registration">Registration</flux:tab>
            <flux:tab name="promotions">Promotions</flux:tab>
        </flux:tabs>

        <!-- Subscription Settings Tab -->
        <flux:tab.panel name="subscription">
            <flux:card>
                <form wire:submit="saveSubscriptionSettings">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Subscription Settings</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                                Configure trial periods and subscription-related options.
                            </flux:text>
                        </div>

                        <flux:separator />

                        <!-- Trial Period Days -->
                        <flux:field>
                            <flux:label>Trial Period (Days)</flux:label>
                            <flux:input
                                wire:model="trialPeriodDays"
                                type="number"
                                min="0"
                                max="365"
                            />
                            <flux:error name="trialPeriodDays" />
                            <flux:description>
                                The number of days new subscribers get as a free trial before being charged.
                                Set to 0 to disable the trial period.
                            </flux:description>
                        </flux:field>

                        <flux:callout variant="info" icon="information-circle">
                            <flux:callout.heading>How trials work</flux:callout.heading>
                            <flux:callout.text>
                                When a user subscribes during onboarding, their payment method will be saved but they
                                won't be charged until the trial period ends. They can cancel anytime during the trial
                                without being charged.
                            </flux:callout.text>
                        </flux:callout>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                        <flux:button type="submit" variant="primary">
                            Save Subscription Settings
                        </flux:button>
                    </div>
                </form>
            </flux:card>
        </flux:tab.panel>

        <!-- Registration Settings Tab -->
        <flux:tab.panel name="registration">
            <flux:card>
                <form wire:submit="saveRegistrationSettings">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Registration Settings</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                                Configure user registration and market restrictions.
                            </flux:text>
                        </div>

                        <flux:separator />

                        <!-- Market Restrictions -->
                        <div class="flex items-start justify-between">
                            <div class="flex-1 mr-4">
                                <flux:label>Enable Market Restrictions</flux:label>
                                <flux:description class="mt-1">
                                    When enabled, users must be in an approved market (based on their zip code)
                                    to access the platform. Users outside approved markets will be placed on a waitlist.
                                </flux:description>
                            </div>
                            <flux:switch wire:model="marketsEnabled" />
                        </div>

                        @if($marketsEnabled)
                            <flux:callout variant="warning" icon="exclamation-triangle">
                                <flux:callout.heading>Market restrictions are enabled</flux:callout.heading>
                                <flux:callout.text>
                                    Users registering from zip codes outside of approved markets will be placed on a waitlist.
                                    <a href="{{ route('admin.markets.index') }}" class="underline font-medium" wire:navigate>
                                        Manage markets
                                    </a>
                                </flux:callout.text>
                            </flux:callout>
                        @else
                            <flux:callout variant="info" icon="information-circle">
                                <flux:callout.heading>Market restrictions are disabled</flux:callout.heading>
                                <flux:callout.text>
                                    All users can register and access the platform regardless of their location.
                                </flux:callout.text>
                            </flux:callout>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                        <flux:button type="submit" variant="primary">
                            Save Registration Settings
                        </flux:button>
                    </div>
                </form>
            </flux:card>
        </flux:tab.panel>

        <!-- Promotions Settings Tab -->
        <flux:tab.panel name="promotions">
            <flux:card>
                <form wire:submit="savePromotionSettings">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Promotion Settings</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                                Configure profile promotion options and credit settings.
                            </flux:text>
                        </div>

                        <flux:separator />

                        <!-- Profile Promotion Days -->
                        <flux:field>
                            <flux:label>Days Per Promotion Credit</flux:label>
                            <flux:input
                                wire:model="profilePromotionDays"
                                type="number"
                                min="1"
                                max="365"
                            />
                            <flux:error name="profilePromotionDays" />
                            <flux:description>
                                The number of days a profile stays promoted when a user uses one promotion credit.
                            </flux:description>
                        </flux:field>

                        <flux:callout variant="info" icon="information-circle">
                            <flux:callout.heading>How profile promotions work</flux:callout.heading>
                            <flux:callout.text>
                                Users can purchase promotion credits and use them to boost their profile visibility
                                in search results. Each credit promotes their profile for the configured number of days.
                                Promoted profiles appear first in search results.
                            </flux:callout.text>
                        </flux:callout>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                        <flux:button type="submit" variant="primary">
                            Save Promotion Settings
                        </flux:button>
                    </div>
                </form>
            </flux:card>
        </flux:tab.panel>
    </flux:tab.group>
</div>
