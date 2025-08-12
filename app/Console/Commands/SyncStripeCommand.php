<?php

namespace App\Console\Commands;

use App\Models\StripePrice;
use App\Models\StripeProduct;
use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;

class SyncStripeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collabconnect:sync-stripe 
                            {--products-only : Only sync products} 
                            {--prices-only : Only sync prices} 
                            {--limit=100 : Limit the number of items to sync per type}
                            {--active-only : Only sync active products and prices}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products and prices from Stripe to the local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will sync data from Stripe and may overwrite local changes. Continue?')) {
                $this->info('Sync cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('Starting Stripe sync...');

        $stripe = Cashier::stripe();
        $limit = (int) $this->option('limit');

        // Test Stripe connection
        try {
            $stripe->products->all(['limit' => 1]);
        } catch (\Exception $e) {
            $this->error('Unable to connect to Stripe: ' . $e->getMessage());
            return Command::FAILURE;
        }

        try {
            // Sync products unless prices-only is specified
            if (!$this->option('prices-only')) {
                $this->info('Syncing products from Stripe...');
                $this->syncProducts($stripe, $limit);
            }

            // Sync prices unless products-only is specified
            if (!$this->option('products-only')) {
                $this->info('Syncing prices from Stripe...');
                $this->syncPrices($stripe, $limit);
            }

            $this->info('Stripe sync completed successfully!');
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Sync products from Stripe
     */
    private function syncProducts($stripe, int $limit)
    {
        $hasMore = true;
        $startingAfter = null;
        $syncedCount = 0;
        $updatedCount = 0;
        $createdCount = 0;

        while ($hasMore && $syncedCount < $limit) {
            $params = ['limit' => min(100, $limit - $syncedCount)];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }
            if ($this->option('active-only')) {
                $params['active'] = true;
            }

            $products = $stripe->products->all($params);

            foreach ($products->data as $productData) {
                $existingProduct = StripeProduct::where('stripe_id', $productData->id)->first();

                if ($existingProduct) {
                    // Update existing product
                    $existingProduct->update([
                        'name' => $productData->name,
                        'active' => $productData->active,
                        'description' => $productData->description ?? null,
                        'metadata' => $productData->metadata->toArray() ?? null,
                        'livemode' => $productData->livemode ?? false,
                    ]);
                    $updatedCount++;
                    $this->line("Updated product: {$productData->name} ({$productData->id})");
                } else {
                    // Create new product
                    StripeProduct::create([
                        'stripe_id' => $productData->id,
                        'name' => $productData->name,
                        'active' => $productData->active,
                        'description' => $productData->description ?? null,
                        'metadata' => $productData->metadata->toArray() ?? null,
                        'livemode' => $productData->livemode ?? false,
                    ]);
                    $createdCount++;
                    $this->line("Created product: {$productData->name} ({$productData->id})");
                }

                $syncedCount++;
            }

            $hasMore = $products->has_more && $syncedCount < $limit;
            if ($hasMore && !empty($products->data)) {
                $startingAfter = end($products->data)->id;
            }
        }

        $this->info("Products synced: {$syncedCount} total ({$createdCount} created, {$updatedCount} updated)");
    }

    /**
     * Sync prices from Stripe
     */
    private function syncPrices($stripe, int $limit)
    {
        $hasMore = true;
        $startingAfter = null;
        $syncedCount = 0;
        $updatedCount = 0;
        $createdCount = 0;
        $skippedCount = 0;

        while ($hasMore && $syncedCount < $limit) {
            $params = ['limit' => min(100, $limit - $syncedCount)];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }
            if ($this->option('active-only')) {
                $params['active'] = true;
            }

            $prices = $stripe->prices->all($params);

            foreach ($prices->data as $priceData) {
                // Find the corresponding product in our database
                $product = StripeProduct::where('stripe_id', $priceData->product)->first();

                if (!$product) {
                    $this->warn("Skipping price {$priceData->id} - product {$priceData->product} not found in database");
                    $skippedCount++;
                    $syncedCount++;
                    continue;
                }

                $existingPrice = StripePrice::where('stripe_id', $priceData->id)->first();

                if ($existingPrice) {
                    // Update existing price
                    $existingPrice->update([
                        'active' => $priceData->active,
                        'billing_scheme' => $priceData->billing_scheme,
                        'livemode' => $priceData->livemode,
                        'metadata' => $priceData->metadata->toArray() ?? null,
                        'stripe_product_id' => $product->id,
                        'recurring' => $priceData->recurring ? $priceData->recurring->toArray() : null,
                        'type' => $priceData->type,
                        'unit_amount' => $priceData->unit_amount,
                    ]);
                    $updatedCount++;
                    $this->line("Updated price: {$priceData->id} ($" . $this->formatPrice($priceData->unit_amount) . ")");
                } else {
                    // Create new price
                    StripePrice::create([
                        'stripe_id' => $priceData->id,
                        'active' => $priceData->active,
                        'billing_scheme' => $priceData->billing_scheme,
                        'livemode' => $priceData->livemode,
                        'metadata' => $priceData->metadata->toArray() ?? null,
                        'stripe_product_id' => $product->id,
                        'recurring' => $priceData->recurring ? $priceData->recurring->toArray() : null,
                        'type' => $priceData->type,
                        'unit_amount' => $priceData->unit_amount,
                    ]);
                    $createdCount++;
                    $this->line("Created price: {$priceData->id} ($" . $this->formatPrice($priceData->unit_amount) . ")");
                }

                $syncedCount++;
            }

            $hasMore = $prices->has_more && $syncedCount < $limit;
            if ($hasMore && !empty($prices->data)) {
                $startingAfter = end($prices->data)->id;
            }
        }

        $this->info("Prices synced: {$syncedCount} total ({$createdCount} created, {$updatedCount} updated, {$skippedCount} skipped)");
    }

    /**
     * Format price for display
     */
    private function formatPrice(?int $unitAmount): string
    {
        if ($unitAmount === null) {
            return '0.00';
        }
        return number_format($unitAmount / 100, 2);
    }
}
