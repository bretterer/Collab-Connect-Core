<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SearchService;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Models\PostalCode;
use App\Models\SocialMediaAccount;
use App\Enums\AccountType;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\CollaborationGoal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\Test;

class SearchServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PostalCode $postalCode1;
    protected PostalCode $postalCode2;
    protected User $businessUser;
    protected User $influencerUser1;
    protected User $influencerUser2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create postal codes for testing
        $this->postalCode1 = PostalCode::factory()->create([
            'postal_code' => '12345',
            'city' => 'New York',
            'state' => 'NY',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $this->postalCode2 = PostalCode::factory()->create([
            'postal_code' => '90210',
            'city' => 'Beverly Hills',
            'state' => 'CA',
            'latitude' => 34.0901,
            'longitude' => -118.4065,
        ]);

        // Create test users
        $this->businessUser = User::factory()->business()->create([
            'name' => 'Business User',
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

        // Create business profile
        BusinessProfile::factory()->create([
            'user_id' => $this->businessUser->id,
            'company_name' => 'Fashion Company',
            'niches' => [Niche::FASHION->value],
            'postal_code_id' => $this->postalCode1->id,
            'postal_code' => '12345',
            'city' => 'New York',
            'state' => 'NY',
        ]);

        // Create influencer profiles
        InfluencerProfile::factory()->create([
            'user_id' => $this->influencerUser1->id,
            'bio' => 'Fashion and beauty content creator',
            'niches' => [Niche::FASHION->value, Niche::BEAUTY->value],
            'collaboration_goals' => [CollaborationGoal::BRAND_AWARENESS->value],
            'postal_code_id' => $this->postalCode1->id,
            'postal_code' => '12345',
            'city' => 'New York',
            'state' => 'NY',
        ]);

        InfluencerProfile::factory()->create([
            'user_id' => $this->influencerUser2->id,
            'bio' => 'Technology reviewer and gadget enthusiast',
            'niches' => [Niche::TECH->value],
            'collaboration_goals' => [CollaborationGoal::PRODUCT_REVIEWS->value],
            'postal_code_id' => $this->postalCode2->id,
            'postal_code' => '90210',
            'city' => 'Beverly Hills',
            'state' => 'CA',
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
    }

    #[Test]
    public function it_searches_users_without_criteria()
    {
        $results = SearchService::searchUsers([]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(3, $results->count());

        // Should include all users with profiles
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->businessUser->id, $userIds);
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertContains($this->influencerUser2->id, $userIds);
    }

    #[Test]
    public function it_searches_users_by_search_term()
    {
        $results = SearchService::searchUsers(['search' => 'Fashion']);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Should include users with "Fashion" in name, bio, or company name
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->businessUser->id, $userIds); // Company name: Fashion Company
        $this->assertContains($this->influencerUser1->id, $userIds); // Name: Fashion Influencer
    }

    #[Test]
    public function it_searches_users_by_location()
    {
        $results = SearchService::searchUsers(['location' => 'New York']);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Should include users in New York
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->businessUser->id, $userIds);
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertNotContains($this->influencerUser2->id, $userIds);
    }

    #[Test]
    public function it_searches_users_by_niche()
    {
        $results = SearchService::searchUsers(['niches' => [Niche::FASHION->value]]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Should include users with Fashion niche
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->businessUser->id, $userIds);
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertNotContains($this->influencerUser2->id, $userIds);
    }

    #[Test]
    public function it_searches_users_by_collaboration_goal()
    {
        $results = SearchService::searchUsers(['collaboration_goals' => [CollaborationGoal::BRAND_AWARENESS->value]]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(1, $results->count());

        // Should include users with Brand Awareness goal
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertNotContains($this->influencerUser2->id, $userIds);
    }

    #[Test]
    public function it_searches_users_by_account_type()
    {
        $results = SearchService::searchUsers(['account_type' => AccountType::INFLUENCER->value]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Should include only influencers
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertContains($this->influencerUser2->id, $userIds);
        $this->assertNotContains($this->businessUser->id, $userIds);
    }

    #[Test]
    public function it_searches_users_by_social_platform()
    {
        $results = SearchService::searchUsers(['social_platforms' => [SocialPlatform::INSTAGRAM->value]]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(1, $results->count());

        // Should include users with Instagram accounts
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertNotContains($this->influencerUser2->id, $userIds);
    }

    #[Test]
    public function it_searches_users_by_follower_count_range()
    {
        $results = SearchService::searchUsers([
            'min_followers' => 60000,
            'max_followers' => 150000,
        ]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(1, $results->count());

        // Should include users with follower count in range
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->influencerUser2->id, $userIds); // 100k followers
        $this->assertNotContains($this->influencerUser1->id, $userIds); // 50k followers
    }

    #[Test]
    public function it_searches_users_by_proximity()
    {
        $results = SearchService::searchUsers([
            'postal_code' => '12345',
            'radius' => 50, // 50 miles
        ]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Should include users within 50 miles of postal code 12345
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->businessUser->id, $userIds);
        $this->assertContains($this->influencerUser1->id, $userIds);
        // User 2 is in CA, should not be included
        $this->assertNotContains($this->influencerUser2->id, $userIds);
    }

    #[Test]
    public function it_searches_users_with_multiple_criteria()
    {
        $results = SearchService::searchUsers([
            'search' => 'Fashion',
            'account_type' => AccountType::INFLUENCER->value,
            'niches' => [Niche::FASHION->value],
            'location' => 'New York',
        ]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(1, $results->count());

        // Should include only influencers matching all criteria
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertNotContains($this->businessUser->id, $userIds); // Not influencer
        $this->assertNotContains($this->influencerUser2->id, $userIds); // Wrong niche and location
    }

    #[Test]
    public function it_sorts_results_by_relevance_by_default()
    {
        $results = SearchService::searchUsers(['search' => 'Fashion']);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Results should be ordered by relevance (created_at desc by default)
        $firstResult = $results->first();
        $this->assertNotNull($firstResult);
    }

    #[Test]
    public function it_sorts_results_by_follower_count()
    {
        $results = SearchService::searchUsers([
            'account_type' => AccountType::INFLUENCER->value,
            'sort' => 'followers',
        ]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Should be sorted by follower count (highest first)
        $firstUser = $results->first();
        $lastUser = $results->last();

        $firstUserFollowers = $firstUser->socialMediaAccounts->sum('follower_count');
        $lastUserFollowers = $lastUser->socialMediaAccounts->sum('follower_count');

        $this->assertGreaterThanOrEqual($lastUserFollowers, $firstUserFollowers);
    }

    #[Test]
    public function it_sorts_results_by_distance()
    {
        $results = SearchService::searchUsers([
            'postal_code' => '12345',
            'radius' => 3000, // Large radius to include all users
            'sort' => 'distance',
        ]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(2, $results->count());

        // Results should be sorted by distance from postal code
        $firstResult = $results->first();
        $this->assertNotNull($firstResult);

        // First result should be closest to postal code 12345
        $this->assertContains($firstResult->id, [$this->businessUser->id, $this->influencerUser1->id]);
    }

    #[Test]
    public function it_gets_search_metadata()
    {
        $criteria = [
            'search' => 'Fashion',
            'location' => 'New York',
            'niches' => [Niche::FASHION->value],
        ];

        $metadata = SearchService::getSearchMetadata($criteria);

        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('total_results', $metadata);
        $this->assertArrayHasKey('filters_applied', $metadata);
        $this->assertArrayHasKey('search_term', $metadata);
        $this->assertArrayHasKey('location', $metadata);
        $this->assertArrayHasKey('niches', $metadata);

        $this->assertIsInt($metadata['total_results']);
        $this->assertIsArray($metadata['filters_applied']);
        $this->assertEquals('Fashion', $metadata['search_term']);
        $this->assertEquals('New York', $metadata['location']);
        $this->assertEquals([Niche::FASHION->value], $metadata['niches']);

        $this->assertContains('search', $metadata['filters_applied']);
        $this->assertContains('location', $metadata['filters_applied']);
        $this->assertContains('niches', $metadata['filters_applied']);
    }

    #[Test]
    public function it_handles_empty_search_criteria()
    {
        $results = SearchService::searchUsers([]);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertGreaterThanOrEqual(3, $results->count());

        // Should return all users with profiles
        $userIds = $results->pluck('id')->toArray();
        $this->assertContains($this->businessUser->id, $userIds);
        $this->assertContains($this->influencerUser1->id, $userIds);
        $this->assertContains($this->influencerUser2->id, $userIds);
    }

    #[Test]
    public function it_handles_invalid_postal_code_for_proximity_search()
    {
        $results = SearchService::searchUsers([
            'postal_code' => '99999', // Non-existent postal code
            'radius' => 50,
        ]);

        $this->assertInstanceOf(Collection::class, $results);

        // Should return all users when postal code is invalid
        $this->assertGreaterThanOrEqual(3, $results->count());
    }

    #[Test]
    public function it_handles_missing_radius_for_proximity_search()
    {
        $results = SearchService::searchUsers([
            'postal_code' => '12345',
            // Missing radius
        ]);

        $this->assertInstanceOf(Collection::class, $results);

        // Should return all users when radius is missing
        $this->assertGreaterThanOrEqual(3, $results->count());
    }

    #[Test]
    public function it_applies_search_filter_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applySearchFilter');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, 'Fashion');

        $this->assertSame($query, $result);

        // Execute query to test the filter
        $users = $result->get();
        $this->assertGreaterThanOrEqual(1, $users->count());
    }

    #[Test]
    public function it_applies_location_filter_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applyLocationFilter');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, 'New York');

        $this->assertSame($query, $result);

        // Execute query to test the filter
        $users = $result->get();
        $this->assertGreaterThanOrEqual(1, $users->count());
    }

    #[Test]
    public function it_applies_niche_filter_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applyNicheFilter');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, [Niche::FASHION->value]);

        $this->assertSame($query, $result);

        // Execute query to test the filter
        $users = $result->get();
        $this->assertGreaterThanOrEqual(1, $users->count());
    }

    #[Test]
    public function it_applies_account_type_filter_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applyAccountTypeFilter');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, AccountType::INFLUENCER->value);

        $this->assertSame($query, $result);

        // Execute query to test the filter
        $users = $result->get();
        $this->assertGreaterThanOrEqual(1, $users->count());

        // All results should be influencers
        foreach ($users as $user) {
            $this->assertEquals(AccountType::INFLUENCER->value, $user->account_type);
        }
    }

    #[Test]
    public function it_applies_social_platform_filter_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applySocialPlatformFilter');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, [SocialPlatform::INSTAGRAM->value]);

        $this->assertSame($query, $result);

        // Execute query to test the filter
        $users = $result->get();
        $this->assertGreaterThanOrEqual(1, $users->count());
    }

    #[Test]
    public function it_applies_follower_count_filter_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applyFollowerCountFilter');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, 60000, 150000);

        $this->assertSame($query, $result);

        // Execute query to test the filter
        $users = $result->get();
        $this->assertGreaterThanOrEqual(1, $users->count());
    }

    #[Test]
    public function it_applies_proximity_filter_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applyProximityFilter');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, '12345', 50);

        $this->assertSame($query, $result);

        // Execute query to test the filter
        $users = $result->get();
        $this->assertGreaterThanOrEqual(1, $users->count());
    }

    #[Test]
    public function it_applies_sorting_correctly()
    {
        $query = User::query();
        $method = new \ReflectionMethod(SearchService::class, 'applySorting');
        $method->setAccessible(true);

        $result = $method->invoke(null, $query, 'followers');

        $this->assertSame($query, $result);

        // Test different sort options
        $relevanceQuery = User::query();
        $relevanceResult = $method->invoke(null, $relevanceQuery, 'relevance');
        $this->assertSame($relevanceQuery, $relevanceResult);

        $distanceQuery = User::query();
        $distanceResult = $method->invoke(null, $distanceQuery, 'distance');
        $this->assertSame($distanceQuery, $distanceResult);
    }
}