<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CustomSignupPage extends Model
{
    /** @use HasFactory<\Database\Factories\CustomSignupPageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'description',
        'account_type',
        'is_active',
        'settings',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
            'published_at' => 'datetime',
            'account_type' => AccountType::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isPublished(): bool
    {
        return $this->is_active && $this->published_at?->isPast();
    }

    public function publish(): void
    {
        $this->update([
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'is_active' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Get a specific setting value from the settings JSON.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Get the one-time payment price in cents.
     */
    public function getOneTimePaymentAmount(): ?int
    {
        return $this->getSetting('one_time_payment.amount');
    }

    /**
     * Get the one-time payment Stripe price ID.
     */
    public function getOneTimePaymentPriceId(): ?string
    {
        return $this->getSetting('one_time_payment.stripe_price_id');
    }

    /**
     * Get the subscription Stripe price ID.
     */
    public function getSubscriptionPriceId(): ?string
    {
        return $this->getSetting('subscription.stripe_price_id');
    }

    /**
     * Get the trial period in days.
     */
    public function getTrialDays(): ?int
    {
        return $this->getSetting('subscription.trial_days');
    }

    /**
     * Get the webhook URL for post-registration.
     */
    public function getWebhookUrl(): ?string
    {
        return $this->getSetting('webhook.url');
    }

    /**
     * Check if the page requires a one-time payment.
     */
    public function hasOneTimePayment(): bool
    {
        return ! empty($this->getOneTimePaymentPriceId());
    }

    /**
     * Check if the page sets up a subscription.
     */
    public function hasSubscription(): bool
    {
        return ! empty($this->getSubscriptionPriceId());
    }

    /**
     * Get the package benefits/features to display.
     *
     * @return array<string>
     */
    public function getPackageBenefits(): array
    {
        return $this->getSetting('package.benefits', []);
    }

    /**
     * Get the package name.
     */
    public function getPackageName(): ?string
    {
        return $this->getSetting('package.name');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->name);
            }
        });
    }
}
