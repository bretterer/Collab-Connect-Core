<?php

namespace Tests\Feature\Webhooks;

use App\Listeners\StripePriceCreated;
use App\Listeners\StripeProductCreated;
use App\Listeners\StripeProductDeleted;
use App\Listeners\StripeProductUpdated;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Events\WebhookReceived;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function handles_product_created_webhook()
    {
        $webhookPayload = [
            'type' => 'product.created',
            'data' => [
                'object' => [
                    'id' => 'prod_test123',
                    'name' => 'Test Product',
                    'active' => true,
                    'description' => 'A test product',
                    'metadata' => [
                        'account_type' => 'BUSINESS',
                    ],
                    'livemode' => false,
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripeProductCreated;
        $listener->handle($event);

        $this->assertDatabaseHas('stripe_products', [
            'stripe_id' => 'prod_test123',
            'name' => 'Test Product',
            'active' => true,
            'description' => 'A test product',
            'livemode' => false,
        ]);

        $product = StripeProduct::where('stripe_id', 'prod_test123')->first();
        $this->assertEquals(['account_type' => 'BUSINESS'], $product->metadata);
    }

    #[Test]
    public function handles_product_updated_webhook()
    {
        // Create existing product
        $product = StripeProduct::factory()->create([
            'stripe_id' => 'prod_test123',
            'name' => 'Old Product Name',
            'active' => false,
        ]);

        $webhookPayload = [
            'type' => 'product.updated',
            'data' => [
                'object' => [
                    'id' => 'prod_test123',
                    'name' => 'Updated Product Name',
                    'active' => true,
                    'description' => 'Updated description',
                    'metadata' => [
                        'account_type' => 'INFLUENCER',
                    ],
                    'livemode' => false,
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripeProductUpdated;
        $listener->handle($event);

        // Refresh the model
        $product->refresh();

        $this->assertEquals('Updated Product Name', $product->name);
        $this->assertTrue($product->active);
        $this->assertEquals('Updated description', $product->description);
        $this->assertEquals(['account_type' => 'INFLUENCER'], $product->metadata);
    }

    #[Test]
    public function handles_product_deleted_webhook()
    {
        // Create existing product
        $product = StripeProduct::factory()->create([
            'stripe_id' => 'prod_test123',
        ]);

        $webhookPayload = [
            'type' => 'product.deleted',
            'data' => [
                'object' => [
                    'id' => 'prod_test123',
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripeProductDeleted;
        $listener->handle($event);

        $this->assertSoftDeleted('stripe_products', [
            'stripe_id' => 'prod_test123',
        ]);
    }

    #[Test]
    public function handles_price_created_webhook()
    {
        // Create a product first (prices need products)
        $product = StripeProduct::factory()->create([
            'stripe_id' => 'prod_test123',
        ]);

        $webhookPayload = [
            'type' => 'price.created',
            'data' => [
                'object' => [
                    'id' => 'price_test123',
                    'product' => 'prod_test123',
                    'active' => true,
                    'billing_scheme' => 'per_unit',
                    'livemode' => false,
                    'metadata' => [
                        'features' => json_encode(['Feature 1', 'Feature 2']),
                    ],
                    'recurring' => [
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
                    'type' => 'recurring',
                    'unit_amount' => 1999,
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripePriceCreated;
        $listener->handle($event);

        $this->assertDatabaseHas('stripe_prices', [
            'stripe_id' => 'price_test123',
            'stripe_product_id' => $product->id,
            'active' => true,
            'billing_scheme' => 'per_unit',
            'livemode' => false,
            'type' => 'recurring',
            'unit_amount' => 1999,
        ]);

        $price = StripePrice::where('stripe_id', 'price_test123')->first();
        $this->assertEquals([
            'interval' => 'month',
            'interval_count' => 1,
        ], $price->recurring);

        $this->assertEquals([
            'features' => json_encode(['Feature 1', 'Feature 2']),
        ], $price->metadata);
    }

    #[Test]
    public function price_created_webhook_throws_exception_when_product_not_found()
    {
        $webhookPayload = [
            'type' => 'price.created',
            'data' => [
                'object' => [
                    'id' => 'price_test123',
                    'product' => 'prod_nonexistent',
                    'active' => true,
                    'billing_scheme' => 'per_unit',
                    'livemode' => false,
                    'metadata' => [],
                    'recurring' => null,
                    'type' => 'one_time',
                    'unit_amount' => 1999,
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripePriceCreated;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe product not found');

        $listener->handle($event);
    }

    #[Test]
    public function handles_product_webhook_with_null_metadata()
    {
        $webhookPayload = [
            'type' => 'product.created',
            'data' => [
                'object' => [
                    'id' => 'prod_test123',
                    'name' => 'Test Product',
                    'active' => true,
                    'description' => null,
                    'metadata' => null,
                    'livemode' => false,
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripeProductCreated;
        $listener->handle($event);

        $this->assertDatabaseHas('stripe_products', [
            'stripe_id' => 'prod_test123',
            'name' => 'Test Product',
            'description' => null,
        ]);

        $product = StripeProduct::where('stripe_id', 'prod_test123')->first();
        $this->assertNull($product->metadata);
    }

    #[Test]
    public function handles_price_webhook_with_null_metadata_and_recurring()
    {
        $product = StripeProduct::factory()->create([
            'stripe_id' => 'prod_test123',
        ]);

        $webhookPayload = [
            'type' => 'price.created',
            'data' => [
                'object' => [
                    'id' => 'price_test123',
                    'product' => 'prod_test123',
                    'active' => true,
                    'billing_scheme' => 'per_unit',
                    'livemode' => false,
                    'metadata' => null,
                    'recurring' => null,
                    'type' => 'one_time',
                    'unit_amount' => 1999,
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripePriceCreated;
        $listener->handle($event);

        $this->assertDatabaseHas('stripe_prices', [
            'stripe_id' => 'price_test123',
            'type' => 'one_time',
        ]);

        $price = StripePrice::where('stripe_id', 'price_test123')->first();
        $this->assertNull($price->metadata);
        $this->assertNull($price->recurring);
    }

    #[Test]
    public function ignores_non_matching_webhook_types()
    {
        $webhookPayload = [
            'type' => 'customer.created', // Different webhook type
            'data' => [
                'object' => [
                    'id' => 'cus_test123',
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);

        // These listeners should not create any records
        $productListener = new StripeProductCreated;
        $priceListener = new StripePriceCreated;

        $productListener->handle($event);
        $priceListener->handle($event);

        $this->assertDatabaseEmpty('stripe_products');
        $this->assertDatabaseEmpty('stripe_prices');
    }

    #[Test]
    public function handles_product_updated_webhook_for_nonexistent_product()
    {
        // StripeProductUpdated uses updateOrCreate, so it should create the product if it doesn't exist
        $webhookPayload = [
            'type' => 'product.updated',
            'data' => [
                'object' => [
                    'id' => 'prod_nonexistent',
                    'name' => 'Nonexistent Product',
                    'active' => true,
                    'description' => 'This product does not exist',
                    'metadata' => ['account_type' => 'BUSINESS'],
                    'livemode' => false,
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripeProductUpdated;

        $listener->handle($event);

        // Product should be created since updateOrCreate is used
        $this->assertDatabaseHas('stripe_products', [
            'stripe_id' => 'prod_nonexistent',
            'name' => 'Nonexistent Product',
            'active' => true,
        ]);
    }

    #[Test]
    public function handles_product_deleted_webhook_for_nonexistent_product()
    {
        $webhookPayload = [
            'type' => 'product.deleted',
            'data' => [
                'object' => [
                    'id' => 'prod_nonexistent',
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripeProductDeleted;

        // Should handle gracefully without throwing exception
        $listener->handle($event);

        $this->assertTrue(true); // No exception thrown
    }

    #[Test]
    public function handles_free_price_webhook()
    {
        $product = StripeProduct::factory()->create([
            'stripe_id' => 'prod_test123',
        ]);

        $webhookPayload = [
            'type' => 'price.created',
            'data' => [
                'object' => [
                    'id' => 'price_free',
                    'product' => 'prod_test123',
                    'active' => true,
                    'billing_scheme' => 'per_unit',
                    'livemode' => false,
                    'metadata' => [],
                    'recurring' => null,
                    'type' => 'one_time',
                    'unit_amount' => 0, // Free price
                ],
            ],
        ];

        $event = new WebhookReceived($webhookPayload);
        $listener = new StripePriceCreated;
        $listener->handle($event);

        $this->assertDatabaseHas('stripe_prices', [
            'stripe_id' => 'price_free',
            'unit_amount' => 0,
            'type' => 'one_time',
        ]);
    }
}
