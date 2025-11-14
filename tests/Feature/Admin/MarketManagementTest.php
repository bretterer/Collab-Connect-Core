<?php

namespace Tests\Feature\Admin;

use App\Enums\AccountType;
use App\Livewire\Admin\Markets\MarketIndex;
use App\Models\Market;
use App\Models\MarketZipcode;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_create_new_market(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        Livewire::actingAs($admin)
            ->test(MarketIndex::class)
            ->set('name', 'Greater Detroit Area')
            ->set('description', 'Detroit metro market')
            ->call('createMarket')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('markets', [
            'name' => 'Greater Detroit Area',
            'description' => 'Detroit metro market',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function admin_can_toggle_market_active_status(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $market = Market::factory()->inactive()->create();

        $this->assertFalse($market->is_active);

        Livewire::actingAs($admin)
            ->test(MarketIndex::class)
            ->call('toggleActive', $market->id)
            ->assertHasNoErrors();

        $market->refresh();
        $this->assertTrue($market->is_active);

        // Toggle back
        Livewire::actingAs($admin)
            ->test(MarketIndex::class)
            ->call('toggleActive', $market->id)
            ->assertHasNoErrors();

        $market->refresh();
        $this->assertFalse($market->is_active);
    }

    #[Test]
    public function admin_can_delete_market(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $market = Market::factory()->create();

        Livewire::actingAs($admin)
            ->test(MarketIndex::class)
            ->call('deleteMarket', $market->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('markets', [
            'id' => $market->id,
        ]);
    }

    #[Test]
    public function market_can_have_zipcodes_associated(): void
    {
        $market = Market::factory()->create();
        $postalCode1 = PostalCode::factory()->withPostalCode('12345')->create();
        $postalCode2 = PostalCode::factory()->withPostalCode('54321')->create();

        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '54321',
        ]);

        $this->assertCount(2, $market->zipcodes);
        $this->assertTrue($market->hasZipcode('12345'));
        $this->assertTrue($market->hasZipcode('54321'));
        $this->assertFalse($market->hasZipcode('99999'));
    }

    #[Test]
    public function non_admin_cannot_access_market_management(): void
    {
        $user = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $response = $this->actingAs($user)->get('/admin/markets');

        $response->assertStatus(403);
    }

    #[Test]
    public function market_creation_requires_name(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        Livewire::actingAs($admin)
            ->test(MarketIndex::class)
            ->set('name', '')
            ->set('description', 'Test market')
            ->call('createMarket')
            ->assertHasErrors(['name']);

        $this->assertDatabaseMissing('markets', [
            'description' => 'Test market',
        ]);
    }

    #[Test]
    public function market_lists_show_zipcode_counts(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $market = Market::factory()->create();
        $postalCode1 = PostalCode::factory()->withPostalCode('11111')->create();
        $postalCode2 = PostalCode::factory()->withPostalCode('22222')->create();
        $postalCode3 = PostalCode::factory()->withPostalCode('33333')->create();

        MarketZipcode::create(['market_id' => $market->id, 'postal_code' => '11111']);
        MarketZipcode::create(['market_id' => $market->id, 'postal_code' => '22222']);
        MarketZipcode::create(['market_id' => $market->id, 'postal_code' => '33333']);

        $response = Livewire::actingAs($admin)
            ->test(MarketIndex::class);

        // The component should load markets with zipcode counts
        $markets = $response->viewData('markets');
        $loadedMarket = $markets->first();

        $this->assertEquals(3, $loadedMarket->zipcodes_count);
    }

    #[Test]
    public function deleting_market_removes_associated_zipcodes(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $market = Market::factory()->create();
        $postalCode = PostalCode::factory()->withPostalCode('12345')->create();

        MarketZipcode::create([
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        $this->assertDatabaseHas('market_zipcodes', [
            'market_id' => $market->id,
            'postal_code' => '12345',
        ]);

        Livewire::actingAs($admin)
            ->test(MarketIndex::class)
            ->call('deleteMarket', $market->id);

        $this->assertDatabaseMissing('market_zipcodes', [
            'market_id' => $market->id,
        ]);
    }

    #[Test]
    public function market_zipcode_can_check_if_in_active_market(): void
    {
        $activeMarket = Market::factory()->active()->create();
        $inactiveMarket = Market::factory()->inactive()->create();

        $postalCode1 = PostalCode::factory()->withPostalCode('12345')->create();
        $postalCode2 = PostalCode::factory()->withPostalCode('54321')->create();
        $postalCode3 = PostalCode::factory()->withPostalCode('99999')->create();

        MarketZipcode::create([
            'market_id' => $activeMarket->id,
            'postal_code' => '12345',
        ]);

        MarketZipcode::create([
            'market_id' => $inactiveMarket->id,
            'postal_code' => '54321',
        ]);

        $this->assertTrue(MarketZipcode::isInActiveMarket('12345'));
        $this->assertFalse(MarketZipcode::isInActiveMarket('54321')); // Inactive market
        $this->assertFalse(MarketZipcode::isInActiveMarket('99999')); // No market
    }
}
