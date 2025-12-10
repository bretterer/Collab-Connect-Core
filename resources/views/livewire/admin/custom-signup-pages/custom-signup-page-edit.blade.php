<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Link & Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <flux:button :href="route('admin.custom-signup-pages.index')" wire:navigate variant="ghost" icon="arrow-left">
                Back to Signup Pages
            </flux:button>

            @if($is_active)
                <a href="{{ route('signup.show', $customSignupPage->slug) }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    View Live Page
                    <flux:icon.arrow-top-right-on-square class="w-4 h-4" />
                </a>
            @endif
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $name ?: 'Edit Signup Page' }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    <span class="font-mono text-sm">/signup/{{ $slug }}</span>
                    @if($is_active)
                        <flux:badge color="green" size="sm" class="ml-2">Active</flux:badge>
                    @else
                        <flux:badge color="yellow" size="sm" class="ml-2">Draft</flux:badge>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Information Section -->
        <flux:card>
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Basic Information</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Configure the page identity and type.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Page Name -->
                <flux:field>
                    <flux:label>Internal Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g., Elite Webinar Offer" />
                    <flux:error name="name" />
                </flux:field>

                <!-- URL Slug -->
                <flux:field>
                    <flux:label>URL Slug</flux:label>
                    <flux:input wire:model="slug" placeholder="elite-webinar-offer" />
                    <flux:description class="text-xs">Public URL: /signup/{{ $slug }}</flux:description>
                    <flux:error name="slug" />
                </flux:field>

                <!-- Page Title -->
                <flux:field class="md:col-span-2">
                    <flux:label>Page Title</flux:label>
                    <flux:input wire:model="title" placeholder="Join the Elite Influencer Program" />
                    <flux:description>Shown as the main heading on the signup page.</flux:description>
                    <flux:error name="title" />
                </flux:field>

                <!-- Description -->
                <flux:field class="md:col-span-2">
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model="description" rows="3" placeholder="A brief description of this offer..." />
                    <flux:error name="description" />
                </flux:field>

                <!-- Account Type -->
                <flux:field>
                    <flux:label>Account Type</flux:label>
                    <flux:select wire:model="account_type">
                        @foreach($accountTypes as $type)
                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="account_type" />
                </flux:field>
            </div>
        </flux:card>

        <!-- Package Information Section -->
        <flux:card>
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Package Information</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Define what's included in this offer.</p>
            </div>

            <div class="space-y-6">
                <!-- Package Name -->
                <flux:field>
                    <flux:label>Package Name</flux:label>
                    <flux:input wire:model="package_name" placeholder="e.g., Elite Influencer Bundle" />
                    <flux:error name="package_name" />
                </flux:field>

                <!-- Package Benefits -->
                <div>
                    <flux:label class="mb-2">Package Benefits</flux:label>
                    <flux:description class="mb-4">List the features and benefits included in this offer.</flux:description>

                    @if(count($package_benefits) > 0)
                        <ul class="space-y-2 mb-4">
                            @foreach($package_benefits as $index => $benefit)
                                <li class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2" wire:key="benefit-{{ $index }}">
                                    <div class="flex items-center gap-2">
                                        <flux:icon.check-circle class="w-5 h-5 text-green-500" />
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $benefit }}</span>
                                    </div>
                                    <flux:button variant="ghost" size="sm" wire:click="removeBenefit({{ $index }})">
                                        <flux:icon.x-mark class="w-4 h-4 text-gray-400 hover:text-red-500" />
                                    </flux:button>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="flex gap-2">
                        <flux:input
                            wire:model="new_benefit"
                            wire:keydown.enter.prevent="addBenefit"
                            placeholder="Add a benefit..."
                            class="flex-1"
                        />
                        <flux:button type="button" wire:click="addBenefit" icon="plus">
                            Add
                        </flux:button>
                    </div>
                </div>
            </div>
        </flux:card>

        <!-- Payment Settings Section -->
        <flux:card>
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Settings</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Configure one-time payments and subscriptions.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- One-time Payment -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">One-Time Payment</h3>
                    <flux:field>
                        <flux:label>Amount (USD)</flux:label>
                        <flux:input
                            type="number"
                            step="0.01"
                            wire:model="one_time_amount"
                            placeholder="297.00"
                        />
                        <flux:description>Leave empty if no one-time payment.</flux:description>
                        <flux:error name="one_time_amount" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Stripe Price ID</flux:label>
                        <flux:input
                            wire:model="one_time_stripe_price_id"
                            placeholder="price_1234567890"
                        />
                        <flux:description>The Stripe price ID for this one-time charge.</flux:description>
                        <flux:error name="one_time_stripe_price_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Payment Description</flux:label>
                        <flux:input
                            wire:model="one_time_description"
                            placeholder="One-time setup fee"
                        />
                        <flux:error name="one_time_description" />
                    </flux:field>
                </div>

                <!-- Subscription -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Subscription</h3>
                    <flux:field>
                        <flux:label>Stripe Price ID</flux:label>
                        <flux:input
                            wire:model="subscription_stripe_price_id"
                            placeholder="price_1234567890"
                        />
                        <flux:description>The Stripe subscription price ID.</flux:description>
                        <flux:error name="subscription_stripe_price_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Trial Period (Days)</flux:label>
                        <flux:input
                            type="number"
                            wire:model="subscription_trial_days"
                            placeholder="90"
                        />
                        <flux:description>Number of free trial days for the subscription.</flux:description>
                        <flux:error name="subscription_trial_days" />
                    </flux:field>
                </div>
            </div>
        </flux:card>

        <!-- Webhook Settings Section -->
        <flux:card>
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Webhook Settings</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Send registration data to external systems.</p>
            </div>

            <flux:field>
                <flux:label>Webhook URL</flux:label>
                <flux:input
                    type="url"
                    wire:model="webhook_url"
                    placeholder="https://your-crm.com/api/webhook"
                />
                <flux:description>After successful registration, we'll POST user data to this URL.</flux:description>
                <flux:error name="webhook_url" />
            </flux:field>
        </flux:card>

        <!-- Custom Content Section -->
        <flux:card>
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Custom Content</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Customize the look and feel of the signup page.</p>
            </div>

            <div class="space-y-6">
                <!-- Hero Headline -->
                <flux:field>
                    <flux:label>Hero Headline</flux:label>
                    <flux:input
                        wire:model="hero_headline"
                        placeholder="Exclusive Offer for Webinar Attendees"
                    />
                    <flux:error name="hero_headline" />
                </flux:field>

                <!-- Hero Subheadline -->
                <flux:field>
                    <flux:label>Hero Subheadline</flux:label>
                    <flux:textarea
                        wire:model="hero_subheadline"
                        rows="2"
                        placeholder="Join our elite program and unlock..."
                    />
                    <flux:error name="hero_subheadline" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- CTA Button Text -->
                    <flux:field>
                        <flux:label>CTA Button Text</flux:label>
                        <flux:input
                            wire:model="cta_button_text"
                            placeholder="Get Started Now"
                        />
                        <flux:error name="cta_button_text" />
                    </flux:field>

                    <!-- Hero Image URL -->
                    <flux:field>
                        <flux:label>Hero Image URL</flux:label>
                        <flux:input
                            wire:model="hero_image_url"
                            placeholder="https://example.com/hero-image.jpg"
                        />
                        <flux:error name="hero_image_url" />
                    </flux:field>
                </div>
            </div>
        </flux:card>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <div>
                @if($is_active)
                    <flux:button type="button" wire:click="unpublish" variant="ghost">
                        Unpublish
                    </flux:button>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button :href="route('admin.custom-signup-pages.index')" wire:navigate variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="outline">
                    Save Draft
                </flux:button>
                <flux:button type="button" wire:click="save(true)" variant="primary">
                    {{ $is_active ? 'Save & Keep Published' : 'Save & Publish' }}
                </flux:button>
            </div>
        </div>
    </form>
</div>
