<?php

namespace Tests\Unit\Factories;

use App\Enums\AccountType;
use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\SubscriptionPlan;
use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Models\PostalCode;
use App\Models\SocialMediaAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_factory_creates_valid_user()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertInstanceOf(AccountType::class, $user->account_type);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    #[Test]
    public function user_factory_business_state_creates_business_user()
    {
        $user = User::factory()->business()->create();

        $this->assertEquals(AccountType::BUSINESS, $user->account_type);
        $this->assertTrue($user->isBusinessAccount());
    }

    #[Test]
    public function user_factory_influencer_state_creates_influencer_user()
    {
        $user = User::factory()->influencer()->create();

        $this->assertEquals(AccountType::INFLUENCER, $user->account_type);
        $this->assertTrue($user->isInfluencerAccount());
    }

    #[Test]
    public function user_factory_admin_state_creates_admin_user()
    {
        $user = User::factory()->admin()->create();

        $this->assertEquals(AccountType::ADMIN, $user->account_type);
    }

    #[Test]
    public function user_factory_with_profile_creates_business_profile()
    {
        $user = User::factory()->business()->withProfile()->create();

        $this->assertNotNull($user->businessProfile);
        $this->assertInstanceOf(BusinessProfile::class, $user->businessProfile);
        $this->assertEquals($user->id, $user->businessProfile->user_id);
    }

    #[Test]
    public function user_factory_with_profile_creates_influencer_profile()
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->assertNotNull($user->influencerProfile);
        $this->assertInstanceOf(InfluencerProfile::class, $user->influencerProfile);
        $this->assertEquals($user->id, $user->influencerProfile->user_id);
    }

    #[Test]
    public function business_profile_factory_creates_valid_profile()
    {
        $user = User::factory()->business()->create();
        $profile = BusinessProfile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BusinessProfile::class, $profile);
        $this->assertNotNull($profile->business_name);
        $this->assertNotNull($profile->industry);
        $this->assertNotNull($profile->primary_zip_code);
        $this->assertIsNumeric($profile->location_count);
        $this->assertIsArray($profile->websites);
        $this->assertIsArray($profile->campaign_types);
        $this->assertIsArray($profile->collaboration_goals);
        $this->assertNotNull($profile->subscription_plan);
        $this->assertNotNull($profile->contact_name);
        $this->assertNotNull($profile->contact_email);
        $this->assertEquals($user->id, $profile->user_id);
    }

    #[Test]
    public function influencer_profile_factory_creates_valid_profile()
    {
        $user = User::factory()->influencer()->create();
        $profile = InfluencerProfile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(InfluencerProfile::class, $profile);
        $this->assertNotNull($profile->creator_name);
        $this->assertNotNull($profile->primary_niche);
        $this->assertNotNull($profile->primary_zip_code);
        $this->assertIsArray($profile->collaboration_preferences);
        $this->assertIsArray($profile->preferred_brands);
        $this->assertNotNull($profile->subscription_plan);
        $this->assertEquals($user->id, $profile->user_id);
    }


    #[Test]
    public function social_media_account_factory_creates_valid_account()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(SocialMediaAccount::class, $account);
        $this->assertInstanceOf(SocialPlatform::class, $account->platform);
        $this->assertNotNull($account->username);
        $this->assertIsInt($account->follower_count);
        $this->assertGreaterThanOrEqual(0, $account->follower_count);
        $this->assertIsBool($account->is_primary);
        $this->assertEquals($user->id, $account->user_id);
    }

    #[Test]
    public function social_media_account_factory_instagram_state_works()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->instagram()->create(['user_id' => $user->id]);

        $this->assertEquals(SocialPlatform::INSTAGRAM, $account->platform);
    }

    #[Test]
    public function social_media_account_factory_tiktok_state_works()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->tiktok()->create(['user_id' => $user->id]);

        $this->assertEquals(SocialPlatform::TIKTOK, $account->platform);
    }

    #[Test]
    public function social_media_account_factory_youtube_state_works()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->youtube()->create(['user_id' => $user->id]);

        $this->assertEquals(SocialPlatform::YOUTUBE, $account->platform);
    }

    #[Test]
    public function social_media_account_factory_twitter_state_works()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->twitter()->create(['user_id' => $user->id]);

        $this->assertEquals(SocialPlatform::TWITTER, $account->platform);
    }

    #[Test]
    public function social_media_account_factory_facebook_state_works()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->facebook()->create(['user_id' => $user->id]);

        $this->assertEquals(SocialPlatform::FACEBOOK, $account->platform);
    }

    #[Test]
    public function social_media_account_factory_linkedin_state_works()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->linkedin()->create(['user_id' => $user->id]);

        $this->assertEquals(SocialPlatform::LINKEDIN, $account->platform);
    }

    #[Test]
    public function social_media_account_factory_primary_state_works()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->primary()->create(['user_id' => $user->id]);

        $this->assertTrue($account->is_primary);
    }

    #[Test]
    public function postal_code_factory_creates_valid_postal_code()
    {
        $postalCode = PostalCode::factory()->create();

        $this->assertInstanceOf(PostalCode::class, $postalCode);
        $this->assertNotNull($postalCode->postal_code);
        $this->assertNotNull($postalCode->place_name);
        $this->assertNotNull($postalCode->admin_name1);
        $this->assertIsFloat($postalCode->latitude);
        $this->assertIsFloat($postalCode->longitude);
        $this->assertGreaterThanOrEqual(-90, $postalCode->latitude);
        $this->assertLessThanOrEqual(90, $postalCode->latitude);
        $this->assertGreaterThanOrEqual(-180, $postalCode->longitude);
        $this->assertLessThanOrEqual(180, $postalCode->longitude);
    }

    #[Test]
    public function factories_can_create_multiple_instances()
    {
        $users = User::factory()->count(5)->create();
        $this->assertCount(5, $users);

        $businessProfiles = BusinessProfile::factory()->count(3)->create();
        $this->assertCount(3, $businessProfiles);

        $influencerProfiles = InfluencerProfile::factory()->count(3)->create();
        $this->assertCount(3, $influencerProfiles);

        $socialAccounts = SocialMediaAccount::factory()->count(10)->create();
        $this->assertCount(10, $socialAccounts);

        $postalCodes = PostalCode::factory()->count(100)->create();
        $this->assertCount(100, $postalCodes);
    }

    #[Test]
    public function factories_can_override_attributes()
    {
        $user = User::factory()->create([
            'name' => 'Custom Name',
            'email' => 'custom@example.com',
            'account_type' => AccountType::ADMIN,
        ]);

        $this->assertEquals('Custom Name', $user->name);
        $this->assertEquals('custom@example.com', $user->email);
        $this->assertEquals(AccountType::ADMIN, $user->account_type);
    }

    #[Test]
    public function business_profile_factory_can_override_attributes()
    {
        $user = User::factory()->business()->create();
        $profile = BusinessProfile::factory()->create([
            'user_id' => $user->id,
            'company_name' => 'Custom Company',
            'marketing_budget' => 50000,
            'niches' => [Niche::TECH->value],
            'campaign_types' => [CampaignType::SPONSORED_POST->value],
        ]);

        $this->assertEquals('Custom Company', $profile->company_name);
        $this->assertEquals(50000, $profile->marketing_budget);
        $this->assertEquals([Niche::TECH->value], $profile->niches);
        $this->assertEquals([CampaignType::SPONSORED_POST->value], $profile->campaign_types);
    }

    #[Test]
    public function influencer_profile_factory_can_override_attributes()
    {
        $user = User::factory()->influencer()->create();
        $profile = InfluencerProfile::factory()->create([
            'user_id' => $user->id,
            'bio' => 'Custom bio',
            'niches' => [Niche::BEAUTY->value],
            'collaboration_goals' => [CollaborationGoal::PRODUCT_REVIEWS->value],
        ]);

        $this->assertEquals('Custom bio', $profile->bio);
        $this->assertEquals([Niche::BEAUTY->value], $profile->niches);
        $this->assertEquals([CollaborationGoal::PRODUCT_REVIEWS->value], $profile->collaboration_goals);
    }

    #[Test]
    public function social_media_account_factory_can_override_attributes()
    {
        $user = User::factory()->influencer()->create();
        $account = SocialMediaAccount::factory()->create([
            'user_id' => $user->id,
            'platform' => SocialPlatform::INSTAGRAM,
            'username' => 'custom_username',
            'follower_count' => 100000,
            'is_primary' => true,
        ]);

        $this->assertEquals(SocialPlatform::INSTAGRAM, $account->platform);
        $this->assertEquals('custom_username', $account->username);
        $this->assertEquals(100000, $account->follower_count);
        $this->assertTrue($account->is_primary);
    }

    #[Test]
    public function postal_code_factory_can_override_attributes()
    {
        $postalCode = PostalCode::factory()->create([
            'postal_code' => '90210',
            'city' => 'Beverly Hills',
            'state' => 'CA',
            'latitude' => 34.0901,
            'longitude' => -118.4065,
        ]);

        $this->assertEquals('90210', $postalCode->postal_code);
        $this->assertEquals('Beverly Hills', $postalCode->city);
        $this->assertEquals('CA', $postalCode->state);
        $this->assertEquals(34.0901, $postalCode->latitude);
        $this->assertEquals(-118.4065, $postalCode->longitude);
    }

    #[Test]
    public function factories_generate_unique_emails()
    {
        $users = User::factory()->count(10)->create();
        $emails = $users->pluck('email')->toArray();

        $this->assertCount(10, array_unique($emails));
    }

    #[Test]
    public function factories_generate_realistic_data()
    {
        $businessProfile = BusinessProfile::factory()->create();

        // Check that company name looks realistic
        $this->assertNotEmpty($businessProfile->company_name);
        $this->assertIsString($businessProfile->company_name);

        // Check that marketing budget is reasonable
        $this->assertGreaterThan(0, $businessProfile->marketing_budget);
        $this->assertLessThan(1000000, $businessProfile->marketing_budget);

        // Check that niches are valid enum values
        foreach ($businessProfile->niches as $niche) {
            $this->assertTrue(Niche::isValid($niche));
        }

        // Check that campaign types are valid enum values
        foreach ($businessProfile->campaign_types as $campaignType) {
            $this->assertTrue(CampaignType::isValid($campaignType));
        }
    }

    #[Test]
    public function social_media_account_factory_generates_realistic_follower_counts()
    {
        $accounts = SocialMediaAccount::factory()->count(10)->create();

        foreach ($accounts as $account) {
            $this->assertGreaterThanOrEqual(0, $account->follower_count);
            $this->assertLessThan(10000000, $account->follower_count); // Reasonable upper limit
        }
    }

    #[Test]
    public function factories_respect_relationships()
    {
        $user = User::factory()->business()->create();
        $profile = BusinessProfile::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $profile->user_id);
        $this->assertEquals($profile->id, $user->businessProfile->id);
    }

    #[Test]
    public function factories_can_create_complex_scenarios()
    {
        // Create a business user with profile and multiple social media accounts
        $businessUser = User::factory()->business()->withProfile()->create();

        $socialAccounts = SocialMediaAccount::factory()->count(3)->create([
            'user_id' => $businessUser->id,
        ]);

        // Make one primary
        $socialAccounts->first()->update(['is_primary' => true]);

        $this->assertNotNull($businessUser->businessProfile);
        $this->assertCount(3, $businessUser->socialMediaAccounts);
        $this->assertNotNull($businessUser->getPrimarySocialMediaAccount());

        // Create an influencer user with profile and social media accounts
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        $influencerSocialAccounts = collect([
            SocialMediaAccount::factory()->instagram()->primary()->create(['user_id' => $influencerUser->id]),
            SocialMediaAccount::factory()->tiktok()->create(['user_id' => $influencerUser->id]),
            SocialMediaAccount::factory()->youtube()->create(['user_id' => $influencerUser->id]),
        ]);

        $this->assertNotNull($influencerUser->influencerProfile);
        $this->assertCount(3, $influencerUser->socialMediaAccounts);
        $this->assertEquals(SocialPlatform::INSTAGRAM, $influencerUser->getPrimarySocialMediaAccount()->platform);
    }

    #[Test]
    public function factories_handle_enum_casting_correctly()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(AccountType::class, $user->account_type);

        $businessProfile = BusinessProfile::factory()->create();
        $this->assertInstanceOf(SubscriptionPlan::class, $businessProfile->subscription_plan);

        $influencerProfile = InfluencerProfile::factory()->create();
        $this->assertInstanceOf(SubscriptionPlan::class, $influencerProfile->subscription_plan);

        $socialAccount = SocialMediaAccount::factory()->create();
        $this->assertInstanceOf(SocialPlatform::class, $socialAccount->platform);
    }

    #[Test]
    public function factories_generate_valid_postal_codes()
    {
        $postalCodes = PostalCode::factory()->count(50)->create();

        foreach ($postalCodes as $postalCode) {
            $this->assertMatchesRegularExpression('/^\d{5}$/', $postalCode->postal_code);
            $this->assertNotEmpty($postalCode->city);
            $this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $postalCode->state);
        }
    }

    #[Test]
    public function factories_can_be_used_in_sequences()
    {
        $users = User::factory()->count(3)->sequence(
            ['account_type' => AccountType::BUSINESS],
            ['account_type' => AccountType::INFLUENCER],
            ['account_type' => AccountType::ADMIN]
        )->create();

        $this->assertEquals(AccountType::BUSINESS, $users[0]->account_type);
        $this->assertEquals(AccountType::INFLUENCER, $users[1]->account_type);
        $this->assertEquals(AccountType::ADMIN, $users[2]->account_type);
    }

    #[Test]
    public function factories_work_with_make_method()
    {
        $user = User::factory()->make();
        $this->assertInstanceOf(User::class, $user);
        $this->assertNull($user->id); // Not persisted

        $profile = BusinessProfile::factory()->make();
        $this->assertInstanceOf(BusinessProfile::class, $profile);
        $this->assertNull($profile->id); // Not persisted
    }
}