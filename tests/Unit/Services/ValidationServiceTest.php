<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ValidationService;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\SubscriptionPlan;
use PHPUnit\Framework\Attributes\Test;

class ValidationServiceTest extends TestCase
{
    #[Test]
    public function it_returns_correct_user_validation_rules()
    {
        $rules = ValidationService::userRules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);

        $this->assertContains('required', $rules['name']);
        $this->assertContains('string', $rules['name']);
        $this->assertContains('max:255', $rules['name']);

        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('max:255', $rules['email']);

        $this->assertContains('required', $rules['password']);
        $this->assertContains('string', $rules['password']);
        $this->assertContains('min:8', $rules['password']);
        $this->assertContains('confirmed', $rules['password']);
    }

    #[Test]
    public function it_returns_correct_profile_validation_rules()
    {
        $rules = ValidationService::profileRules();

        $this->assertArrayHasKey('primary_zip_code', $rules);
        $this->assertArrayHasKey('contact_name', $rules);
        $this->assertArrayHasKey('contact_email', $rules);

        $this->assertContains('required', $rules['primary_zip_code']);
        $this->assertContains('string', $rules['primary_zip_code']);
        $this->assertContains('max:10', $rules['primary_zip_code']);

        $this->assertContains('required', $rules['contact_name']);
        $this->assertContains('string', $rules['contact_name']);
        $this->assertContains('max:255', $rules['contact_name']);

        $this->assertContains('required', $rules['contact_email']);
        $this->assertContains('email', $rules['contact_email']);
        $this->assertContains('max:255', $rules['contact_email']);
    }

    #[Test]
    public function it_returns_correct_business_step_1_validation_rules()
    {
        $rules = ValidationService::businessStep1Rules();

        $this->assertArrayHasKey('businessName', $rules);
        $this->assertArrayHasKey('industry', $rules);
        $this->assertArrayHasKey('primaryZipCode', $rules);
        $this->assertArrayHasKey('locationCount', $rules);

        $this->assertContains('required', $rules['businessName']);
        $this->assertContains('string', $rules['businessName']);
        $this->assertContains('max:255', $rules['businessName']);

        $this->assertContains('required', $rules['industry']);
        $this->assertContains(Niche::validationRule(), $rules['industry']);

        $this->assertContains('required', $rules['primaryZipCode']);
        $this->assertContains('string', $rules['primaryZipCode']);
        $this->assertContains('max:10', $rules['primaryZipCode']);
    }

    #[Test]
    public function it_returns_correct_business_step_2_validation_rules()
    {
        $rules = ValidationService::businessStep2Rules();

        $this->assertArrayHasKey('contactName', $rules);
        $this->assertArrayHasKey('contactEmail', $rules);
        $this->assertArrayHasKey('subscriptionPlan', $rules);

        $this->assertContains('required', $rules['contactName']);
        $this->assertContains('string', $rules['contactName']);
        $this->assertContains('max:255', $rules['contactName']);

        $this->assertContains('required', $rules['contactEmail']);
        $this->assertContains('email', $rules['contactEmail']);
        $this->assertContains('max:255', $rules['contactEmail']);

        $this->assertContains('required', $rules['subscriptionPlan']);
        $this->assertContains(SubscriptionPlan::validationRule(), $rules['subscriptionPlan']);
    }

    #[Test]
    public function it_returns_correct_business_step_3_validation_rules()
    {
        $rules = ValidationService::businessStep3Rules();

        $this->assertArrayHasKey('collaborationGoals', $rules);
        $this->assertArrayHasKey('collaborationGoals.*', $rules);
        $this->assertArrayHasKey('campaignTypes', $rules);
        $this->assertArrayHasKey('campaignTypes.*', $rules);

        $this->assertContains('required', $rules['collaborationGoals']);
        $this->assertContains('array', $rules['collaborationGoals']);
        $this->assertContains('min:1', $rules['collaborationGoals']);

        $this->assertContains('required', $rules['campaignTypes']);
        $this->assertContains('array', $rules['campaignTypes']);
        $this->assertContains('min:1', $rules['campaignTypes']);

        $this->assertEquals(CollaborationGoal::validationRule(), $rules['collaborationGoals.*']);
        $this->assertEquals(CampaignType::validationRule(), $rules['campaignTypes.*']);
    }

    #[Test]
    public function it_returns_correct_business_step_4_validation_rules()
    {
        $rules = ValidationService::businessStep4Rules();

        $this->assertArrayHasKey('teamMembers.*.name', $rules);
        $this->assertArrayHasKey('teamMembers.*.email', $rules);

        $this->assertContains('required', $rules['teamMembers.*.name']);
        $this->assertContains('string', $rules['teamMembers.*.name']);
        $this->assertContains('max:255', $rules['teamMembers.*.name']);

        $this->assertContains('required', $rules['teamMembers.*.email']);
        $this->assertContains('email', $rules['teamMembers.*.email']);
        $this->assertContains('max:255', $rules['teamMembers.*.email']);
    }

    #[Test]
    public function it_returns_correct_influencer_step_1_validation_rules()
    {
        $rules = ValidationService::influencerStep1Rules();

        $this->assertArrayHasKey('creatorName', $rules);
        $this->assertArrayHasKey('primaryNiche', $rules);
        $this->assertArrayHasKey('primaryZipCode', $rules);

        $this->assertContains('required', $rules['creatorName']);
        $this->assertContains('string', $rules['creatorName']);
        $this->assertContains('max:255', $rules['creatorName']);

        $this->assertContains('required', $rules['primaryNiche']);
        $this->assertContains(Niche::validationRule(), $rules['primaryNiche']);

        $this->assertContains('required', $rules['primaryZipCode']);
        $this->assertContains('string', $rules['primaryZipCode']);
        $this->assertContains('max:10', $rules['primaryZipCode']);
    }

    #[Test]
    public function it_returns_correct_influencer_step_2_validation_rules()
    {
        $rules = ValidationService::influencerStep2Rules();

        $this->assertArrayHasKey('socialMediaAccounts.*.platform', $rules);
        $this->assertArrayHasKey('socialMediaAccounts.*.username', $rules);
        $this->assertArrayHasKey('socialMediaAccounts.*.follower_count', $rules);

        $this->assertContains('required', $rules['socialMediaAccounts.*.platform']);
        $this->assertContains(SocialPlatform::validationRule(), $rules['socialMediaAccounts.*.platform']);

        $this->assertContains('required', $rules['socialMediaAccounts.*.username']);
        $this->assertContains('string', $rules['socialMediaAccounts.*.username']);
        $this->assertContains('max:255', $rules['socialMediaAccounts.*.username']);

        $this->assertContains('required', $rules['socialMediaAccounts.*.follower_count']);
        $this->assertContains('integer', $rules['socialMediaAccounts.*.follower_count']);
        $this->assertContains('min:0', $rules['socialMediaAccounts.*.follower_count']);
    }

    #[Test]
    public function it_returns_correct_influencer_step_3_validation_rules()
    {
        $rules = ValidationService::influencerStep3Rules();

        $this->assertArrayHasKey('mediaKitUrl', $rules);

        $this->assertContains('nullable', $rules['mediaKitUrl']);
        $this->assertContains('url', $rules['mediaKitUrl']);
    }

    #[Test]
    public function it_returns_correct_influencer_step_4_validation_rules()
    {
        $rules = ValidationService::influencerStep4Rules();

        $this->assertArrayHasKey('collaborationPreferences', $rules);
        $this->assertArrayHasKey('collaborationPreferences.*', $rules);
        $this->assertArrayHasKey('subscriptionPlan', $rules);

        $this->assertContains('required', $rules['collaborationPreferences']);
        $this->assertContains('array', $rules['collaborationPreferences']);
        $this->assertContains('min:1', $rules['collaborationPreferences']);

        $this->assertEquals(CollaborationGoal::validationRule(), $rules['collaborationPreferences.*']);

        $this->assertContains('required', $rules['subscriptionPlan']);
        $this->assertContains(SubscriptionPlan::validationRule(), $rules['subscriptionPlan']);
    }

    #[Test]
    public function it_returns_correct_social_media_account_validation_rules()
    {
        $rules = ValidationService::socialMediaAccountRules();

        $this->assertArrayHasKey('platform', $rules);
        $this->assertArrayHasKey('username', $rules);
        $this->assertArrayHasKey('follower_count', $rules);
        $this->assertArrayHasKey('is_primary', $rules);

        $this->assertContains('required', $rules['platform']);
        $this->assertContains(SocialPlatform::validationRule(), $rules['platform']);

        $this->assertContains('required', $rules['username']);
        $this->assertContains('string', $rules['username']);
        $this->assertContains('max:255', $rules['username']);

        $this->assertContains('required', $rules['follower_count']);
        $this->assertContains('integer', $rules['follower_count']);
        $this->assertContains('min:0', $rules['follower_count']);

        $this->assertContains('boolean', $rules['is_primary']);
    }

    #[Test]
    public function it_returns_correct_search_validation_rules()
    {
        $rules = ValidationService::searchRules();

        $this->assertArrayHasKey('search', $rules);
        $this->assertArrayHasKey('location', $rules);
        $this->assertArrayHasKey('account_type', $rules);
        $this->assertArrayHasKey('niches', $rules);
        $this->assertArrayHasKey('niches.*', $rules);
        $this->assertArrayHasKey('collaboration_goals', $rules);
        $this->assertArrayHasKey('collaboration_goals.*', $rules);
        $this->assertArrayHasKey('social_platforms', $rules);
        $this->assertArrayHasKey('social_platforms.*', $rules);
        $this->assertArrayHasKey('min_followers', $rules);
        $this->assertArrayHasKey('max_followers', $rules);
        $this->assertArrayHasKey('postal_code', $rules);
        $this->assertArrayHasKey('radius', $rules);
        $this->assertArrayHasKey('sort_by', $rules);

        $this->assertContains('nullable', $rules['search']);
        $this->assertContains('string', $rules['search']);
        $this->assertContains('max:255', $rules['search']);

        $this->assertContains('nullable', $rules['location']);
        $this->assertContains('string', $rules['location']);
        $this->assertContains('max:255', $rules['location']);

        $this->assertContains('nullable', $rules['account_type']);
        $this->assertContains('string', $rules['account_type']);

        $this->assertContains('nullable', $rules['niches']);
        $this->assertContains('array', $rules['niches']);
        $this->assertEquals(Niche::validationRule(), $rules['niches.*']);

        $this->assertContains('nullable', $rules['collaboration_goals']);
        $this->assertContains('array', $rules['collaboration_goals']);
        $this->assertEquals(CollaborationGoal::validationRule(), $rules['collaboration_goals.*']);

        $this->assertContains('nullable', $rules['social_platforms']);
        $this->assertContains('array', $rules['social_platforms']);
        $this->assertEquals(SocialPlatform::validationRule(), $rules['social_platforms.*']);

        $this->assertContains('nullable', $rules['min_followers']);
        $this->assertContains('integer', $rules['min_followers']);
        $this->assertContains('min:0', $rules['min_followers']);

        $this->assertContains('nullable', $rules['max_followers']);
        $this->assertContains('integer', $rules['max_followers']);
        $this->assertContains('min:0', $rules['max_followers']);

        $this->assertContains('nullable', $rules['postal_code']);
        $this->assertContains('string', $rules['postal_code']);
        $this->assertContains('max:10', $rules['postal_code']);

        $this->assertContains('nullable', $rules['radius']);
        $this->assertContains('integer', $rules['radius']);
        $this->assertContains('min:1', $rules['radius']);

        $this->assertContains('nullable', $rules['sort_by']);
        $this->assertContains('string', $rules['sort_by']);
        $this->assertContains('in:relevance,followers,distance', $rules['sort_by']);
    }

    #[Test]
    public function it_returns_correct_auth_validation_rules()
    {
        $rules = ValidationService::authRules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);

        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);

        $this->assertContains('required', $rules['password']);
        $this->assertContains('string', $rules['password']);
    }

    #[Test]
    public function it_returns_correct_registration_validation_rules()
    {
        $rules = ValidationService::registrationRules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertArrayHasKey('account_type', $rules);

        $this->assertContains('required', $rules['name']);
        $this->assertContains('string', $rules['name']);
        $this->assertContains('max:255', $rules['name']);

        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('max:255', $rules['email']);
        $this->assertContains('unique:users', $rules['email']);

        $this->assertContains('required', $rules['password']);
        $this->assertContains('string', $rules['password']);
        $this->assertContains('min:8', $rules['password']);
        $this->assertContains('confirmed', $rules['password']);

        $this->assertContains('required', $rules['account_type']);
        $this->assertContains('string', $rules['account_type']);
        $this->assertContains('in:business,influencer', $rules['account_type']);
    }

    #[Test]
    public function it_returns_correct_password_reset_validation_rules()
    {
        $rules = ValidationService::passwordResetRules();

        $this->assertArrayHasKey('token', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);

        $this->assertContains('required', $rules['token']);
        $this->assertContains('string', $rules['token']);

        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);

        $this->assertContains('required', $rules['password']);
        $this->assertContains('string', $rules['password']);
        $this->assertContains('min:8', $rules['password']);
        $this->assertContains('confirmed', $rules['password']);
    }

    #[Test]
    public function it_returns_correct_step_rules_for_business_account()
    {
        $step1Rules = ValidationService::getStepRules('business', 1);
        $step2Rules = ValidationService::getStepRules('business', 2);
        $step3Rules = ValidationService::getStepRules('business', 3);
        $step4Rules = ValidationService::getStepRules('business', 4);

        $this->assertArrayHasKey('businessName', $step1Rules);
        $this->assertArrayHasKey('contactName', $step2Rules);
        $this->assertArrayHasKey('collaborationGoals', $step3Rules);
        $this->assertArrayHasKey('teamMembers.*.name', $step4Rules);
    }

    #[Test]
    public function it_returns_correct_step_rules_for_influencer_account()
    {
        $step1Rules = ValidationService::getStepRules('influencer', 1);
        $step2Rules = ValidationService::getStepRules('influencer', 2);
        $step3Rules = ValidationService::getStepRules('influencer', 3);
        $step4Rules = ValidationService::getStepRules('influencer', 4);

        $this->assertArrayHasKey('creatorName', $step1Rules);
        $this->assertArrayHasKey('socialMediaAccounts.*.platform', $step2Rules);
        $this->assertArrayHasKey('mediaKitUrl', $step3Rules);
        $this->assertArrayHasKey('collaborationPreferences', $step4Rules);
    }

    #[Test]
    public function it_returns_empty_array_for_invalid_step()
    {
        $rules = ValidationService::getStepRules('business', 99);
        $this->assertEmpty($rules);

        $rules = ValidationService::getStepRules('influencer', 0);
        $this->assertEmpty($rules);

        $rules = ValidationService::getStepRules('invalid', 1);
        $this->assertEmpty($rules);
    }

    #[Test]
    public function it_validates_enum_values_correctly()
    {
        $rules = ValidationService::businessStep1Rules();
        $industryRule = $rules['industry'];

        $this->assertContains(Niche::validationRule(), $industryRule);

        $socialMediaRules = ValidationService::influencerStep2Rules();
        $platformRule = $socialMediaRules['socialMediaAccounts.*.platform'];

        $this->assertContains(SocialPlatform::validationRule(), $platformRule);
    }

    #[Test]
    public function it_handles_array_validation_correctly()
    {
        $rules = ValidationService::businessStep3Rules();

        $this->assertContains('array', $rules['collaborationGoals']);
        $this->assertContains('min:1', $rules['collaborationGoals']);
        $this->assertEquals(CollaborationGoal::validationRule(), $rules['collaborationGoals.*']);

        $this->assertContains('array', $rules['campaignTypes']);
        $this->assertContains('min:1', $rules['campaignTypes']);
        $this->assertEquals(CampaignType::validationRule(), $rules['campaignTypes.*']);
    }

    #[Test]
    public function it_handles_nullable_fields_correctly()
    {
        $rules = ValidationService::influencerStep3Rules();
        $this->assertContains('nullable', $rules['mediaKitUrl']);

        $rules = ValidationService::influencerStep4Rules();
        $this->assertContains('nullable', $rules['collaborationPreferences']);

        $searchRules = ValidationService::searchRules();
        $this->assertContains('nullable', $searchRules['search']);
        $this->assertContains('nullable', $searchRules['location']);
        $this->assertContains('nullable', $searchRules['postal_code']);
    }

    #[Test]
    public function it_handles_integer_validation_correctly()
    {
        $rules = ValidationService::socialMediaAccountRules();
        $this->assertContains('integer', $rules['follower_count']);
        $this->assertContains('min:0', $rules['follower_count']);

        $searchRules = ValidationService::searchRules();
        $this->assertContains('integer', $searchRules['min_followers']);
        $this->assertContains('min:0', $searchRules['min_followers']);
        $this->assertContains('integer', $searchRules['max_followers']);
        $this->assertContains('min:0', $searchRules['max_followers']);
    }

    #[Test]
    public function it_handles_url_validation_correctly()
    {
        $rules = ValidationService::influencerStep3Rules();
        $this->assertContains('url', $rules['mediaKitUrl']);
    }
}