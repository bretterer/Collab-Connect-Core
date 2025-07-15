<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProfileService;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Models\SocialMediaAccount;
use App\Models\PostalCode;
use App\Enums\AccountType;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected PostalCode $postalCode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $this->postalCode = PostalCode::factory()->create([
            'postal_code' => '12345',
            'city' => 'Test City',
            'state' => 'Test State',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);
    }

    #[Test]
    public function it_creates_business_profile_successfully()
    {
        $profileData = [
            'company_name' => 'Test Company',
            'company_description' => 'A test company description',
            'company_website' => 'https://test.com',
            'target_audience' => 'Young adults',
            'marketing_budget' => 10000,
            'niches' => [Niche::FASHION->value, Niche::BEAUTY->value],
            'campaign_types' => [CampaignType::SPONSORED_POST->value],
            'collaboration_goals' => [CollaborationGoal::BRAND_AWARENESS->value],
            'subscription_plan' => SubscriptionPlan::BASIC->value,
        ];

        $locationData = [
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'US',
        ];

        $profile = ProfileService::createBusinessProfile($this->user, $profileData, $locationData);

        $this->assertInstanceOf(BusinessProfile::class, $profile);
        $this->assertEquals('Test Company', $profile->company_name);
        $this->assertEquals('A test company description', $profile->company_description);
        $this->assertEquals('https://test.com', $profile->company_website);
        $this->assertEquals('Young adults', $profile->target_audience);
        $this->assertEquals(10000, $profile->marketing_budget);
        $this->assertEquals([Niche::FASHION->value, Niche::BEAUTY->value], $profile->niches);
        $this->assertEquals([CampaignType::SPONSORED_POST->value], $profile->campaign_types);
        $this->assertEquals([CollaborationGoal::BRAND_AWARENESS->value], $profile->collaboration_goals);
        $this->assertEquals(SubscriptionPlan::BASIC->value, $profile->subscription_plan);
        $this->assertEquals('123 Test St', $profile->address);
        $this->assertEquals('Test City', $profile->city);
        $this->assertEquals('Test State', $profile->state);
        $this->assertEquals('12345', $profile->postal_code);
        $this->assertEquals('US', $profile->country);
        $this->assertEquals($this->user->id, $profile->user_id);
        $this->assertEquals($this->postalCode->id, $profile->postal_code_id);
    }

    #[Test]
    public function it_creates_influencer_profile_successfully()
    {
        $this->user->update(['account_type' => AccountType::INFLUENCER]);

        $profileData = [
            'bio' => 'Test influencer bio',
            'niches' => [Niche::FASHION->value, Niche::BEAUTY->value],
            'collaboration_goals' => [CollaborationGoal::BRAND_AWARENESS->value],
            'subscription_plan' => SubscriptionPlan::BASIC->value,
        ];

        $locationData = [
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'US',
        ];

        $profile = ProfileService::createInfluencerProfile($this->user, $profileData, $locationData);

        $this->assertInstanceOf(InfluencerProfile::class, $profile);
        $this->assertEquals('Test influencer bio', $profile->bio);
        $this->assertEquals([Niche::FASHION->value, Niche::BEAUTY->value], $profile->niches);
        $this->assertEquals([CollaborationGoal::BRAND_AWARENESS->value], $profile->collaboration_goals);
        $this->assertEquals(SubscriptionPlan::BASIC->value, $profile->subscription_plan);
        $this->assertEquals('123 Test St', $profile->address);
        $this->assertEquals('Test City', $profile->city);
        $this->assertEquals('Test State', $profile->state);
        $this->assertEquals('12345', $profile->postal_code);
        $this->assertEquals('US', $profile->country);
        $this->assertEquals($this->user->id, $profile->user_id);
        $this->assertEquals($this->postalCode->id, $profile->postal_code_id);
    }

    #[Test]
    public function it_creates_social_media_accounts_successfully()
    {
        $socialMediaData = [
            [
                'platform' => SocialPlatform::INSTAGRAM->value,
                'username' => 'test_instagram',
                'follower_count' => 10000,
                'is_primary' => true,
            ],
            [
                'platform' => SocialPlatform::TIKTOK->value,
                'username' => 'test_tiktok',
                'follower_count' => 5000,
                'is_primary' => false,
            ],
        ];

        $accounts = ProfileService::createSocialMediaAccounts($this->user, $socialMediaData);

        $this->assertCount(2, $accounts);

        $instagramAccount = $accounts->where('platform', SocialPlatform::INSTAGRAM->value)->first();
        $this->assertEquals('test_instagram', $instagramAccount->username);
        $this->assertEquals(10000, $instagramAccount->follower_count);
        $this->assertTrue($instagramAccount->is_primary);

        $tiktokAccount = $accounts->where('platform', SocialPlatform::TIKTOK->value)->first();
        $this->assertEquals('test_tiktok', $tiktokAccount->username);
        $this->assertEquals(5000, $tiktokAccount->follower_count);
        $this->assertFalse($tiktokAccount->is_primary);
    }

    #[Test]
    public function it_updates_business_profile_successfully()
    {
        $profile = BusinessProfile::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'company_name' => 'Updated Company Name',
            'company_description' => 'Updated description',
            'marketing_budget' => 20000,
        ];

        $updatedProfile = ProfileService::updateBusinessProfile($profile, $updateData);

        $this->assertEquals('Updated Company Name', $updatedProfile->company_name);
        $this->assertEquals('Updated description', $updatedProfile->company_description);
        $this->assertEquals(20000, $updatedProfile->marketing_budget);
    }

    #[Test]
    public function it_updates_influencer_profile_successfully()
    {
        $profile = InfluencerProfile::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'bio' => 'Updated bio',
            'niches' => [Niche::TECH->value],
        ];

        $updatedProfile = ProfileService::updateInfluencerProfile($profile, $updateData);

        $this->assertEquals('Updated bio', $updatedProfile->bio);
        $this->assertEquals([Niche::TECH->value], $updatedProfile->niches);
    }

    #[Test]
    public function it_checks_if_user_has_completed_onboarding()
    {
        // User without profile
        $this->assertFalse(ProfileService::hasCompletedOnboarding($this->user));

        // Business user with profile
        BusinessProfile::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue(ProfileService::hasCompletedOnboarding($this->user));

        // Influencer user with profile
        $influencerUser = User::factory()->create(['account_type' => AccountType::INFLUENCER]);
        $this->assertFalse(ProfileService::hasCompletedOnboarding($influencerUser));

        InfluencerProfile::factory()->create(['user_id' => $influencerUser->id]);
        $this->assertTrue(ProfileService::hasCompletedOnboarding($influencerUser));
    }

    #[Test]
    public function it_gets_user_profile_for_business_user()
    {
        $businessProfile = BusinessProfile::factory()->create(['user_id' => $this->user->id]);

        $profile = ProfileService::getUserProfile($this->user);

        $this->assertInstanceOf(BusinessProfile::class, $profile);
        $this->assertEquals($businessProfile->id, $profile->id);
    }

    #[Test]
    public function it_gets_user_profile_for_influencer_user()
    {
        $influencerUser = User::factory()->create(['account_type' => AccountType::INFLUENCER]);
        $influencerProfile = InfluencerProfile::factory()->create(['user_id' => $influencerUser->id]);

        $profile = ProfileService::getUserProfile($influencerUser);

        $this->assertInstanceOf(InfluencerProfile::class, $profile);
        $this->assertEquals($influencerProfile->id, $profile->id);
    }

    #[Test]
    public function it_returns_null_for_user_without_profile()
    {
        $userWithoutProfile = User::factory()->create(['account_type' => AccountType::BUSINESS]);

        $profile = ProfileService::getUserProfile($userWithoutProfile);

        $this->assertNull($profile);
    }

    #[Test]
    public function it_gets_primary_social_media_account()
    {
        $primaryAccount = SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::INSTAGRAM->value,
            'username' => 'primary_account',
            'is_primary' => true,
        ]);

        SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::TIKTOK->value,
            'username' => 'secondary_account',
            'is_primary' => false,
        ]);

        $result = ProfileService::getPrimarySocialMediaAccount($this->user);

        $this->assertInstanceOf(SocialMediaAccount::class, $result);
        $this->assertEquals($primaryAccount->id, $result->id);
        $this->assertEquals('primary_account', $result->username);
        $this->assertTrue($result->is_primary);
    }

    #[Test]
    public function it_returns_null_when_no_primary_social_media_account()
    {
        SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::INSTAGRAM->value,
            'username' => 'account1',
            'is_primary' => false,
        ]);

        SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::TIKTOK->value,
            'username' => 'account2',
            'is_primary' => false,
        ]);

        $result = ProfileService::getPrimarySocialMediaAccount($this->user);

        $this->assertNull($result);
    }

    #[Test]
    public function it_generates_social_media_url_for_instagram()
    {
        $account = SocialMediaAccount::factory()->create([
            'platform' => SocialPlatform::INSTAGRAM->value,
            'username' => 'test_user',
        ]);

        $url = ProfileService::generateSocialMediaUrl($account);

        $this->assertEquals('https://instagram.com/test_user', $url);
    }

    #[Test]
    public function it_generates_social_media_url_for_tiktok()
    {
        $account = SocialMediaAccount::factory()->create([
            'platform' => SocialPlatform::TIKTOK->value,
            'username' => 'test_user',
        ]);

        $url = ProfileService::generateSocialMediaUrl($account);

        $this->assertEquals('https://tiktok.com/@test_user', $url);
    }

    #[Test]
    public function it_generates_social_media_url_for_youtube()
    {
        $account = SocialMediaAccount::factory()->create([
            'platform' => SocialPlatform::YOUTUBE->value,
            'username' => 'test_user',
        ]);

        $url = ProfileService::generateSocialMediaUrl($account);

        $this->assertEquals('https://youtube.com/@test_user', $url);
    }

    #[Test]
    public function it_generates_social_media_url_for_twitter()
    {
        $account = SocialMediaAccount::factory()->create([
            'platform' => SocialPlatform::TWITTER->value,
            'username' => 'test_user',
        ]);

        $url = ProfileService::generateSocialMediaUrl($account);

        $this->assertEquals('https://twitter.com/test_user', $url);
    }

    #[Test]
    public function it_generates_social_media_url_for_facebook()
    {
        $account = SocialMediaAccount::factory()->create([
            'platform' => SocialPlatform::FACEBOOK->value,
            'username' => 'test_user',
        ]);

        $url = ProfileService::generateSocialMediaUrl($account);

        $this->assertEquals('https://facebook.com/test_user', $url);
    }

    #[Test]
    public function it_generates_social_media_url_for_linkedin()
    {
        $account = SocialMediaAccount::factory()->create([
            'platform' => SocialPlatform::LINKEDIN->value,
            'username' => 'test_user',
        ]);

        $url = ProfileService::generateSocialMediaUrl($account);

        $this->assertEquals('https://linkedin.com/in/test_user', $url);
    }

    #[Test]
    public function it_returns_null_for_unknown_platform()
    {
        $account = SocialMediaAccount::factory()->create([
            'platform' => 'unknown_platform',
            'username' => 'test_user',
        ]);

        $url = ProfileService::generateSocialMediaUrl($account);

        $this->assertNull($url);
    }

    #[Test]
    public function it_finds_postal_code_by_code()
    {
        $result = ProfileService::findPostalCodeByCode('12345');

        $this->assertInstanceOf(PostalCode::class, $result);
        $this->assertEquals('12345', $result->postal_code);
        $this->assertEquals('Test City', $result->city);
        $this->assertEquals('Test State', $result->state);
    }

    #[Test]
    public function it_returns_null_for_nonexistent_postal_code()
    {
        $result = ProfileService::findPostalCodeByCode('99999');

        $this->assertNull($result);
    }

    #[Test]
    public function it_creates_business_profile_with_postal_code_lookup()
    {
        $profileData = [
            'company_name' => 'Test Company',
            'company_description' => 'A test company description',
            'niches' => [Niche::FASHION->value],
            'collaboration_goals' => [CollaborationGoal::BRAND_AWARENESS->value],
            'subscription_plan' => SubscriptionPlan::BASIC->value,
        ];

        $locationData = [
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'US',
        ];

        $profile = ProfileService::createBusinessProfile($this->user, $profileData, $locationData);

        $this->assertInstanceOf(BusinessProfile::class, $profile);
        $this->assertEquals($this->postalCode->id, $profile->postal_code_id);
        $this->assertEquals('12345', $profile->postal_code);
        $this->assertEquals('Test City', $profile->city);
        $this->assertEquals('Test State', $profile->state);
    }

    #[Test]
    public function it_creates_influencer_profile_with_postal_code_lookup()
    {
        $this->user->update(['account_type' => AccountType::INFLUENCER]);

        $profileData = [
            'bio' => 'Test influencer bio',
            'niches' => [Niche::FASHION->value],
            'collaboration_goals' => [CollaborationGoal::BRAND_AWARENESS->value],
            'subscription_plan' => SubscriptionPlan::BASIC->value,
        ];

        $locationData = [
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'US',
        ];

        $profile = ProfileService::createInfluencerProfile($this->user, $profileData, $locationData);

        $this->assertInstanceOf(InfluencerProfile::class, $profile);
        $this->assertEquals($this->postalCode->id, $profile->postal_code_id);
        $this->assertEquals('12345', $profile->postal_code);
        $this->assertEquals('Test City', $profile->city);
        $this->assertEquals('Test State', $profile->state);
    }
}