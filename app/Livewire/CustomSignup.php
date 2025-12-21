<?php

namespace App\Livewire;

use App\Enums\AccountType;
use App\Models\Business;
use App\Models\CustomSignupPage;
use App\Models\Influencer;
use App\Models\MarketWaitlist;
use App\Models\MarketZipcode;
use App\Models\PostalCode;
use App\Models\User;
use App\Settings\RegistrationMarkets;
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

#[Layout('layouts.custom-signup')]
class CustomSignup extends Component
{
    use UsesSpamProtection;

    public CustomSignupPage $signupPage;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $postal_code = '';

    public HoneypotData $extraFields;

    public bool $registrationMarketsEnabled = true;

    public bool $marketNotOpen = false;

    public bool $registrationComplete = false;

    public bool $isProcessing = false;

    public bool $requiresPayment = false;

    public ?string $paymentError = null;

    public function mount(string $slug): void
    {
        $this->signupPage = CustomSignupPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->extraFields = new HoneypotData;
        $settings = app(RegistrationMarkets::class);
        $this->registrationMarketsEnabled = $settings->enabled;

        // Check if payment is required
        $this->requiresPayment = $this->signupPage->hasOneTimePayment();

        // Track Lead event when user lands on custom signup page
        MetaPixel::track('Lead', [
            'content_category' => 'custom_signup',
            'content_name' => $this->signupPage->slug,
        ]);
    }

    public function register(): void
    {
        // If payment is required, just validate first then trigger Stripe
        if ($this->requiresPayment) {
            $this->validateRegistration();
            $this->isProcessing = true;

            // Track InitiateCheckout when starting payment flow
            MetaPixel::track('InitiateCheckout', [
                'content_name' => $this->signupPage->name,
                'value' => $this->signupPage->getOneTimePaymentAmount() / 100,
                'currency' => 'USD',
            ]);

            $this->dispatch('createStripePaymentMethod');

            return;
        }

        // No payment required, proceed with registration
        $this->processRegistration();
    }

    protected function validateRegistration(): void
    {
        $this->protectAgainstSpam();

        $this->email = strtolower($this->email);
        $this->postal_code = trim($this->postal_code);

        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];

        if ($this->registrationMarketsEnabled) {
            $validationRules['postal_code'] = ['required', 'string', 'max:10', function ($attribute, $value, $fail) {
                $postalCode = PostalCode::where('postal_code', $value)
                    ->where('country_code', 'US')
                    ->first();

                if (! $postalCode) {
                    $fail('The postal code you entered is not valid.');
                }
            }];
        }

