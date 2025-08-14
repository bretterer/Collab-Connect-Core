<!-- Step 4: Plan Selection & Setup -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">4</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Platform Preferences & Setup
        </flux:heading>
    </div>

    <!-- Preferred Social Media Platforms -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Which social media platforms are you most interested in?
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            Select the platforms where you'd like to connect with influencers.
        </flux:description>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="relative">
                <flux:checkbox wire:model="platforms" value="instagram" class="peer sr-only">
                    Instagram
                </flux:checkbox>
                <label class="flex items-center justify-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center">
                        <div class="text-2xl mb-2">📸</div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Instagram</div>
                    </div>
                </label>
            </div>

            <div class="relative">
                <flux:checkbox wire:model="platforms" value="tiktok" class="peer sr-only">
                    TikTok
                </flux:checkbox>
                <label class="flex items-center justify-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center">
                        <div class="text-2xl mb-2">🎵</div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">TikTok</div>
                    </div>
                </label>
            </div>

            <div class="relative">
                <flux:checkbox wire:model="platforms" value="youtube" class="peer sr-only">
                    YouTube
                </flux:checkbox>
                <label class="flex items-center justify-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center">
                        <div class="text-2xl mb-2">📺</div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">YouTube</div>
                    </div>
                </label>
            </div>

            <div class="relative">
                <flux:checkbox wire:model="platforms" value="facebook" class="peer sr-only">
                    Facebook
                </flux:checkbox>
                <label class="flex items-center justify-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center">
                        <div class="text-2xl mb-2">👥</div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Facebook</div>
                    </div>
                </label>
            </div>

            <div class="relative">
                <flux:checkbox wire:model="platforms" value="twitter" class="peer sr-only">
                    Twitter/X
                </flux:checkbox>
                <label class="flex items-center justify-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center">
                        <div class="text-2xl mb-2">🐦</div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Twitter/X</div>
                    </div>
                </label>
            </div>

            <div class="relative">
                <flux:checkbox wire:model="platforms" value="linkedin" class="peer sr-only">
                    LinkedIn
                </flux:checkbox>
                <label class="flex items-center justify-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center">
                        <div class="text-2xl mb-2">💼</div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">LinkedIn</div>
                    </div>
                </label>
            </div>
        </div>
        <flux:error name="platforms" />
    </div>

    <!-- Plan Selection -->
    <div class="space-y-6">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Choose your plan
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            Select the plan that best fits your business needs. You can upgrade or downgrade anytime.
        </flux:description>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Starter Plan -->
            <div class="relative">
                <flux:radio wire:model="selectedPlan" value="starter" class="peer sr-only">
                    Starter Plan
                </flux:radio>
                <label class="block p-6 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-gray-300 dark:hover:border-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center space-y-4">
                        <flux:badge color="green">Most Popular</flux:badge>
                        <div>
                            <div class="text-xl font-bold text-gray-800 dark:text-gray-200">Starter</div>
                            <div class="text-3xl font-bold text-blue-600">$29</div>
                            <div class="text-sm text-gray-500">/month</div>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div>✓ Up to 3 active campaigns</div>
                            <div>✓ 50 influencer contacts/month</div>
                            <div>✓ Basic analytics</div>
                            <div>✓ Email support</div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Growth Plan -->
            <div class="relative">
                <flux:radio wire:model="selectedPlan" value="growth" class="peer sr-only">
                    Growth Plan
                </flux:radio>
                <label class="block p-6 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-gray-300 dark:hover:border-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center space-y-4">
                        <flux:badge color="blue">Recommended</flux:badge>
                        <div>
                            <div class="text-xl font-bold text-gray-800 dark:text-gray-200">Growth</div>
                            <div class="text-3xl font-bold text-blue-600">$79</div>
                            <div class="text-sm text-gray-500">/month</div>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div>✓ Up to 10 active campaigns</div>
                            <div>✓ 200 influencer contacts/month</div>
                            <div>✓ Advanced analytics</div>
                            <div>✓ Priority support</div>
                            <div>✓ Custom campaign templates</div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Scale Plan -->
            <div class="relative">
                <flux:radio wire:model="selectedPlan" value="scale" class="peer sr-only">
                    Scale Plan
                </flux:radio>
                <label class="block p-6 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-gray-300 dark:hover:border-gray-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                    <div class="text-center space-y-4">
                        <flux:badge color="purple">Enterprise</flux:badge>
                        <div>
                            <div class="text-xl font-bold text-gray-800 dark:text-gray-200">Scale</div>
                            <div class="text-3xl font-bold text-blue-600">$199</div>
                            <div class="text-sm text-gray-500">/month</div>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div>✓ Unlimited campaigns</div>
                            <div>✓ Unlimited contacts</div>
                            <div>✓ Premium analytics & reports</div>
                            <div>✓ Dedicated account manager</div>
                            <div>✓ API access</div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
        <flux:error name="selectedPlan" />
    </div>

    <!-- Notification Preferences -->
    <div class="space-y-4">
        <flux:heading class="text-gray-800 dark:text-gray-200">
            Communication preferences
        </flux:heading>
        <flux:description class="text-gray-600 dark:text-gray-400">
            Choose how you'd like to hear from us.
        </flux:description>

        <div class="space-y-4">
            <flux:field>
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div>
                        <flux:label class="font-medium">Influencer Opportunities</flux:label>
                        <flux:description class="text-sm text-gray-600 dark:text-gray-400">
                            Get notified when new influencers match your criteria
                        </flux:description>
                    </div>
                    <flux:switch wire:model="emailNotifications" />
                </div>
            </flux:field>

            <flux:field>
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div>
                        <flux:label class="font-medium">Marketing Tips & Updates</flux:label>
                        <flux:description class="text-sm text-gray-600 dark:text-gray-400">
                            Receive tips and best practices for influencer marketing
                        </flux:description>
                    </div>
                    <flux:switch wire:model="marketingEmails" />
                </div>
            </flux:field>
        </div>
    </div>
</div>