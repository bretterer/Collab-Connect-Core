<?php

namespace Tests\Feature\Integration;

use App\Enums\AccountType;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PricingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);
    }

    #[Test]
    public function complete_pricing_workflow()
    {
        // 1. Create products via factory (simulating Stripe sync)
        $businessProduct = StripeProduct::factory()->business()->create([
            'name' => 'Business Plan',
            'description' => 'For growing businesses',
        ]);

        $influencerProduct = StripeProduct::factory()->influencer()->create([
            'name' => 'Influencer Plan',
            'description' => 'For content creators',
        ]);

        // 2. Create prices for each product
        $businessMonthly = StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $businessProduct->id,
            'unit_amount' => 2999, // $29.99
            'metadata' => [
                'features' => json_encode([
                    'Unlimited campaigns',
                    'Advanced analytics',
                    'Priority support',
                ]),
            ],
        ]);

        $businessYearly = StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $businessProduct->id,
            'unit_amount' => 29999, // $299.99
            'recurring' => ['interval' => 'year', 'interval_count' => 1],
            'metadata' => [
                'features' => json_encode([
                    'Unlimited campaigns',
                    'Advanced analytics',
                    'Priority support',
                    '2 months free',
                ]),
            ],
        ]);

        $influencerMonthly = StripePrice::factory()->recurring()->create([
            'stripe_product_id' => $influencerProduct->id,
            'unit_amount' => 999, // $9.99
            'metadata' => [
                'features' => json_encode([
                    'Campaign discovery',
                    'Basic analytics',
                    'Email support',
                ]),
            ],
        ]);

        // 3. Test admin can view all products and prices
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/pricing');

        $response->assertOk()
            ->assertSee('Business Plan')
            ->assertSee('Influencer Plan')
            ->assertSee('$29.99')
            ->assertSee('$9.99')
            ->assertSee('$299.99');

        // 4. Test filtering by account type works
        $this->assertEquals(2, StripeProduct::count());
        $this->assertEquals(3, StripePrice::count());

        // Business products
        $businessProducts = StripeProduct::whereJsonContains('metadata->account_type', 'BUSINESS')->get();
        $this->assertEquals(1, $businessProducts->count());
        $this->assertEquals('Business Plan', $businessProducts->first()->name);

        // Influencer products
        $influencerProducts = StripeProduct::whereJsonContains('metadata->account_type', 'INFLUENCER')->get();
        $this->assertEquals(1, $influencerProducts->count());
        $this->assertEquals('Influencer Plan', $influencerProducts->first()->name);

        // 5. Test price features are properly stored and retrieved
        $businessPrice = StripePrice::where('unit_amount', 2999)->first();
        $features = json_decode($businessPrice->metadata['features'], true);

        $this->assertCount(3, $features);
        $this->assertContains('Unlimited campaigns', $features);
        $this->assertContains('Advanced analytics', $features);
        $this->assertContains('Priority support', $features);

        // 6. Test yearly plan has additional features
        $yearlyPrice = StripePrice::where('unit_amount', 29999)->first();
        $yearlyFeatures = json_decode($yearlyPrice->metadata['features'], true);

        $this->assertCount(4, $yearlyFeatures);
        $this->assertContains('2 months free', $yearlyFeatures);
    }

    #[Test]
    public function pricing_data_structure_integrity()
    {
        $product = StripeProduct::factory()->create([
            'metadata' => [
                'account_type' => 'BUSINESS',
                'custom_field' => 'custom_value',
            ],
        ]);

        $price = StripePrice::factory()->create([
            'stripe_product_id' => $product->id,
            'metadata' => [
                'features' => json_encode(['Feature A', 'Feature B']),
                'highlight' => 'Most Popular',
            ],
        ]);

        // Test relationships work correctly
        $this->assertEquals($product->id, $price->stripeProduct->id);
        $this->assertTrue($product->stripePrices->contains($price));

        // Test metadata is properly cast
        $this->assertIsArray($product->metadata);
        $this->assertIsArray($price->metadata);

        // Test JSON features are accessible
        $features = json_decode($price->metadata['features'], true);
        $this->assertIsArray($features);
        $this->assertEquals(['Feature A', 'Feature B'], $features);

        // Test additional metadata is preserved
        $this->assertEquals('custom_value', $product->metadata['custom_field']);
        $this->assertEquals('Most Popular', $price->metadata['highlight']);
    }

    #[Test]
    public function account_type_enum_integration()
    {
        // Test creating products with different account types
        foreach ([AccountType::BUSINESS, AccountType::INFLUENCER] as $accountType) {
            $product = StripeProduct::factory()->create([
                'metadata' => ['account_type' => $accountType->name],
            ]);

            // Test we can retrieve and use the enum
            $storedAccountType = $product->metadata['account_type'];

            // Find matching enum case
            $enumCase = null;
            foreach (AccountType::cases() as $case) {
                if ($case->name === $storedAccountType) {
                    $enumCase = $case;
                    break;
                }
            }

            $this->assertNotNull($enumCase);
            $this->assertEquals($accountType, $enumCase);
        }
    }

    #[Test]
    public function handles_edge_cases()
    {
        // Test free price
        $freePrice = StripePrice::factory()->free()->create();
        $this->assertEquals(0, $freePrice->unit_amount);

        // Test inactive products and prices
        $inactiveProduct = StripeProduct::factory()->inactive()->create();
        $inactivePrice = StripePrice::factory()->inactive()->create();

        $this->assertFalse($inactiveProduct->active);
        $this->assertFalse($inactivePrice->active);

        // Test products without metadata
        $simpleProduct = StripeProduct::factory()->create(['metadata' => null]);
        $this->assertNull($simpleProduct->metadata);

        // Test prices without features
        $simplePrice = StripePrice::factory()->create(['metadata' => []]);
        $this->assertEquals([], $simplePrice->metadata);

        // Test one-time prices
        $oneTimePrice = StripePrice::factory()->oneTime()->create();
        $this->assertEquals('one_time', $oneTimePrice->type);
        $this->assertNull($oneTimePrice->recurring);
    }

    #[Test]
    public function bulk_operations_work_correctly()
    {
        // Explicitly clean the database for this test
        StripePrice::query()->delete();
        StripeProduct::query()->delete();

        // Ensure we start with a clean database
        $this->assertEquals(0, StripeProduct::count());
        $this->assertEquals(0, StripePrice::count());

        // Create multiple products and prices
        $products = StripeProduct::factory(5)->create();

        foreach ($products as $product) {
            StripePrice::factory(2)->create(['stripe_product_id' => $product->id]);
        }

        // Test bulk queries
        $this->assertEquals(5, StripeProduct::count());
        $this->assertEquals(10, StripePrice::count());

        // Test eager loading
        $productsWithPrices = StripeProduct::with('stripePrices')->get();

        foreach ($productsWithPrices as $product) {
            $this->assertEquals(2, $product->stripePrices->count());
            // Ensure no N+1 queries by accessing related data
            foreach ($product->stripePrices as $price) {
                $this->assertNotNull($price->unit_amount);
            }
        }

        // Test filtering active items
        $inactiveProducts = StripeProduct::factory(2)->inactive()->create();
        StripePrice::factory(3)->inactive()->create(['stripe_product_id' => $inactiveProducts->first()->id]);

        // Now we should have 7 total products (5 active, 2 inactive) and 13 total prices (10 active, 3 inactive)
        $this->assertEquals(7, StripeProduct::count());
        $this->assertEquals(13, StripePrice::count());

        $activeProducts = StripeProduct::where('active', true)->count();
        $activePrices = StripePrice::where('active', true)->count();

        $this->assertEquals(5, $activeProducts); // Original 5 are active
        $this->assertEquals(10, $activePrices); // Original 10 are active
    }
}
