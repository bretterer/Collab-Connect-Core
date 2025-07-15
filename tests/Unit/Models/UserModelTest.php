<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Models\SocialMediaAccount;
use App\Models\PostalCode;
use App\Enums\AccountType;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserModelTest extends TestCase
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
        ]);
    }

    #[Test]
    public function user_has_business_profile_relationship()
    {
        $profile = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(BusinessProfile::class, $this->user->businessProfile);
        $this->assertEquals($profile->id, $this->user->businessProfile->id);
    }

    #[Test]
    public function user_has_influencer_profile_relationship()
    {
        $this->user->update(['account_type' => AccountType::INFLUENCER]);

        $profile = InfluencerProfile::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(InfluencerProfile::class, $this->user->influencerProfile);
        $this->assertEquals($profile->id, $this->user->influencerProfile->id);
    }

    #[Test]
    public function user_has_social_media_accounts_relationship()
    {
        $account1 = SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::INSTAGRAM->value,
        ]);

        $account2 = SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::TIKTOK->value,
        ]);

        $this->assertCount(2, $this->user->socialMediaAccounts);
        $this->assertTrue($this->user->socialMediaAccounts->contains($account1));
        $this->assertTrue($this->user->socialMediaAccounts->contains($account2));
    }

    #[Test]
    public function user_can_check_if_business_account()
    {
        $this->assertTrue($this->user->isBusinessAccount());
        $this->assertFalse($this->user->isInfluencerAccount());
    }

    #[Test]
    public function user_can_check_if_influencer_account()
    {
        $this->user->update(['account_type' => AccountType::INFLUENCER]);

        $this->assertTrue($this->user->isInfluencerAccount());
        $this->assertFalse($this->user->isBusinessAccount());
    }

    #[Test]
    public function user_can_check_if_onboarding_completed()
    {
        // Without profile
        $this->assertFalse($this->user->hasCompletedOnboarding());

        // With business profile
        BusinessProfile::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->user->hasCompletedOnboarding());

        // Influencer user without profile
        $influencerUser = User::factory()->create(['account_type' => AccountType::INFLUENCER]);
        $this->assertFalse($influencerUser->hasCompletedOnboarding());

        // With influencer profile
        InfluencerProfile::factory()->create(['user_id' => $influencerUser->id]);
        $this->assertTrue($influencerUser->hasCompletedOnboarding());
    }

    #[Test]
    public function user_can_get_profile()
    {
        // Business user
        $businessProfile = BusinessProfile::factory()->create(['user_id' => $this->user->id]);
        $this->assertEquals($businessProfile->id, $this->user->getProfile()->id);

        // Influencer user
        $influencerUser = User::factory()->create(['account_type' => AccountType::INFLUENCER]);
        $influencerProfile = InfluencerProfile::factory()->create(['user_id' => $influencerUser->id]);
        $this->assertEquals($influencerProfile->id, $influencerUser->getProfile()->id);

        // User without profile
        $userWithoutProfile = User::factory()->create();
        $this->assertNull($userWithoutProfile->getProfile());
    }

    #[Test]
    public function user_can_get_postal_code_info()
    {
        $profile = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'postal_code' => '12345',
            'postal_code_id' => $this->postalCode->id,
        ]);

        $postalCodeInfo = $this->user->getPostalCodeInfo();

        $this->assertEquals('12345', $postalCodeInfo['postal_code']);
        $this->assertEquals('Test City', $postalCodeInfo['city']);
        $this->assertEquals('Test State', $postalCodeInfo['state']);
    }

    #[Test]
    public function user_returns_null_postal_code_info_without_profile()
    {
        $this->assertNull($this->user->getPostalCodeInfo());
    }

    #[Test]
    public function user_can_get_primary_social_media_account()
    {
        $this->user->update(['account_type' => AccountType::INFLUENCER]);

        $primaryAccount = SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::INSTAGRAM->value,
            'is_primary' => true,
        ]);

        $secondaryAccount = SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::TIKTOK->value,
            'is_primary' => false,
        ]);

        $result = $this->user->getPrimarySocialMediaAccount();

        $this->assertEquals($primaryAccount->id, $result->id);
        $this->assertTrue($result->is_primary);
    }

    #[Test]
    public function user_returns_null_when_no_primary_social_media_account()
    {
        $this->user->update(['account_type' => AccountType::INFLUENCER]);

        SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::INSTAGRAM->value,
            'is_primary' => false,
        ]);

        $this->assertNull($this->user->getPrimarySocialMediaAccount());
    }

    #[Test]
    public function user_can_get_total_follower_count()
    {
        $this->user->update(['account_type' => AccountType::INFLUENCER]);

        SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::INSTAGRAM->value,
            'follower_count' => 10000,
        ]);

        SocialMediaAccount::factory()->create([
            'user_id' => $this->user->id,
            'platform' => SocialPlatform::TIKTOK->value,
            'follower_count' => 5000,
        ]);

        $this->assertEquals(15000, $this->user->getTotalFollowerCount());
    }

    #[Test]
    public function user_returns_zero_follower_count_without_accounts()
    {
        $this->assertEquals(0, $this->user->getTotalFollowerCount());
    }

    #[Test]
    public function user_can_get_niches()
    {
        $profile = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'niches' => [Niche::FASHION->value, Niche::BEAUTY->value],
        ]);

        $niches = $this->user->getNiches();

        $this->assertEquals([Niche::FASHION->value, Niche::BEAUTY->value], $niches);
    }

    #[Test]
    public function user_returns_empty_niches_without_profile()
    {
        $this->assertEquals([], $this->user->getNiches());
    }

    #[Test]
    public function user_can_check_if_has_niche()
    {
        $profile = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'niches' => [Niche::FASHION->value, Niche::BEAUTY->value],
        ]);

        $this->assertTrue($this->user->hasNiche(Niche::FASHION->value));
        $this->assertTrue($this->user->hasNiche(Niche::BEAUTY->value));
        $this->assertFalse($this->user->hasNiche(Niche::TECH->value));
    }

    #[Test]
    public function user_returns_false_for_has_niche_without_profile()
    {
        $this->assertFalse($this->user->hasNiche(Niche::FASHION->value));
    }

    #[Test]
    public function user_can_get_display_name()
    {
        $this->assertEquals($this->user->name, $this->user->getDisplayName());

        // Business user with company name
        $businessProfile = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'Test Company',
        ]);

        $this->assertEquals('Test Company', $this->user->getDisplayName());

        // Influencer user should use name
        $influencerUser = User::factory()->create(['account_type' => AccountType::INFLUENCER]);
        $this->assertEquals($influencerUser->name, $influencerUser->getDisplayName());
    }

    #[Test]
    public function user_can_get_bio_or_description()
    {
        // Business user
        $businessProfile = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'company_description' => 'Test company description',
        ]);

        $this->assertEquals('Test company description', $this->user->getBioOrDescription());

        // Influencer user
        $influencerUser = User::factory()->create(['account_type' => AccountType::INFLUENCER]);
        $influencerProfile = InfluencerProfile::factory()->create([
            'user_id' => $influencerUser->id,
            'bio' => 'Test influencer bio',
        ]);

        $this->assertEquals('Test influencer bio', $influencerUser->getBioOrDescription());

        // User without profile
        $userWithoutProfile = User::factory()->create();
        $this->assertNull($userWithoutProfile->getBioOrDescription());
    }

    #[Test]
    public function user_can_get_location_string()
    {
        $profile = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'city' => 'Test City',
            'state' => 'Test State',
        ]);

        $this->assertEquals('Test City, Test State', $this->user->getLocationString());

        // User without profile
        $userWithoutProfile = User::factory()->create();
        $this->assertNull($userWithoutProfile->getLocationString());
    }

    #[Test]
    public function user_model_has_correct_fillable_attributes()
    {
        $fillable = [
            'name',
            'email',
            'email_verified_at',
            'password',
            'account_type',
        ];

        $this->assertEquals($fillable, $this->user->getFillable());
    }

    #[Test]
    public function user_model_has_correct_hidden_attributes()
    {
        $hidden = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($hidden, $this->user->getHidden());
    }

    #[Test]
    public function user_model_has_correct_casts()
    {
        $casts = [
            'id' => 'int',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_type' => AccountType::class,
        ];

        $this->assertEquals($casts, $this->user->getCasts());
    }

    #[Test]
    public function user_account_type_is_cast_to_enum()
    {
        $this->assertInstanceOf(AccountType::class, $this->user->account_type);
        $this->assertEquals(AccountType::BUSINESS, $this->user->account_type);
    }

    #[Test]
    public function user_password_is_hashed()
    {
        $user = User::factory()->create(['password' => 'plaintext']);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(password_verify('plaintext', $user->password));
    }

    #[Test]
    public function user_email_is_unique()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => 'test@example.com']);
        User::factory()->create(['email' => 'test@example.com']);
    }

    #[Test]
    public function user_can_be_created_with_factory()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertInstanceOf(AccountType::class, $user->account_type);
    }

    #[Test]
    public function user_factory_can_create_business_users()
    {
        $user = User::factory()->business()->create();

        $this->assertEquals(AccountType::BUSINESS, $user->account_type);
    }

    #[Test]
    public function user_factory_can_create_influencer_users()
    {
        $user = User::factory()->influencer()->create();

        $this->assertEquals(AccountType::INFLUENCER, $user->account_type);
    }

    #[Test]
    public function user_factory_can_create_admin_users()
    {
        $user = User::factory()->admin()->create();

        $this->assertEquals(AccountType::ADMIN, $user->account_type);
    }

    #[Test]
    public function user_factory_can_create_with_profile()
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $this->assertNotNull($businessUser->businessProfile);

        $influencerUser = User::factory()->influencer()->withProfile()->create();
        $this->assertNotNull($influencerUser->influencerProfile);
    }

    #[Test]
    public function user_relationships_are_properly_configured()
    {
        // Test business profile relationship
        $businessProfile = BusinessProfile::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->user->businessProfile()->exists());

        // Test influencer profile relationship
        $influencerUser = User::factory()->influencer()->create();
        $influencerProfile = InfluencerProfile::factory()->create(['user_id' => $influencerUser->id]);
        $this->assertTrue($influencerUser->influencerProfile()->exists());

        // Test social media accounts relationship
        $socialAccount = SocialMediaAccount::factory()->create(['user_id' => $this->user->id]);
        $this->assertTrue($this->user->socialMediaAccounts()->exists());
        $this->assertEquals(1, $this->user->socialMediaAccounts()->count());
    }

    #[Test]
    public function user_can_be_soft_deleted()
    {
        $userId = $this->user->id;

        $this->user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertNull(User::find($userId));
        $this->assertNotNull(User::withTrashed()->find($userId));
    }

    #[Test]
    public function user_deletion_cascades_to_profiles()
    {
        $businessProfile = BusinessProfile::factory()->create(['user_id' => $this->user->id]);
        $socialAccount = SocialMediaAccount::factory()->create(['user_id' => $this->user->id]);

        $this->user->delete();

        // Profiles should be soft deleted when user is deleted
        $this->assertSoftDeleted('business_profiles', ['id' => $businessProfile->id]);
        $this->assertSoftDeleted('social_media_accounts', ['id' => $socialAccount->id]);
    }
}