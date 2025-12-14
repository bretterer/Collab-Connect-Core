<?php

namespace Database\Seeders;

use App\Models\StripePrice;
use App\Models\StripeProduct;
use Illuminate\Database\Seeder;
use Laravel\Cashier\Cashier;

class StripeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Fetches all products and their associated prices from Stripe
     * and syncs them to the local database.
     */
    public function run(): void
    {
        $stripe = Cashier::stripe(['api_key' => config('cashier.secret')]);

        $this->syncProducts($stripe);
        $this->syncPrices($stripe);
    }

    /**
     * Sync all products from Stripe to the local database.
     *
     * @param  \Stripe\StripeClient  $stripe
     */
    protected function syncProducts($stripe): void
    {
        $this->command->info('Fetching products from Stripe...');

        $hasMore = true;
        $startingAfter = null;
        $syncedCount = 0;

        while ($hasMore) {
            $params = ['limit' => 100];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }

            $products = $stripe->products->all($params);

            foreach ($products->data as $productData) {
                StripeProduct::updateOrCreate(
                    ['stripe_id' => $productData->id],
                    [
                        'name' => $productData->name,
                        'active' => $productData->active,
                        'description' => $productData->description ?? null,
                        'metadata' => $productData->metadata->toArray() ?? null,
                        'livemode' => $productData->livemode ?? false,
                    ]
                );

                $this->command->line("Synced product: {$productData->name} ({$productData->id})");
                $syncedCount++;
            }

            $hasMore = $products->has_more;
            if ($hasMore && ! empty($products->data)) {
                $startingAfter = end($products->data)->id;
            }
        }

        $this->command->info("Products synced: {$syncedCount} total");
    }

    /**
     * Sync all prices from Stripe to the local database.
     *
     * @param  \Stripe\StripeClient  $stripe
     */
    protected function syncPrices($stripe): void
    {
        $this->command->info('Fetching prices from Stripe...');

        $hasMore = true;
        $startingAfter = null;
        $syncedCount = 0;
        $skippedCount = 0;

        while ($hasMore) {
            $params = ['limit' => 100];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }

            $prices = $stripe->prices->all($params);

            foreach ($prices->data as $priceData) {
                $product = StripeProduct::where('stripe_id', $priceData->product)->first();

                if (! $product) {
                    $this->command->warn("Skipping price {$priceData->id} - product {$priceData->product} not found in database");
                    $skippedCount++;

                    continue;
                }

                StripePrice::updateOrCreate(
                    ['stripe_id' => $priceData->id],
                    [
                        'stripe_product_id' => $product->id,
                        'product_name' => $product->name,
                        'lookup_key' => $priceData->lookup_key ?? null,
                        'active' => $priceData->active,
                        'billing_scheme' => $priceData->billing_scheme,
                        'livemode' => $priceData->livemode,
                        'metadata' => $priceData->metadata->toArray() ?? null,
                        'recurring' => $priceData->recurring ? $priceData->recurring->toArray() : null,
                        'type' => $priceData->type,
                        'unit_amount' => $priceData->unit_amount ?? 0,
                        'currency' => $priceData->currency ?? 'usd',
                    ]
                );

                $this->command->line("Synced price: {$priceData->id} for {$product->name}");
                $syncedCount++;
            }

            $hasMore = $prices->has_more;
            if ($hasMore && ! empty($prices->data)) {
                $startingAfter = end($prices->data)->id;
            }
        }

        $this->command->info("Prices synced: {$syncedCount} total ({$skippedCount} skipped)");
    }
}
