<?php

namespace App\Listeners;

use App\Enums\AccountType;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Subscription\SubscriptionMetadataSchema;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;

class StripePriceCreated
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'price.created') {

            $priceData = $event->payload['data']['object'];

            $product = StripeProduct::query()
                ->where('stripe_id', $priceData['product'])
                ->first();

            if (! $product) {
                throw new \Exception('Stripe product not found');
            }

            // Merge existing metadata with default limit keys
            $existingMetadata = $priceData['metadata'] ?? [];
            $mergedMetadata = $this->mergeWithDefaultMetadata($existingMetadata, $product);

            // Create a new StripePrice record in the database
            StripePrice::create([
                'stripe_id' => $priceData['id'],
                'active' => $priceData['active'],
                'billing_scheme' => $priceData['billing_scheme'],
                'livemode' => $priceData['livemode'],
                'metadata' => $mergedMetadata,
                'stripe_product_id' => $product->id,
                'recurring' => $priceData['recurring'] ?? null,
                'type' => $priceData['type'],
                'unit_amount' => $priceData['unit_amount'],
                'product_name' => $priceData['nickname'] ?? null,
                'currency' => $priceData['currency'] ?? null,
                'lookup_key' => $priceData['lookup_key'] ?? null,
            ]);

            // Push default metadata to Stripe so all limit keys are present
            $this->pushDefaultMetadataToStripe($priceData['id'], $mergedMetadata);
        }
    }

    /**
     * Merge existing metadata with default limit keys for the product's account type.
     *
     * @param  array<string, mixed>  $existingMetadata
     * @return array<string, mixed>
     */
    private function mergeWithDefaultMetadata(array $existingMetadata, StripeProduct $product): array
    {
        $accountType = $this->getAccountTypeFromProduct($product);

        if (! $accountType) {
            return $existingMetadata;
        }

        // Get default values for this account type (all 0)
        $defaults = SubscriptionMetadataSchema::getDefaultsForAccountType($accountType);

        // Merge: existing values take precedence over defaults
        return array_merge($defaults, $existingMetadata);
    }

    /**
     * Get the AccountType from a product's metadata.
     */
    private function getAccountTypeFromProduct(StripeProduct $product): ?AccountType
    {
        $accountTypeValue = $product->metadata['account_type'] ?? null;

        if (! $accountTypeValue) {
            return null;
        }

        // Handle both enum name (BUSINESS, INFLUENCER) and value (1, 2)
        foreach (AccountType::cases() as $case) {
            if ($case->name === $accountTypeValue || $case->value == $accountTypeValue) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Push default metadata to Stripe for the price.
     *
     * @param  array<string, mixed>  $metadata
     */
    private function pushDefaultMetadataToStripe(string $priceId, array $metadata): void
    {
        try {
            $stripe = Cashier::stripe();
            $stripe->prices->update($priceId, [
                'metadata' => $metadata,
            ]);
        } catch (\Exception $e) {
            // Log but don't fail - the local record is already created
            report($e);
        }
    }
}