        $this->validate($validationRules);
    }

    #[On('stripePaymentMethodCreated')]
    public function handleStripePaymentMethod(string $paymentMethodId): void
    {
        try {
            $this->processRegistration($paymentMethodId);
        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->paymentError = $e->getMessage();
            $this->dispatch('stripePaymentMethodError', message: $e->getMessage());
        }
    }

    #[On('stripePaymentMethodError')]
    public function handleStripeError(string $message): void
    {
        $this->isProcessing = false;
        $this->paymentError = $message;
    }

    protected function processRegistration(?string $paymentMethodId = null): void
    {
        if (! $this->requiresPayment) {
            $this->validateRegistration();
        }

        // Check if postal code is in an active market
        $isMarketApproved = true;
        if ($this->registrationMarketsEnabled && ! empty($this->postal_code)) {
            $isMarketApproved = MarketZipcode::isInActiveMarket($this->postal_code);
        }

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'account_type' => $this->signupPage->account_type,
                'postal_code' => $this->postal_code,
                'market_approved' => $isMarketApproved,
            ]);

            // Create profile based on account type
            $billableModel = $this->createProfile($user);

            // Process one-time payment if required (always charge, regardless of market status)
            if ($this->requiresPayment && $paymentMethodId) {
                $this->processPayment($user, $billableModel, $paymentMethodId, $isMarketApproved);
            }

            if ($isMarketApproved) {
                // Market is open - create subscription immediately with trial
                if ($this->signupPage->hasSubscription()) {
                    $this->createSubscription($user, $billableModel, $paymentMethodId);
                }

                // Fire registered event for approved users
                event(new Registered($user));
            } else {
                // Market is not open - add to waitlist with subscription details for later activation
                if ($this->registrationMarketsEnabled) {
                    MarketWaitlist::create([
                        'user_id' => $user->id,
                        'postal_code' => $this->postal_code,
                        'custom_signup_page_id' => $this->signupPage->id,
                        'subscription_stripe_price_id' => $this->signupPage->getSubscriptionPriceId(),
                        'intended_trial_days' => $this->signupPage->getTrialDays(),
                    ]);
                    $this->marketNotOpen = true;
                }
            }

            // Send webhook notification if configured
            $this->sendWebhookNotification($user);

            DB::commit();

            Auth::login($user);

            $this->isProcessing = false;
            $this->registrationComplete = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->isProcessing = false;
            throw $e;
        }
    }

    protected function createProfile(User $user): Business|Influencer
    {
        if ($this->signupPage->account_type === AccountType::BUSINESS) {
            $business = Business::create([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            // Attach user to business
            $user->businesses()->attach($business->id);
            $user->update(['current_business' => $business->id]);

            return $business;
        }

        // Default to influencer
        return Influencer::create([
            'user_id' => $user->id,
        ]);
    }

    protected function processPayment(User $user, Business|Influencer $billableModel, string $paymentMethodId, bool $isMarketApproved): void
    {
        $oneTimePriceId = $this->signupPage->getOneTimePaymentPriceId();

        if (! $oneTimePriceId) {
            return;
        }

        // Create Stripe customer if not already
        if (! $billableModel->hasStripeId()) {
            $billableModel->createAsStripeCustomer([
                'email' => $billableModel->email ?? $user->email,
                'name' => $user->name,
                'metadata' => [
                    'signup_page_id' => $this->signupPage->id,
                    'signup_page_slug' => $this->signupPage->slug,
                ],
            ]);
        }

        // Add the payment method to the customer
        $billableModel->addPaymentMethod($paymentMethodId);
        $billableModel->updateDefaultPaymentMethod($paymentMethodId);

        // Charge the one-time payment using the payment method directly
        $billableModel->charge(
            $this->signupPage->getOneTimePaymentAmount(),
            $paymentMethodId,
            [
                'description' => $this->signupPage->getSetting('one_time_payment.description', 'Custom signup page payment'),
                'metadata' => [
                    'signup_page_id' => $this->signupPage->id,
                    'signup_page_slug' => $this->signupPage->slug,
                    'user_id' => $user->id,
                    'market_approved' => $isMarketApproved ? 'yes' : 'no',
                ],
                'confirm' => true,
                'payment_method_types' => ['card'],
            ]
        );

        // Track Purchase event via server-side Conversions API for reliability
        $customData = (new CustomData)
            ->setCurrency('USD')
            ->setValue($this->signupPage->getOneTimePaymentAmount() / 100)
            ->setContentName($this->signupPage->name);

        MetaPixel::send('Purchase', uniqid('purchase_', true), $customData);
    }

    protected function createSubscription(User $user, Business|Influencer $billableModel, ?string $paymentMethodId): void
    {
        $subscriptionPriceId = $this->signupPage->getSubscriptionPriceId();

        if (! $subscriptionPriceId) {
            return;
        }

        // If no payment method was provided (free trial only), we can't create a subscription
        if (! $paymentMethodId) {
            return;
        }

        // Create Stripe customer if not already (in case no one-time payment was processed)
        if (! $billableModel->hasStripeId()) {
            $billableModel->createAsStripeCustomer([
                'email' => $billableModel->email ?? $user->email,
                'name' => $user->name,
                'metadata' => [
                    'signup_page_id' => $this->signupPage->id,
                    'signup_page_slug' => $this->signupPage->slug,
                ],
            ]);
        }

        $trialDays = $this->signupPage->getTrialDays() ?? 14;

        $billableModel->newSubscription('default', $subscriptionPriceId)
            ->trialDays($trialDays)
            ->create($paymentMethodId, [
                'email' => $billableModel->email ?? $user->email,
                'name' => $user->name,
                'metadata' => [
                    'signup_page_id' => $this->signupPage->id,
                    'signup_page_slug' => $this->signupPage->slug,
                ],
            ]);

        // Track StartTrial event for subscription with trial
        if ($trialDays > 0) {
            MetaPixel::track('StartTrial', [
                'value' => 0,
                'currency' => 'USD',
                'predicted_ltv' => 0,
            ]);
        }
    }

    protected function sendWebhookNotification(User $user): void
    {
        $webhookUrl = $this->signupPage->getWebhookUrl();

        if (! $webhookUrl) {
            return;
        }

        try {
            Http::timeout(10)->post($webhookUrl, [
                'event' => 'user.registered',
                'signup_page' => [
                    'id' => $this->signupPage->id,
                    'name' => $this->signupPage->name,
                    'slug' => $this->signupPage->slug,
                ],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'account_type' => $this->signupPage->account_type->value,
                    'postal_code' => $this->postal_code,
                    'market_approved' => $user->market_approved,
                ],
                'payment' => [
                    'one_time_amount' => $this->signupPage->getOneTimePaymentAmount(),
                    'has_subscription' => $this->signupPage->hasSubscription(),
                    'trial_days' => $this->signupPage->getTrialDays(),
                ],
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            // Log webhook failure but don't fail the registration
            logger()->warning('Custom signup webhook failed', [
                'signup_page_id' => $this->signupPage->id,
                'webhook_url' => $webhookUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.custom-signup', [
            'page' => $this->signupPage,
            'packageName' => $this->signupPage->getPackageName(),
            'packageBenefits' => $this->signupPage->getPackageBenefits(),
            'oneTimeAmount' => $this->signupPage->getOneTimePaymentAmount(),
            'oneTimeDescription' => $this->signupPage->getSetting('one_time_payment.description'),
            'trialDays' => $this->signupPage->getTrialDays(),
            'heroHeadline' => $this->signupPage->getSetting('content.hero_headline'),
            'heroSubheadline' => $this->signupPage->getSetting('content.hero_subheadline'),
            'ctaButtonText' => $this->signupPage->getSetting('content.cta_button_text', 'Get Started'),
            'heroImageUrl' => $this->signupPage->getSetting('content.hero_image_url'),
        ]);
    }
}
