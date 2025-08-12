<?php

namespace Tests\Feature\Commands;

use App\Console\Commands\SyncStripeCommand;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Cashier;
use Mockery;
use stdClass;
use Tests\TestCase;

class SyncStripeCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function sync_command_requires_confirmation_by_default()
    {
        $this->artisan('collabconnect:sync-stripe')
            ->expectsConfirmation('This will sync data from Stripe and may overwrite local changes. Continue?', 'no')
            ->expectsOutputToContain('Sync cancelled.')
            ->assertExitCode(0);
    }

    /** @test */
    public function sync_command_can_be_forced()
    {
        $this->mockStripeApiSuccess();

        $this->artisan('collabconnect:sync-stripe --force')
            ->expectsOutputToContain('Starting Stripe sync...')
            ->expectsOutputToContain('Stripe sync completed successfully!')
            ->assertExitCode(0);
    }

    /** @test */
    public function sync_command_tests_stripe_connection()
    {
        $this->mockStripeApiConnectionFailure();

        $this->artisan('collabconnect:sync-stripe --force')
            ->expectsOutputToContain('Unable to connect to Stripe')
            ->assertExitCode(1);
    }

    /** @test */
    public function sync_command_syncs_products_from_stripe()
    {
        $this->mockStripeProductsApi();

        $this->artisan('collabconnect:sync-stripe --force --products-only')
            ->expectsOutputToContain('Syncing products from Stripe...')
            ->expectsOutputToContain('Created product: Test Product 1')
            ->expectsOutputToContain('Created product: Test Product 2')
            ->expectsOutputToContain('Products synced: 2 total (2 created, 0 updated)')
            ->assertExitCode(0);

        $this->assertDatabaseHas('stripe_products', [
            'stripe_id' => 'prod_test1',
            'name' => 'Test Product 1',
            'active' => true,
        ]);

        $this->assertDatabaseHas('stripe_products', [
            'stripe_id' => 'prod_test2',
            'name' => 'Test Product 2',
            'active' => false,
        ]);
    }

    /** @test */
    public function sync_command_updates_existing_products()
    {
        // Create existing product with old data
        StripeProduct::factory()->create([
            'stripe_id' => 'prod_test1',
            'name' => 'Old Product Name',
            'active' => false,
        ]);

        $this->mockStripeProductsApi();

        $this->artisan('collabconnect:sync-stripe --force --products-only')
            ->expectsOutputToContain('Updated product: Test Product 1')
            ->expectsOutputToContain('Products synced: 2 total (1 created, 1 updated)')
            ->assertExitCode(0);

        // Verify product was updated
        $this->assertDatabaseHas('stripe_products', [
            'stripe_id' => 'prod_test1',
            'name' => 'Test Product 1', // Updated name
            'active' => true, // Updated status
        ]);
    }

    /** @test */
    public function sync_command_syncs_prices_from_stripe()
    {
        // Create a product first (prices need products)
        $product = StripeProduct::factory()->create(['stripe_id' => 'prod_test1']);

        $this->mockStripePricesApi();

        $this->artisan('collabconnect:sync-stripe --force --prices-only')
            ->expectsOutputToContain('Syncing prices from Stripe...')
            ->expectsOutputToContain('Created price: price_test1 ($19.99)')
            ->expectsOutputToContain('Created price: price_test2 ($9.99)')
            ->expectsOutputToContain('Prices synced: 2 total (2 created, 0 updated, 0 skipped)')
            ->assertExitCode(0);

        $this->assertDatabaseHas('stripe_prices', [
            'stripe_id' => 'price_test1',
            'unit_amount' => 1999,
            'stripe_product_id' => $product->id,
        ]);
    }

    /** @test */
    public function sync_command_skips_prices_without_products()
    {
        $this->mockStripePricesApi();

        $this->artisan('collabconnect:sync-stripe --force --prices-only')
            ->expectsOutputToContain('Skipping price price_test1 - product prod_test1 not found in database')
            ->expectsOutputToContain('Prices synced: 2 total (0 created, 0 updated, 2 skipped)')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('stripe_prices', [
            'stripe_id' => 'price_test1',
        ]);
    }

    /** @test */
    public function sync_command_updates_existing_prices()
    {
        $product = StripeProduct::factory()->create(['stripe_id' => 'prod_test1']);
        
        // Create existing price with old data
        StripePrice::factory()->create([
            'stripe_id' => 'price_test1',
            'unit_amount' => 999, // Old amount
            'stripe_product_id' => $product->id,
        ]);

        $this->mockStripePricesApi();

        $this->artisan('collabconnect:sync-stripe --force --prices-only')
            ->expectsOutputToContain('Updated price: price_test1 ($19.99)')
            ->expectsOutputToContain('Prices synced: 2 total (1 created, 1 updated, 0 skipped)')
            ->assertExitCode(0);

        // Verify price was updated
        $this->assertDatabaseHas('stripe_prices', [
            'stripe_id' => 'price_test1',
            'unit_amount' => 1999, // Updated amount
        ]);
    }

    /** @test */
    public function sync_command_respects_limit_option()
    {
        $this->mockStripeProductsApiWithPagination();

        $this->artisan('collabconnect:sync-stripe --force --products-only --limit=1')
            ->expectsOutputToContain('Products synced: 1 total')
            ->assertExitCode(0);

        // Should only have created 1 product due to limit
        $this->assertEquals(1, StripeProduct::count());
    }

    /** @test */
    public function sync_command_respects_active_only_option()
    {
        $this->mockStripeActiveProductsApi();

        $this->artisan('collabconnect:sync-stripe --force --products-only --active-only')
            ->expectsOutputToContain('Products synced: 1 total')
            ->assertExitCode(0);

        // Should only have the active product
        $this->assertDatabaseHas('stripe_products', [
            'stripe_id' => 'prod_active',
            'active' => true,
        ]);
    }

    /** @test */
    public function sync_command_handles_stripe_api_errors()
    {
        $this->mockStripeApiError();

        $this->artisan('collabconnect:sync-stripe --force')
            ->expectsOutputToContain('Sync failed: API Error')
            ->assertExitCode(1);
    }

    protected function mockStripeApiSuccess()
    {
        $stripeMock = Mockery::mock();
        $productsMock = Mockery::mock();
        $pricesMock = Mockery::mock();

        // Mock successful connection test
        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->once()
            ->andReturn($this->createStripeCollection([]));

        // Mock empty results for actual sync
        $productsMock->shouldReceive('all')
            ->andReturn($this->createStripeCollection([]));
        
        $pricesMock->shouldReceive('all')
            ->andReturn($this->createStripeCollection([]));

        $stripeMock->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($productsMock);
            
        $stripeMock->shouldReceive('getAttribute')
            ->with('prices')
            ->andReturn($pricesMock);

        Cashier::shouldReceive('stripe')->andReturn($stripeMock);
    }

    protected function mockStripeApiConnectionFailure()
    {
        $stripeMock = Mockery::mock();
        $productsMock = Mockery::mock();

        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->andThrow(new \Exception('Connection failed'));

        $stripeMock->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($productsMock);

        Cashier::shouldReceive('stripe')->andReturn($stripeMock);
    }

    protected function mockStripeProductsApi()
    {
        $stripeMock = Mockery::mock();
        $productsMock = Mockery::mock();

        // Mock connection test
        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->once()
            ->andReturn($this->createStripeCollection([]));

        // Mock products sync
        $products = [
            $this->createStripeProduct('prod_test1', 'Test Product 1', true),
            $this->createStripeProduct('prod_test2', 'Test Product 2', false),
        ];

        $productsMock->shouldReceive('all')
            ->andReturn($this->createStripeCollection($products));

        $stripeMock->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($productsMock);

        Cashier::shouldReceive('stripe')->andReturn($stripeMock);
    }

    protected function mockStripePricesApi()
    {
        $stripeMock = Mockery::mock();
        $productsMock = Mockery::mock();
        $pricesMock = Mockery::mock();

        // Mock connection test
        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->once()
            ->andReturn($this->createStripeCollection([]));

        // Mock prices sync
        $prices = [
            $this->createStripePrice('price_test1', 'prod_test1', 1999),
            $this->createStripePrice('price_test2', 'prod_test1', 999),
        ];

        $pricesMock->shouldReceive('all')
            ->andReturn($this->createStripeCollection($prices));

        $stripeMock->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($productsMock);
            
        $stripeMock->shouldReceive('getAttribute')
            ->with('prices')
            ->andReturn($pricesMock);

        Cashier::shouldReceive('stripe')->andReturn($stripeMock);
    }

    protected function mockStripeProductsApiWithPagination()
    {
        $stripeMock = Mockery::mock();
        $productsMock = Mockery::mock();

        // Mock connection test
        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->once()
            ->andReturn($this->createStripeCollection([]));

        // Mock first page with 1 product (due to limit)
        $products = [$this->createStripeProduct('prod_test1', 'Test Product 1', true)];
        
        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->andReturn($this->createStripeCollection($products, false));

        $stripeMock->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($productsMock);

        Cashier::shouldReceive('stripe')->andReturn($stripeMock);
    }

    protected function mockStripeActiveProductsApi()
    {
        $stripeMock = Mockery::mock();
        $productsMock = Mockery::mock();

        // Mock connection test
        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->once()
            ->andReturn($this->createStripeCollection([]));

        // Mock active products only
        $products = [$this->createStripeProduct('prod_active', 'Active Product', true)];
        
        $productsMock->shouldReceive('all')
            ->with(['limit' => 100, 'active' => true])
            ->andReturn($this->createStripeCollection($products));

        $stripeMock->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($productsMock);

        Cashier::shouldReceive('stripe')->andReturn($stripeMock);
    }

    protected function mockStripeApiError()
    {
        $stripeMock = Mockery::mock();
        $productsMock = Mockery::mock();

        // Mock connection test success
        $productsMock->shouldReceive('all')
            ->with(['limit' => 1])
            ->once()
            ->andReturn($this->createStripeCollection([]));

        // Mock API error during sync
        $productsMock->shouldReceive('all')
            ->andThrow(new \Exception('API Error'));

        $stripeMock->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($productsMock);

        Cashier::shouldReceive('stripe')->andReturn($stripeMock);
    }

    protected function createStripeCollection(array $data, bool $hasMore = false): stdClass
    {
        $collection = new stdClass();
        $collection->data = $data;
        $collection->has_more = $hasMore;
        return $collection;
    }

    protected function createStripeProduct(string $id, string $name, bool $active): stdClass
    {
        $product = new stdClass();
        $product->id = $id;
        $product->name = $name;
        $product->active = $active;
        $product->description = 'Test description';
        $product->livemode = false;
        
        $metadata = new stdClass();
        $metadata->account_type = 'BUSINESS';
        $product->metadata = $metadata;
        
        return $product;
    }

    protected function createStripePrice(string $id, string $productId, int $unitAmount): stdClass
    {
        $price = new stdClass();
        $price->id = $id;
        $price->product = $productId;
        $price->active = true;
        $price->billing_scheme = 'per_unit';
        $price->livemode = false;
        $price->type = 'recurring';
        $price->unit_amount = $unitAmount;
        
        $metadata = new stdClass();
        $price->metadata = $metadata;
        
        $recurring = new stdClass();
        $recurring->interval = 'month';
        $recurring->interval_count = 1;
        $price->recurring = $recurring;
        
        return $price;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}