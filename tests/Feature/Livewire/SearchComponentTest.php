<?php

namespace Tests\Feature\Livewire;

use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Livewire\Search;
use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Models\PostalCode;
use App\Models\SocialMediaAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchComponentTest extends TestCase
{
    use RefreshDatabase;

    protected PostalCode $postalCode1;

    protected PostalCode $postalCode2;

    protected PostalCode $postalCode3;

    protected User $businessUser;

    protected User $influencerUser1;

    protected User $influencerUser2;

    protected User $influencerUser3;

    protected function setUp(): void
    {
        parent::setUp();

        // Create postal codes
        $this->postalCode1 = PostalCode::factory()->create([
            'postal_code' => '12345',
            'place_name' => 'New York',
            'admin_name1' => 'NY',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $this->postalCode2 = PostalCode::factory()->create([
            'postal_code' => '90210',
            'place_name' => 'Beverly Hills',
            'admin_name1' => 'CA',
            'latitude' => 34.0901,
            'longitude' => -118.4065,
        ]);

        $this->postalCode3 = PostalCode::factory()->create([
            'postal_code' => '43210',
            'place_name' => 'Buffalo',
            'admin_name1' => 'NY',
            'latitude' => 42.8864,
            'longitude' => -78.8784,
        ]);

        // Create test users
        $this->businessUser = User::factory()->business()->create([
            'name' => 'Fashion Business',
            'email' => 'business@test.com',
        ]);

        $this->influencerUser1 = User::factory()->influencer()->create([
            'name' => 'Fashion Influencer',
            'email' => 'fashion@test.com',
        ]);

        $this->influencerUser2 = User::factory()->influencer()->create([
            'name' => 'Tech Influencer',
            'email' => 'tech@test.com',
        ]);

        $this->influencerUser3 = User::factory()->influencer()->create([
            'name' => 'Buffalo Influencer',
            'email' => 'buffalo@test.com',
        ]);

        // Create profiles
        BusinessProfile::factory()->create([
            'user_id' => $this->businessUser->id,
            'business_name' => 'Fashion Company',
            'industry' => Niche::FASHION->value,
            'primary_zip_code' => '12345',
        ]);

        InfluencerProfile::factory()->create([
            'user_id' => $this->influencerUser1->id,
            'creator_name' => 'Fashion and beauty content creator',
            'primary_niche' => Niche::FASHION->value,
            'collaboration_preferences' => [CollaborationGoal::BRAND_AWARENESS->value],
            'primary_zip_code' => '12345',
        ]);

        InfluencerProfile::factory()->create([
            'user_id' => $this->influencerUser2->id,
            'creator_name' => 'Technology reviewer and gadget enthusiast',
            'primary_niche' => Niche::TECHNOLOGY->value,
            'collaboration_preferences' => [CollaborationGoal::PRODUCT_LAUNCHES->value],
            'primary_zip_code' => '90210',
        ]);

        InfluencerProfile::factory()->create([
            'user_id' => $this->influencerUser3->id,
            'creator_name' => 'Buffalo influencer',
            'primary_niche' => Niche::FOOD->value,
            'collaboration_preferences' => [CollaborationGoal::CUSTOMER_ACQUISITION->value],
            'primary_zip_code' => '43210',
        ]);

        // Create social media accounts
        SocialMediaAccount::factory()->create([
            'user_id' => $this->influencerUser1->id,
            'platform' => SocialPlatform::INSTAGRAM->value,
            'username' => 'fashion_influencer',
            'follower_count' => 50000,
            'is_primary' => true,
        ]);

        SocialMediaAccount::factory()->create([
            'user_id' => $this->influencerUser2->id,
            'platform' => SocialPlatform::YOUTUBE->value,
            'username' => 'tech_reviewer',
            'follower_count' => 100000,
            'is_primary' => true,
        ]);

        SocialMediaAccount::factory()->create([
            'user_id' => $this->influencerUser3->id,
            'platform' => SocialPlatform::TIKTOK->value,
            'username' => 'buffalo_influencer',
            'follower_count' => 100000,
            'is_primary' => true,
        ]);
    }

    #[Test]
    public function search_component_renders_correctly()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->assertSuccessful();
        $component->assertSee('Search');
        $component->assertSee('Filter');
    }

    #[Test]
    public function search_component_displays_all_users_initially()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->assertSuccessful();
        $component->assertViewHas('results');

        $users = $component->viewData('results');
        $this->assertEquals(3, $users->count());
    }

    #[Test]
    public function search_component_filters_by_search_term()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('search', 'Fashion');

        $users = $component->viewData('results');
        $this->assertEquals(1, $users->count());

        // Should include users with "Fashion" in their name, bio, or company name
        $userNames = $users->pluck('name')->toArray();
        $this->assertContains('Fashion Influencer', $userNames);
    }

    #[Test]
    public function search_component_filters_by_niche()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('selectedNiches', [Niche::FASHION->value]);

        $users = $component->viewData('results');
        $this->assertEquals(1, $users->count());

        // Should include users with Fashion niche
        $userNames = $users->pluck('name')->toArray();
        $this->assertContains('Fashion Influencer', $userNames);
        $this->assertNotContains('Tech Influencer', $userNames);
    }

    #[Test]
    public function search_component_filters_by_social_platform()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('selectedPlatforms', [SocialPlatform::INSTAGRAM->value]);

        $users = $component->viewData('results');
        $this->assertEquals(1, $users->count());

        // Should include users with Instagram accounts
        $userNames = $users->pluck('name')->toArray();
        $this->assertContains('Fashion Influencer', $userNames);
        $this->assertNotContains('Tech Influencer', $userNames);
    }

    #[Test]
    public function search_component_filters_by_follower_count_range()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('minFollowers', 60000);
        $component->set('maxFollowers', 150000);

        $users = $component->viewData('results');
        $this->assertEquals(2, $users->count());

        // Should include users with follower count in range
        $userNames = $users->pluck('name')->toArray();
        $this->assertContains('Tech Influencer', $userNames);
        $this->assertNotContains('Fashion Influencer', $userNames);
    }

    #[Test]
    public function search_component_filters_by_proximity()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('location', '12345');
        $component->set('searchRadius', 50);

        $users = $component->viewData('results');
        $this->assertEquals(1, $users->count());

        // Should include users within 50 miles of postal code 12345
        $userNames = $users->pluck('name')->toArray();
        $this->assertContains('Fashion Influencer', $userNames);
        $this->assertNotContains('Tech Influencer', $userNames);
    }

    #[Test]
    public function search_component_combines_multiple_filters()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('search', 'Fashion');
        $component->set('selectedNiches', [Niche::FASHION->value]);
        $component->set('location', '12345');
        $component->set('searchRadius', 50);

        $users = $component->viewData('results');
        $this->assertEquals(1, $users->count());

        // Should include only influencers matching all criteria
        $userNames = $users->pluck('name')->toArray();
        $this->assertContains('Fashion Influencer', $userNames);
        $this->assertNotContains('Fashion Business', $userNames);
        $this->assertNotContains('Tech Influencer', $userNames);
    }

    #[Test]
    public function search_component_can_clear_filters()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        // Set some filters
        $component->set('search', 'Fashion');
        $component->set('location', 'New York');
        $component->set('selectedNiches', [Niche::FASHION->value]);

        $component->call('clearFilters');

        $component->assertSet('search', '');
        $component->assertSet('location', '');
        $component->assertSet('selectedNiches', []);
        $component->assertSet('selectedPlatforms', []);
        $component->assertSet('minFollowers', null);
        $component->assertSet('maxFollowers', null);
        $component->assertSet('location', '');
        $component->assertSet('searchRadius', 50);
    }

    #[Test]
    public function search_component_handles_empty_results_business()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('search', 'NonExistentTerm12345');

        $users = $component->viewData('results');
        $this->assertEquals(0, $users->count());

        $component->assertSee('No influencers found');
    }

    #[Test]
    public function search_component_handles_empty_results_influencer()
    {
        $component = Livewire::actingAs($this->influencerUser1)->test(Search::class);

        $component->set('search', 'NonExistentTerm12345');

        $users = $component->viewData('results');
        $this->assertEquals(0, $users->count());

        $component->assertSee('No businesses found');
    }

    #[Test]
    public function search_component_handles_invalid_postal_code()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('location', '99999');
        $component->set('searchRadius', 50);

        // Should not error, but should return all users
        $users = $component->viewData('results');
        $this->assertEquals(0, $users->count());
    }

    #[Test]
    public function search_component_preserves_filters_between_searches()
    {
        $component = Livewire::actingAs($this->businessUser)->test(Search::class);

        $component->set('search', 'Fashion');
        $component->set('location', 'New York');

        // Filters should be preserved
        $component->assertSet('search', 'Fashion');
        $component->assertSet('location', 'New York');
    }
}
