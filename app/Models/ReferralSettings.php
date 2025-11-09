<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSettings extends Model
{
    protected $fillable = [
        'default_percentage',
        'payout_threshold',
        'payout_day_of_month',
        'require_subscription',
        'minimum_account_age_days',
        'terms_and_conditions',
    ];

    protected function casts(): array
    {
        return [
            'default_percentage' => 'integer',
            'payout_threshold' => 'decimal:2',
            'payout_day_of_month' => 'integer',
            'require_subscription' => 'boolean',
            'minimum_account_age_days' => 'integer',
        ];
    }

    /**
     * Get the current referral settings (singleton pattern).
     */
    public static function get(): self
    {
        $settings = self::first();

        if (! $settings) {
            $settings = self::create([
                'default_percentage' => config('collabconnect.referrals.default_percentage', 10),
                'payout_threshold' => 25.00,
                'payout_day_of_month' => 15,
                'require_subscription' => true,
                'minimum_account_age_days' => 0,
            ]);
        }

        return $settings;
    }

    /**
     * Update referral settings.
     */
    public static function updateSettings(array $data): self
    {
        $settings = self::get();
        $settings->update($data);

        return $settings;
    }
}
