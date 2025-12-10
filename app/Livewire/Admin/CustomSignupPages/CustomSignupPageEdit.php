<?php

namespace App\Livewire\Admin\CustomSignupPages;

use App\Enums\AccountType;
use App\Models\CustomSignupPage;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CustomSignupPageEdit extends Component
{
    public CustomSignupPage $customSignupPage;

    // Basic Info
    public string $name = '';

    public string $slug = '';

    public string $title = '';

    public ?string $description = '';

    public int $account_type = AccountType::INFLUENCER->value;

    public bool $is_active = false;

    // Package Settings
    public ?string $package_name = '';

    /** @var array<string> */
    public array $package_benefits = [];

    public string $new_benefit = '';

    // One-time Payment Settings
    public ?float $one_time_amount = null;

    public ?string $one_time_stripe_price_id = '';

    public ?string $one_time_description = '';

    // Subscription Settings
    public ?string $subscription_stripe_price_id = '';

    public ?int $subscription_trial_days = null;

    // Webhook Settings
    public ?string $webhook_url = '';

    // Custom Content
    public ?string $hero_headline = '';

    public ?string $hero_subheadline = '';

    public ?string $cta_button_text = '';

    public ?string $hero_image_url = '';

    public function mount(CustomSignupPage $customSignupPage): void
    {
        $this->customSignupPage = $customSignupPage;
        $this->loadFromModel();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:custom_signup_pages,slug,'.$this->customSignupPage->id, 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'account_type' => ['required', 'integer', 'in:'.AccountType::INFLUENCER->value.','.AccountType::BUSINESS->value],
            'package_name' => ['nullable', 'string', 'max:255'],
            'one_time_amount' => ['nullable', 'numeric', 'min:0'],
            'one_time_stripe_price_id' => ['nullable', 'string', 'max:255'],
            'one_time_description' => ['nullable', 'string', 'max:500'],
            'subscription_stripe_price_id' => ['nullable', 'string', 'max:255'],
            'subscription_trial_days' => ['nullable', 'integer', 'min:0'],
            'webhook_url' => ['nullable', 'url', 'max:500'],
            'hero_headline' => ['nullable', 'string', 'max:2000'],
            'hero_subheadline' => ['nullable', 'string', 'max:5000'],
            'cta_button_text' => ['nullable', 'string', 'max:255'],
            'hero_image_url' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function loadFromModel(): void
    {
        $this->name = $this->customSignupPage->name;
        $this->slug = $this->customSignupPage->slug;
        $this->title = $this->customSignupPage->title;
        $this->description = $this->customSignupPage->description ?? '';
        $this->account_type = $this->customSignupPage->account_type->value;
        $this->is_active = $this->customSignupPage->is_active;

        // Package Settings
        $this->package_name = $this->customSignupPage->getSetting('package.name', '');
        $this->package_benefits = $this->customSignupPage->getSetting('package.benefits', []);

        // One-time Payment
        $amount = $this->customSignupPage->getSetting('one_time_payment.amount');
        $this->one_time_amount = $amount ? $amount / 100 : null;
        $this->one_time_stripe_price_id = $this->customSignupPage->getSetting('one_time_payment.stripe_price_id', '');
        $this->one_time_description = $this->customSignupPage->getSetting('one_time_payment.description', '');

        // Subscription
        $this->subscription_stripe_price_id = $this->customSignupPage->getSetting('subscription.stripe_price_id', '');
        $this->subscription_trial_days = $this->customSignupPage->getSetting('subscription.trial_days');

        // Webhook
        $this->webhook_url = $this->customSignupPage->getSetting('webhook.url', '');

        // Custom Content
        $this->hero_headline = $this->customSignupPage->getSetting('content.hero_headline', '');
        $this->hero_subheadline = $this->customSignupPage->getSetting('content.hero_subheadline', '');
        $this->cta_button_text = $this->customSignupPage->getSetting('content.cta_button_text', '');
        $this->hero_image_url = $this->customSignupPage->getSetting('content.hero_image_url', '');
    }

    public function addBenefit(): void
    {
        if (! empty(trim($this->new_benefit))) {
            $this->package_benefits[] = trim($this->new_benefit);
            $this->new_benefit = '';
        }
    }

    public function removeBenefit(int $index): void
    {
        unset($this->package_benefits[$index]);
        $this->package_benefits = array_values($this->package_benefits);
    }

    protected function buildSettings(): array
    {
        return [
            'package' => [
                'name' => $this->package_name ?: null,
                'benefits' => $this->package_benefits,
            ],
            'one_time_payment' => [
                'amount' => $this->one_time_amount ? (int) ($this->one_time_amount * 100) : null,
                'stripe_price_id' => $this->one_time_stripe_price_id ?: null,
                'description' => $this->one_time_description ?: null,
            ],
            'subscription' => [
                'stripe_price_id' => $this->subscription_stripe_price_id ?: null,
                'trial_days' => $this->subscription_trial_days,
            ],
            'webhook' => [
                'url' => $this->webhook_url ?: null,
                'headers' => [],
            ],
            'content' => [
                'hero_headline' => $this->hero_headline ?: null,
                'hero_subheadline' => $this->hero_subheadline ?: null,
                'cta_button_text' => $this->cta_button_text ?: null,
                'hero_image_url' => $this->hero_image_url ?: null,
            ],
        ];
    }

    public function save(bool $publish = false): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'account_type' => $this->account_type,
            'is_active' => $publish ? true : $this->is_active,
            'settings' => $this->buildSettings(),
            'updated_by' => auth()->id(),
        ];

        if ($publish && ! $this->customSignupPage->isPublished()) {
            $data['published_at'] = now();
        }

        $this->customSignupPage->update($data);
        $this->is_active = $data['is_active'];

        $message = $publish ? 'Page published successfully' : 'Page saved successfully';
        Flux::toast(text: $message, variant: 'success');
    }

    public function unpublish(): void
    {
        $this->customSignupPage->update([
            'is_active' => false,
            'published_at' => null,
            'updated_by' => auth()->id(),
        ]);

        $this->is_active = false;

        Flux::toast(text: 'Page unpublished', variant: 'success');
    }

    public function render()
    {
        return view('livewire.admin.custom-signup-pages.custom-signup-page-edit', [
            'accountTypes' => [
                ['value' => AccountType::INFLUENCER->value, 'label' => AccountType::INFLUENCER->label()],
                ['value' => AccountType::BUSINESS->value, 'label' => AccountType::BUSINESS->label()],
            ],
        ]);
    }
}
