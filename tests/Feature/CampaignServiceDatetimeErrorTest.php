<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Services\CampaignService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class CampaignServiceDatetimeErrorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_handles_empty_strings_for_date_fields_in_sqlite()
    {
        // Create a business user
        $user = User::factory()->business()->withProfile()->create();

        // Data that would cause the error - empty strings for date fields
        $campaignData = [
            'campaign_goal' => 'Gain momentum and sign ups through collab connect platform by inviting new cincinnati/dayton area businesses and influencers',
            'campaign_type' => ['user_generated'],
            'target_zip_code' => '45066',
            'target_area' => '',
            'campaign_description' => '',
            'influencer_count' => 1,
            'application_deadline' => '', // This causes the error in MySQL
            'campaign_completion_date' => '', // This could also cause the error
            'publish_action' => 'publish',
            'scheduled_date' => '', // This could also cause the error
            'current_step' => 1,
            'project_name' => 'CollabConnect Sign up Initiative',
            'compensation_type' => 'monetary',
            'compensation_amount' => 0,
        ];

        // In SQLite this might work, but we want to test the fix works regardless
        $campaign = CampaignService::saveDraft($user, $campaignData);

        // These date fields should be null when empty strings are provided
        $this->assertNull($campaign->application_deadline);
        $this->assertNull($campaign->campaign_completion_date);
        $this->assertNull($campaign->scheduled_date);
    }

    #[Test]
    public function it_successfully_saves_campaign_with_empty_date_strings_after_fix()
    {
        // Create a business user
        $user = User::factory()->business()->withProfile()->create();

        // This is the exact data structure that caused the original error
        $campaignData = [
            'campaign_goal' => 'Gain momentum and sign ups through collab connect platform by inviting new cincinnati/dayton area businesses and influencers',
            'campaign_type' => ['user_generated'],
            'target_zip_code' => '45066',
            'target_area' => '',
            'campaign_description' => '',
            'influencer_count' => 1,
            'application_deadline' => '', // Empty string that should be null
            'campaign_completion_date' => '', // Empty string that should be null
            'publish_action' => 'publish',
            'scheduled_date' => '', // Empty string that should be null
            'current_step' => 1,
            'project_name' => 'CollabConnect Sign up Initiative',
            'main_contact' => '',
            'campaign_objective' => '',
            'key_insights' => '',
            'fan_motivator' => '',
            'creative_connection' => '',
            'target_audience' => '',
            'timing_details' => '',
            'additional_requirements' => '',
            'brand_overview' => '',
            'current_advertising_campaign' => '',
            'brand_story' => '',
            'brand_guidelines' => '',
            'social_requirements' => [],
            'placement_requirements' => [],
            'target_platforms' => [],
            'deliverables' => [],
            'success_metrics' => [],
            'content_guidelines' => '',
            'posting_restrictions' => '',
            'specific_products' => '',
            'additional_considerations' => '',
            'compensation_type' => 'monetary',
            'compensation_amount' => 0,
            'compensation_description' => '',
            'compensation_details' => [],
        ];

        // This should now work without throwing an exception
        $campaign = CampaignService::saveDraft($user, $campaignData);

        // Verify the campaign was created successfully and date fields are null
        $this->assertInstanceOf(\App\Models\Campaign::class, $campaign);
        $this->assertNull($campaign->application_deadline);
        $this->assertNull($campaign->campaign_completion_date);
        $this->assertNull($campaign->scheduled_date);
        $this->assertEquals('CollabConnect Sign up Initiative', $campaign->project_name);
    }

    #[Test]
    public function it_handles_empty_strings_in_update_campaign_method()
    {
        // Create a business user and initial campaign
        $user = User::factory()->business()->withProfile()->create();
        $campaign = CampaignService::saveDraft($user, [
            'campaign_goal' => 'Initial goal',
            'campaign_type' => ['user_generated'],
            'target_zip_code' => '45066',
            'influencer_count' => 1,
            'compensation_type' => 'monetary',
            'compensation_amount' => 100,
        ]);

        // Update data with empty date strings that would cause the error
        $updateData = [
            'current_advertising_campaign' => 'Updated campaign description',
            'application_deadline' => '', // Empty string that should be null
            'campaign_completion_date' => '', // Empty string that should be null
            'scheduled_date' => '', // Empty string that should be null
            'additional_requirements' => [],
            'publish_action' => 'draft',
        ];

        // This should now work without throwing an exception
        $updatedCampaign = CampaignService::updateCampaign($campaign->id, $updateData, $user);

        // Verify the campaign was updated successfully and date fields are null
        $this->assertInstanceOf(\App\Models\Campaign::class, $updatedCampaign);
        $this->assertNull($updatedCampaign->application_deadline);
        $this->assertNull($updatedCampaign->campaign_completion_date);
        $this->assertNull($updatedCampaign->scheduled_date);
        $this->assertEquals('Updated campaign description', $updatedCampaign->current_advertising_campaign);
    }
}
