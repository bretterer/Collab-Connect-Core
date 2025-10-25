<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripeProduct;
use Illuminate\Database\Seeder;

class StripeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $influencer = StripeProduct::create([
            'stripe_id' => config('collabconnect.stripe.products.influencer'),
            'name' => 'Influencer',
            'active' => true,
            'description' => 'Influencer Subscription Plan',
            'metadata' => [],
            'billable_type' => Influencer::class,
            'livemode' => false,
        ]);

        $influencer->prices()->createMany([
            [
                'stripe_id' => config('collabconnect.stripe.prices.influencer_basic'),
                'active' => true,
                'billing_scheme' => 'per_unit',
                'livemode' => false,
                'metadata' => [],
                'recurring' => [
                    'interval' => 'month',
                    'interval_count' => 1,
                ],
                'type' => 'recurring',
                'unit_amount' => 499,
            ],
        ]);

        $business = StripeProduct::create([
            'stripe_id' => config('collabconnect.stripe.products.business'),
            'name' => 'Business',
            'active' => true,
            'description' => 'Business Subscription Plan',
            'metadata' => [],
            'billable_type' => Business::class,
            'livemode' => false,
        ]);

        $business->prices()->createMany([
            [
                'stripe_id' => config('collabconnect.stripe.prices.business_basic'),
                'active' => true,
                'billing_scheme' => 'per_unit',
                'livemode' => false,
                'metadata' => [],
                'recurring' => [
                    'interval' => 'month',
                    'interval_count' => 1,
                ],
                'type' => 'recurring',
                'unit_amount' => 999,
            ],
            [
                'stripe_id' => config('collabconnect.stripe.prices.business_pro'),
                'active' => true,
                'billing_scheme' => 'per_unit',
                'livemode' => false,
                'metadata' => [],
                'recurring' => [
                    'interval' => 'month',
                    'interval_count' => 1,
                ],
                'type' => 'recurring',
                'unit_amount' => 1999,
            ],
        ]);
    }
}
