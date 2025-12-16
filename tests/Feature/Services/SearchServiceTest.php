<?php

namespace Tests\Feature\Services;

use App\Models\Influencer;
use App\Models\InfluencerSocial;
use App\Models\PostalCode;
use App\Models\SavedUser;
use App\Models\User;
use App\Services\SearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $businessUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->businessUser = User::factory()->business()->withProfile()->create();
    }

    // ==================== Basic Search Tests ====================

    #[Test]
    public function search_returns_only_completed_influencers(): void
    {
        // Create completed influencer
        $completedInfluencer = User::factory()->influencer()->withProfile()->create();

        // Create incomplete influencer (no profile/onboarding)
        $incompleteUser = User::factory()->influencer()->create();
        Influencer::factory()->create([
            'user_id' => $incompleteUser->id,
            'onboarding_complete' => false,
        ]);

        $results = SearchService::searchInfluencers([], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($completedInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_excludes_current_user(): void
    {
        // Create a business user who is also an influencer (edge case)
        $dualUser = User::factory()->influencer()->withProfile()->create();

        // Create another influencer
        $otherInfluencer = User::factory()->influencer()->withProfile()->create();

        $results = SearchService::searchInfluencers([], $dualUser);

        // Should not include the searching user
        $userIds = $results->pluck('user_id')->toArray();
        $this->assertNotContains($dualUser->id, $userIds);
    }

    #[Test]
    public function search_returns_paginated_results(): void
    {
        // Create 15 influencers
        for ($i = 0; $i < 15; $i++) {
            User::factory()->influencer()->withProfile()->create();
        }

        $results = SearchService::searchInfluencers([], $this->businessUser, perPage: 10);

        $this->assertCount(10, $results);
        $this->assertEquals(15, $results->total());
        $this->assertEquals(2, $results->lastPage());
    }

    // ==================== Text Search Tests ====================

    #[Test]
    public function search_filters_by_username(): void
    {
        $targetInfluencer = User::factory()->influencer()->withProfile()->create();
        $targetInfluencer->influencer->update(['username' => 'unique_fashion_blogger']);

        $otherInfluencer = User::factory()->influencer()->withProfile()->create();
        $otherInfluencer->influencer->update(['username' => 'food_lover']);

        $results = SearchService::searchInfluencers(['search' => 'fashion'], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals('unique_fashion_blogger', $results->first()->username);
    }

    #[Test]
    public function search_filters_by_bio(): void
    {
        $targetInfluencer = User::factory()->influencer()->withProfile()->create();
        $targetInfluencer->influencer->update(['bio' => 'I love creating travel content and exploring new places']);

        $otherInfluencer = User::factory()->influencer()->withProfile()->create();
        $otherInfluencer->influencer->update(['bio' => 'Food photography is my passion']);

        $results = SearchService::searchInfluencers(['search' => 'travel'], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertStringContainsString('travel', $results->first()->bio);
    }

    #[Test]
    public function search_filters_by_user_name(): void
    {
        $targetInfluencer = User::factory()->influencer()->withProfile()->create([
            'name' => 'Sarah Johnson',
        ]);

        $otherInfluencer = User::factory()->influencer()->withProfile()->create([
            'name' => 'Mike Smith',
        ]);

        $results = SearchService::searchInfluencers(['search' => 'Sarah'], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($targetInfluencer->id, $results->first()->user_id);
    }

    // ==================== Location Filter Tests ====================

    #[Test]
    public function search_filters_by_zip_code_proximity(): void
    {
        // Create postal codes with known coordinates
        $centerZip = PostalCode::factory()->withPostalCode('45066')->withCoordinates(39.5528, -84.2314)->create();
        $nearbyZip = PostalCode::factory()->withPostalCode('45005')->withCoordinates(39.5581, -84.2853)->create();
        $farZip = PostalCode::factory()->withPostalCode('90210')->withCoordinates(34.0901, -118.4065)->create();

        // Create influencers at these locations
        $nearbyInfluencer = User::factory()->influencer()->withProfile()->create();
        $nearbyInfluencer->influencer->update(['postal_code' => '45005']);

        $farInfluencer = User::factory()->influencer()->withProfile()->create();
        $farInfluencer->influencer->update(['postal_code' => '90210']);

        $results = SearchService::searchInfluencers([
            'location' => '45066',
            'searchRadius' => 50,
        ], $this->businessUser);

        // Only nearby influencer should be returned
        $postalCodes = $results->pluck('postal_code')->toArray();
        $this->assertContains('45005', $postalCodes);
        $this->assertNotContains('90210', $postalCodes);
    }

    #[Test]
    public function search_falls_back_to_text_search_for_non_zip_location(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $influencer->influencer->update(['postal_code' => '45066']);

        // Search with partial zip code (not 5 digits)
        $results = SearchService::searchInfluencers([
            'location' => '450',
        ], $this->businessUser);

        // Should find via text search on postal_code
        $this->assertCount(1, $results);
    }

    // ==================== Niche/Content Type Filter Tests ====================

    #[Test]
    public function search_filters_by_single_niche(): void
    {
        $fashionInfluencer = User::factory()->influencer()->withProfile()->create();
        $fashionInfluencer->influencer->update(['content_types' => ['fashion', 'lifestyle']]);

        $foodInfluencer = User::factory()->influencer()->withProfile()->create();
        $foodInfluencer->influencer->update(['content_types' => ['food', 'cooking']]);

        $results = SearchService::searchInfluencers([
            'selectedNiches' => ['fashion'],
        ], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertContains('fashion', $results->first()->content_types);
    }

    #[Test]
    public function search_filters_by_multiple_niches_with_or_logic(): void
    {
        $fashionInfluencer = User::factory()->influencer()->withProfile()->create();
        $fashionInfluencer->influencer->update(['content_types' => ['fashion']]);

        $foodInfluencer = User::factory()->influencer()->withProfile()->create();
        $foodInfluencer->influencer->update(['content_types' => ['food']]);

        $techInfluencer = User::factory()->influencer()->withProfile()->create();
        $techInfluencer->influencer->update(['content_types' => ['technology']]);

        $results = SearchService::searchInfluencers([
            'selectedNiches' => ['fashion', 'food'],
        ], $this->businessUser);

        // Should return both fashion and food influencers (OR logic)
        $this->assertCount(2, $results);
    }

    // ==================== Platform Filter Tests ====================

    #[Test]
    public function search_filters_by_social_platform(): void
    {
        $instagramInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $instagramInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'insta_user',
            'followers' => 10000,
        ]);

        $tiktokInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $tiktokInfluencer->influencer->id,
            'platform' => 'tiktok',
            'username' => 'tiktok_user',
            'followers' => 5000,
        ]);

        $results = SearchService::searchInfluencers([
            'selectedPlatforms' => ['instagram'],
        ], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($instagramInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_filters_by_multiple_platforms(): void
    {
        $instagramInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $instagramInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'insta_user',
            'followers' => 10000,
        ]);

        $youtubeInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $youtubeInfluencer->influencer->id,
            'platform' => 'youtube',
            'username' => 'youtube_user',
            'followers' => 50000,
        ]);

        $tiktokInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $tiktokInfluencer->influencer->id,
            'platform' => 'tiktok',
            'username' => 'tiktok_user',
            'followers' => 5000,
        ]);

        $results = SearchService::searchInfluencers([
            'selectedPlatforms' => ['instagram', 'youtube'],
        ], $this->businessUser);

        $this->assertCount(2, $results);
    }

    // ==================== Follower Count Filter Tests ====================

    #[Test]
    public function search_filters_by_minimum_followers(): void
    {
        $smallInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $smallInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'small_user',
            'followers' => 500,
        ]);

        $largeInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $largeInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'large_user',
            'followers' => 50000,
        ]);

        $results = SearchService::searchInfluencers([
            'minFollowers' => 10000,
        ], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($largeInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_filters_by_maximum_followers(): void
    {
        $smallInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $smallInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'small_user',
            'followers' => 500,
        ]);

        $largeInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $largeInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'large_user',
            'followers' => 50000,
        ]);

        $results = SearchService::searchInfluencers([
            'maxFollowers' => 5000,
        ], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($smallInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_filters_by_follower_range(): void
    {
        $nanoInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $nanoInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'nano_user',
            'followers' => 500,
        ]);

        $microInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $microInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'micro_user',
            'followers' => 5000,
        ]);

        $macroInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $macroInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'macro_user',
            'followers' => 500000,
        ]);

        $results = SearchService::searchInfluencers([
            'minFollowers' => 1000,
            'maxFollowers' => 100000,
        ], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($microInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_sums_followers_across_all_platforms(): void
    {
        $multiPlatformInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $multiPlatformInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'insta_user',
            'followers' => 3000,
        ]);
        InfluencerSocial::create([
            'influencer_id' => $multiPlatformInfluencer->influencer->id,
            'platform' => 'tiktok',
            'username' => 'tiktok_user',
            'followers' => 4000,
        ]);

        // Total followers: 7000

        $results = SearchService::searchInfluencers([
            'minFollowers' => 5000,
        ], $this->businessUser);

        // Should be included because total (7000) >= 5000
        $this->assertCount(1, $results);
    }

    // ==================== Sorting Tests ====================

    #[Test]
    public function search_sorts_by_newest_first(): void
    {
        $olderInfluencer = User::factory()->influencer()->withProfile()->create();
        $olderInfluencer->influencer->update(['created_at' => now()->subDays(10)]);

        $newerInfluencer = User::factory()->influencer()->withProfile()->create();
        $newerInfluencer->influencer->update(['created_at' => now()]);

        $results = SearchService::searchInfluencers([
            'sortBy' => 'newest',
        ], $this->businessUser);

        $this->assertEquals($newerInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_sorts_by_oldest_first(): void
    {
        $olderInfluencer = User::factory()->influencer()->withProfile()->create();
        $olderInfluencer->influencer->update(['created_at' => now()->subDays(10)]);

        $newerInfluencer = User::factory()->influencer()->withProfile()->create();
        $newerInfluencer->influencer->update(['created_at' => now()]);

        $results = SearchService::searchInfluencers([
            'sortBy' => 'oldest',
        ], $this->businessUser);

        $this->assertEquals($olderInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_sorts_by_most_followers(): void
    {
        $smallInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $smallInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'small_user',
            'followers' => 1000,
        ]);

        $largeInfluencer = User::factory()->influencer()->withProfile()->create();
        InfluencerSocial::create([
            'influencer_id' => $largeInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'large_user',
            'followers' => 100000,
        ]);

        $results = SearchService::searchInfluencers([
            'sortBy' => 'followers',
        ], $this->businessUser);

        $this->assertEquals($largeInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_sorts_by_name(): void
    {
        $zUser = User::factory()->influencer()->withProfile()->create([
            'name' => 'Zara Williams',
        ]);

        $aUser = User::factory()->influencer()->withProfile()->create([
            'name' => 'Alice Brown',
        ]);

        $results = SearchService::searchInfluencers([
            'sortBy' => 'name',
        ], $this->businessUser);

        $this->assertEquals($aUser->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_sorts_by_quality_score(): void
    {
        $lowQualityInfluencer = User::factory()->influencer()->withProfile()->create();
        $lowQualityInfluencer->influencer->update(['content_quality_score' => 50]);

        $highQualityInfluencer = User::factory()->influencer()->withProfile()->create();
        $highQualityInfluencer->influencer->update(['content_quality_score' => 95]);

        $results = SearchService::searchInfluencers([
            'sortBy' => 'quality',
        ], $this->businessUser);

        $this->assertEquals($highQualityInfluencer->influencer->id, $results->first()->id);
    }

    // ==================== Saved/Hidden User Filter Tests ====================

    #[Test]
    public function search_shows_only_saved_users_when_requested(): void
    {
        $savedInfluencer = User::factory()->influencer()->withProfile()->create();
        $unsavedInfluencer = User::factory()->influencer()->withProfile()->create();

        // Save the first influencer (type='saved')
        SavedUser::create([
            'user_id' => $this->businessUser->id,
            'saved_user_id' => $savedInfluencer->id,
            'type' => 'saved',
        ]);

        $results = SearchService::searchInfluencers([
            'showSavedOnly' => true,
        ], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($savedInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_hides_hidden_users_by_default(): void
    {
        $visibleInfluencer = User::factory()->influencer()->withProfile()->create();
        $hiddenInfluencer = User::factory()->influencer()->withProfile()->create();

        // Hide the second influencer (type='hidden')
        SavedUser::create([
            'user_id' => $this->businessUser->id,
            'saved_user_id' => $hiddenInfluencer->id,
            'type' => 'hidden',
        ]);

        $results = SearchService::searchInfluencers([], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($visibleInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_can_show_hidden_users_when_explicitly_requested(): void
    {
        $visibleInfluencer = User::factory()->influencer()->withProfile()->create();
        $hiddenInfluencer = User::factory()->influencer()->withProfile()->create();

        SavedUser::create([
            'user_id' => $this->businessUser->id,
            'saved_user_id' => $hiddenInfluencer->id,
            'type' => 'hidden',
        ]);

        $results = SearchService::searchInfluencers([
            'hideHidden' => false,
        ], $this->businessUser);

        $this->assertCount(2, $results);
    }

    // ==================== Search Metadata Tests ====================

    #[Test]
    public function get_search_metadata_with_valid_zip_code(): void
    {
        PostalCode::factory()->withPostalCode('45066')->withCoordinates(39.5528, -84.2314)->create();

        $metadata = SearchService::getSearchMetadata([
            'location' => '45066',
            'searchRadius' => 50,
        ]);

        $this->assertNotNull($metadata['searchPostalCode']);
        $this->assertEquals('45066', $metadata['searchPostalCode']->postal_code);
        $this->assertIsBool($metadata['isProximitySearch']);
        $this->assertIsInt($metadata['nearbyZipCodesCount']);
    }

    #[Test]
    public function get_search_metadata_with_invalid_zip_code(): void
    {
        $metadata = SearchService::getSearchMetadata([
            'location' => '99999', // Non-existent
            'searchRadius' => 50,
        ]);

        $this->assertNull($metadata['searchPostalCode']);
        $this->assertFalse($metadata['isProximitySearch']);
        $this->assertEquals(0, $metadata['nearbyZipCodesCount']);
    }

    #[Test]
    public function get_search_metadata_without_location(): void
    {
        $metadata = SearchService::getSearchMetadata([]);

        $this->assertNull($metadata['searchPostalCode']);
        $this->assertFalse($metadata['isProximitySearch']);
        $this->assertEquals(0, $metadata['nearbyZipCodesCount']);
    }

    // ==================== Filter Options Tests ====================

    #[Test]
    public function get_filter_options_returns_expected_structure(): void
    {
        $options = SearchService::getFilterOptions();

        $this->assertArrayHasKey('radiusOptions', $options);
        $this->assertArrayHasKey('followerPresets', $options);
        $this->assertArrayHasKey('sortOptions', $options);

        // Verify radius options
        $this->assertNotEmpty($options['radiusOptions']);
        $this->assertArrayHasKey('value', $options['radiusOptions'][0]);
        $this->assertArrayHasKey('label', $options['radiusOptions'][0]);

        // Verify follower presets
        $this->assertNotEmpty($options['followerPresets']);
        $this->assertArrayHasKey('min', $options['followerPresets'][0]);
        $this->assertArrayHasKey('max', $options['followerPresets'][0]);
        $this->assertArrayHasKey('label', $options['followerPresets'][0]);

        // Verify sort options
        $this->assertNotEmpty($options['sortOptions']);
        $this->assertArrayHasKey('value', $options['sortOptions'][0]);
        $this->assertArrayHasKey('label', $options['sortOptions'][0]);
    }

    // ==================== Legacy searchUsers Method Tests ====================

    #[Test]
    public function search_users_returns_user_models_for_business_users(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();

        $results = SearchService::searchUsers([], $this->businessUser);

        // Results should be User models (not Influencer models)
        $this->assertInstanceOf(User::class, $results->first());
        $this->assertEquals($influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_users_includes_influencer_relationship(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();

        $results = SearchService::searchUsers([], $this->businessUser);

        // The influencer relationship should be loaded
        $this->assertTrue($results->first()->relationLoaded('influencer'));
    }

    // ==================== Combined Filter Tests ====================

    #[Test]
    public function search_with_multiple_filters_combined(): void
    {
        // Target influencer: fashion, Instagram, 10k followers, in Ohio
        $targetInfluencer = User::factory()->influencer()->withProfile()->create([
            'name' => 'Fashion Star',
        ]);
        $targetInfluencer->influencer->update([
            'content_types' => ['fashion', 'lifestyle'],
            'postal_code' => '45066',
        ]);
        InfluencerSocial::create([
            'influencer_id' => $targetInfluencer->influencer->id,
            'platform' => 'instagram',
            'username' => 'fashion_star',
            'followers' => 15000,
        ]);

        // Non-matching influencer: food, TikTok, 1k followers
        $otherInfluencer = User::factory()->influencer()->withProfile()->create([
            'name' => 'Food Blogger',
        ]);
        $otherInfluencer->influencer->update([
            'content_types' => ['food', 'cooking'],
            'postal_code' => '90210',
        ]);
        InfluencerSocial::create([
            'influencer_id' => $otherInfluencer->influencer->id,
            'platform' => 'tiktok',
            'username' => 'food_blogger',
            'followers' => 1000,
        ]);

        $results = SearchService::searchInfluencers([
            'search' => 'Fashion',
            'selectedNiches' => ['fashion'],
            'selectedPlatforms' => ['instagram'],
            'minFollowers' => 10000,
        ], $this->businessUser);

        $this->assertCount(1, $results);
        $this->assertEquals($targetInfluencer->influencer->id, $results->first()->id);
    }

    #[Test]
    public function search_with_empty_results(): void
    {
        User::factory()->influencer()->withProfile()->create();

        $results = SearchService::searchInfluencers([
            'search' => 'NonexistentSearchTermThatWillNeverMatch12345',
        ], $this->businessUser);

        $this->assertCount(0, $results);
        $this->assertEquals(0, $results->total());
    }
}
