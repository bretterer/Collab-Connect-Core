<?php

namespace App\Livewire\Admin\CustomSignupPages;

use App\Enums\AccountType;
use App\Models\CustomSignupPage;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CustomSignupPageCreate extends Component
{
    public string $name = '';

    public string $slug = '';

    public int $account_type = AccountType::INFLUENCER->value;

    public bool $slugManuallyEdited = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:custom_signup_pages,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'account_type' => ['required', 'integer', 'in:'.AccountType::INFLUENCER->value.','.AccountType::BUSINESS->value],
        ];
    }

    public function updatedName(string $value): void
    {
        if (! $this->slugManuallyEdited) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugManuallyEdited = true;
    }

    public function create(): mixed
    {
        $this->validate();

        $page = CustomSignupPage::create([
            'name' => $this->name,
            'title' => $this->name,
            'slug' => $this->slug,
            'account_type' => $this->account_type,
            'is_active' => false,
            'settings' => $this->getDefaultSettings(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(text: 'Custom signup page created as draft', variant: 'success');

        return redirect()->route('admin.custom-signup-pages.edit', $page);
    }

    protected function getDefaultSettings(): array
    {
        return [
            'package' => [
                'name' => '',
                'benefits' => [],
            ],
            'one_time_payment' => [
                'amount' => null,
                'stripe_price_id' => null,
                'description' => null,
            ],
            'subscription' => [
                'stripe_price_id' => null,
                'trial_days' => 14,
            ],
            'webhook' => [
                'url' => null,
                'headers' => [],
            ],
            'content' => [
                'hero_headline' => null,
                'hero_subheadline' => null,
                'cta_button_text' => null,
                'hero_image_url' => null,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.admin.custom-signup-pages.custom-signup-page-create', [
            'accountTypes' => [
                ['value' => AccountType::INFLUENCER->value, 'label' => AccountType::INFLUENCER->label()],
                ['value' => AccountType::BUSINESS->value, 'label' => AccountType::BUSINESS->label()],
            ],
        ]);
    }
}
