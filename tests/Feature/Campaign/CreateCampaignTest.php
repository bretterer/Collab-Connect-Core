<?php

namespace Tests\Feature\Campaign;

use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Enums\DeliverableType;
use App\Enums\SuccessMetric;
use App\Enums\TargetPlatform;
use App\Livewire\Campaigns\EditCampaign;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateCampaignTest extends TestCase
{
    #[Test]
    public function a_business_owner_can_create_a_campaign(): void
    {
        $businessOwner = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $businessOwner->businesses()->first();
        $this->actingAs($businessOwner);

        Livewire::test(EditCampaign::class)
            ->assertSee('Create Campaign')
            ->assertSee('Step 1 of 4')
            ->assertSet('currentStep', 1)
            ->set('projectName', 'New Campaign')
            ->set('campaignGoal', 'Increase Awareness')
            ->set('campaignType', [CampaignType::BRAND_PARTNERSHIPS])
            ->set('targetZipCode', '45066')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertSee('Step 2 of 4')
            // Step 2: Brand & Briefing (combined)
            ->set('brandOverview', 'This is a great brand with excellent products and services.')
            ->set('currentAdvertisingCampaign', 'We advertise on social media.')
            ->set('brandStory', 'Our brand story is compelling.')
            ->set('campaignObjective', 'Our objective is to grow brand awareness and engagement.')
            ->set('keyInsights', 'Key insights are important for target audience understanding.')
            ->set('fanMotivator', 'Our fans are motivated by quality and value.')
            ->set('creativeConnection', 'We connect through storytelling and authentic moments.')
            ->set('specificProducts', 'We offer a range of products.')
            ->set('postingRestrictions', 'No posting restrictions.')
            ->set('additionalConsiderations', 'We value authenticity.')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->assertSee('Step 3 of 4')
            // Step 3: Deliverables & Settings (combined)
            ->set('targetPlatforms', [TargetPlatform::INSTAGRAM->value, TargetPlatform::YOUTUBE->value])
            ->set('deliverables', [DeliverableType::INSTAGRAM_POST->value, DeliverableType::YOUTUBE_VIDEO->value])
            ->set('successMetrics', [SuccessMetric::BRAND_AWARENESS->value, SuccessMetric::CLICKS->value])
            ->set('timingDetails', 'We want to launch in Q2.')
            ->set('compensationType', CompensationType::MONETARY->value)
            ->set('applicationDeadline', now()->addWeeks(2)->format('Y-m-d'))
            ->set('campaignStartDate', now()->addWeeks(3)->format('Y-m-d'))
            ->set('campaignCompletionDate', now()->addMonths(2)->format('Y-m-d'))
            ->set('compensationDescription', 'Competitive compensation based on experience.')
            ->set('influencerCount', '50')
            ->set('exclusivityPeriod', '30')
            ->set('additionalRequirements', 'Must have at least 10k followers.')
            ->call('nextStep')
            ->assertSet('currentStep', 4)
            ->assertSee('Step 4 of 4')
            ->set('publishAction', 'schedule')
            ->assertSee('Schedule Campaign')
            ->set('publishAction', 'publish')
            ->assertSee('Publish Campaign')
            ->call('publishCampaign');

        $this->assertDatabaseHas('campaigns', [
            'business_id' => $business->id,
            'project_name' => 'New Campaign',
            'campaign_goal' => 'Increase Awareness',
            'campaign_type' => '["brand_partnerships"]',
            'target_zip_code' => '45066',
            'brand_overview' => 'This is a great brand with excellent products and services.',
            'current_advertising_campaign' => 'We advertise on social media.',
            'brand_story' => 'Our brand story is compelling.',
            'campaign_objective' => 'Our objective is to grow brand awareness and engagement.',
            'key_insights' => 'Key insights are important for target audience understanding.',
            'fan_motivator' => 'Our fans are motivated by quality and value.',
            'creative_connection' => 'We connect through storytelling and authentic moments.',
            'specific_products' => 'We offer a range of products.',
            'posting_restrictions' => 'No posting restrictions.',
            'additional_considerations' => 'We value authenticity.',
        ]);
    }

    #[Test]
    public function a_business_member_can_create_a_campaign_for_the_business(): void
    {
        $this->withoutExceptionHandling();
        $businessOwner = User::factory()->business()->withProfile()->subscribed()->create();
        $businessMember = User::factory()->business()->withProfile()->create();
        $business = $businessOwner->businesses()->first();

        $business->members()->attach($businessMember->id, ['role' => 'member']);
        $businessMember->setCurrentBusiness($business);

        $this->assertDatabaseHas('business_users', [
            'business_id' => $business->id,
            'user_id' => $businessMember->id,
            'role' => 'member',
        ]);

        $this->actingAs($businessMember);

        Livewire::test(EditCampaign::class)
            ->assertSee('Create Campaign')
            ->assertSee('Step 1 of 4')
            ->assertSet('currentStep', 1)
            ->set('projectName', 'New Campaign')
            ->set('campaignGoal', 'Increase Awareness')
            ->set('campaignType', [CampaignType::BRAND_PARTNERSHIPS])
            ->set('targetZipCode', '45066')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertSee('Step 2 of 4')
            // Step 2: Brand & Briefing (combined)
            ->set('brandOverview', 'This is a great brand with excellent products and services.')
            ->set('currentAdvertisingCampaign', 'We advertise on social media.')
            ->set('brandStory', 'Our brand story is compelling.')
            ->set('campaignObjective', 'Our objective is to grow brand awareness and engagement.')
            ->set('keyInsights', 'Key insights are important for target audience understanding.')
            ->set('fanMotivator', 'Our fans are motivated by quality and value.')
            ->set('creativeConnection', 'We connect through storytelling and authentic moments.')
            ->set('specificProducts', 'We offer a range of products.')
            ->set('postingRestrictions', 'No posting restrictions.')
            ->set('additionalConsiderations', 'We value authenticity.')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->assertSee('Step 3 of 4')
            // Step 3: Deliverables & Settings (combined)
            ->set('targetPlatforms', [TargetPlatform::INSTAGRAM->value, TargetPlatform::YOUTUBE->value])
            ->set('deliverables', [DeliverableType::INSTAGRAM_POST->value, DeliverableType::YOUTUBE_VIDEO->value])
            ->set('successMetrics', [SuccessMetric::BRAND_AWARENESS->value, SuccessMetric::CLICKS->value])
            ->set('timingDetails', 'We want to launch in Q2.')
            ->set('compensationType', CompensationType::MONETARY->value)
            ->set('applicationDeadline', now()->addWeeks(2)->format('Y-m-d'))
            ->set('campaignStartDate', now()->addWeeks(3)->format('Y-m-d'))
            ->set('campaignCompletionDate', now()->addMonths(2)->format('Y-m-d'))
            ->set('compensationDescription', 'Competitive compensation based on experience.')
            ->set('influencerCount', '50')
            ->set('exclusivityPeriod', '30')
            ->set('additionalRequirements', 'Must have at least 10k followers.')
            ->call('nextStep')
            ->assertSet('currentStep', 4)
            ->assertSee('Step 4 of 4')
            ->set('publishAction', 'schedule')
            ->assertSee('Schedule Campaign')
            ->set('publishAction', 'publish')
            ->assertSee('Publish Campaign')
            ->call('publishCampaign');

        $this->assertDatabaseHas('campaigns', [
            'business_id' => $business->id,
            'project_name' => 'New Campaign',
            'campaign_goal' => 'Increase Awareness',
            'campaign_type' => '["brand_partnerships"]',
            'target_zip_code' => '45066',
            'brand_overview' => 'This is a great brand with excellent products and services.',
            'current_advertising_campaign' => 'We advertise on social media.',
            'brand_story' => 'Our brand story is compelling.',
            'campaign_objective' => 'Our objective is to grow brand awareness and engagement.',
            'key_insights' => 'Key insights are important for target audience understanding.',
            'fan_motivator' => 'Our fans are motivated by quality and value.',
            'creative_connection' => 'We connect through storytelling and authentic moments.',
            'specific_products' => 'We offer a range of products.',
            'posting_restrictions' => 'No posting restrictions.',
            'additional_considerations' => 'We value authenticity.',
        ]);
    }
}
