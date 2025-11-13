<?php

namespace Tests\Feature\Campaign;

use App\Enums\BusinessGoal;
use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Facades\MatchScore;
use App\Jobs\SendCampaignNotifications;
use App\Models\Campaign;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewCampaignCreateNotificationTest extends TestCase
{
    #[Test]
    public function a_new_campaign_will_dispatch_notification_job_for_matching_influencers(): void
    {
        Queue::fake();

        PostalCode::factory()->create([
            'postal_code' => '45066',
            'latitude' => 39.2014,
            'longitude' => -84.4582,
        ]);

        // Create business user with profile
        $businessUser = User::factory()->business()->withProfile([
            'target_age_range' => '18-24',
            'target_gender' => 'female',
            'type' => BusinessType::ECOMMERCE,
            'business_goals' => [BusinessGoal::BRAND_AWARENESS],
            'platforms' => ['facebook'],
            'postal_code' => '45066',
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
        ])->create();

        // Create influencer user with matching profile
        $influencerUser = User::factory()->influencer()->withProfile([
            'content_types' => ['retail'],
            'preferred_business_types' => [BusinessType::ECOMMERCE],
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066', // Exact match = 100 location points
        ])->create();

        // Create published campaign - this should trigger the job dispatch
        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '45066',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Amazing fitness brand partnership',
                'status' => \App\Enums\CampaignStatus::PUBLISHED, // Published status
            ])->toArray()
        );

        // Assert the job was dispatched
        Queue::assertPushed(SendCampaignNotifications::class, function ($job) use ($campaign) {
            return $job->campaign->id === $campaign->id;
        });

        // Assert it was pushed exactly once
        Queue::assertPushed(SendCampaignNotifications::class, 1);
    }

    #[Test]
    public function notification_job_sends_notifications_to_matching_influencers(): void
    {
        // DON'T fake the queue - let it run synchronously
        PostalCode::factory()->create([
            'postal_code' => '45066',
            'latitude' => 39.2014,
            'longitude' => -84.4582,
        ]);

        // Create business user with profile
        $businessUser = User::factory()->business()->withProfile([
            'target_age_range' => '18-24',
            'target_gender' => 'female',
            'type' => BusinessType::ECOMMERCE,
            'business_goals' => [BusinessGoal::BRAND_AWARENESS],
            'platforms' => ['facebook'],
            'postal_code' => '45066',
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
        ])->create();

        // Create influencer user with matching profile
        $influencerUser = User::factory()->influencer()->withProfile([
            'content_types' => ['retail'],
            'preferred_business_types' => [BusinessType::ECOMMERCE],
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066', // Exact match = 100 location points
        ])->create();

        // Create published campaign - this should trigger and execute the job
        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '45066',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Amazing fitness brand partnership',
                'status' => \App\Enums\CampaignStatus::PUBLISHED, // Published status
            ])->toArray()
        );

        // Verify the match score is above 80% threshold
        $matchScore = MatchScore::calculateMatchScore($campaign, $influencerUser->influencer);
        $this->assertGreaterThan(80, $matchScore, 'Match score should be above 80% threshold for notifications');

        // Verify that a notification was sent to the matching influencer
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $influencerUser->id,
            'notifiable_type' => 'App\\Models\\User',
        ]);

        // Verify notification count - should have exactly 1 notification for this campaign
        $notificationCount = $influencerUser->notifications()
            ->where('data->campaign_id', $campaign->id)
            ->count();

        $this->assertEquals(1, $notificationCount, 'Should send exactly one notification per matching influencer per campaign');
    }

    #[Test]
    public function notification_job_can_be_manually_executed(): void
    {
        // Alternative approach: Create job manually and test it
        PostalCode::factory()->create([
            'postal_code' => '45066',
            'latitude' => 39.2014,
            'longitude' => -84.4582,
        ]);

        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066',
        ])->create();

        $influencerUser = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066',
        ])->create();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '45066',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Fitness partnership',
            ])->toArray()
        );

        // Manually create and execute the job
        $job = new \App\Jobs\SendCampaignNotifications($campaign);
        $job->handle();

        // Assert notifications were created
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $influencerUser->id,
            'notifiable_type' => 'App\\Models\\User',
        ]);

        $this->assertDatabaseCount('notifications', 1);
    }

    #[Test]
    public function low_match_influencers_do_not_receive_notifications(): void
    {
        PostalCode::factory()->create([
            'postal_code' => '45066',
            'latitude' => 39.2014,
            'longitude' => -84.4582,
        ]);

        // Create a business in FITNESS industry
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066',
        ])->create();

        // Create an influencer with poor match (different industry, different location)
        $lowMatchInfluencer = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::TECHNOLOGY, // Different industry
            'postal_code' => '90210', // Different location (no PostalCode record = 50 points)
        ])->create();

        // Create campaign
        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '45066',
                'campaign_type' => CampaignType::EVENT_COVERAGE, // Lower scoring type
                'compensation_type' => CompensationType::DISCOUNT, // Lower scoring compensation
                'compensation_amount' => 50,
                'campaign_goal' => 'Fitness event coverage',
                'status' => \App\Enums\CampaignStatus::PUBLISHED, // Published status
            ])->toArray()
        );

        // Verify the match score is below 80% threshold
        $matchScore = MatchScore::calculateMatchScore($campaign, $lowMatchInfluencer->influencer);
        $this->assertLessThan(80, $matchScore, 'Match score should be below 80% threshold');

        // Verify that NO notification was sent to the low-match influencer
        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $lowMatchInfluencer->id,
            'notifiable_type' => 'App\\Models\\User',
        ]);
    }

    #[Test]
    public function multiple_matching_influencers_all_receive_notifications(): void
    {
        PostalCode::factory()->create([
            'postal_code' => '45066',
            'latitude' => 39.2014,
            'longitude' => -84.4582,
        ]);

        // Create business
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066',
        ])->create();

        // Create multiple matching influencers
        $influencer1 = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS, // Exact match
            'postal_code' => '45066', // Same location
        ])->create();

        $influencer2 = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS, // Exact match
            'postal_code' => '45066', // Same location
        ])->create();

        $influencer3 = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::HEALTHCARE, // Related industry
            'postal_code' => '45066', // Same location
        ])->create();

        // Create published campaign
        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '45066',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 800,
                'campaign_goal' => 'Premium fitness collaboration',
                'status' => \App\Enums\CampaignStatus::PUBLISHED, // Published status
            ])->toArray()
        );

        // Verify all high-scoring influencers receive notifications
        foreach ([$influencer1, $influencer2, $influencer3] as $influencer) {
            $matchScore = MatchScore::calculateMatchScore($campaign, $influencer->influencer);

            if ($matchScore >= 80) {
                $this->assertDatabaseHas('notifications', [
                    'notifiable_id' => $influencer->id,
                    'notifiable_type' => 'App\\Models\\User',
                ]);
            } else {
                $this->assertDatabaseMissing('notifications', [
                    'notifiable_id' => $influencer->id,
                    'notifiable_type' => 'App\\Models\\User',
                ]);
            }
        }

        // Count total notifications sent for this campaign
        $totalNotifications = DatabaseNotification::query()->where('data->campaign_id', $campaign->id)->count();
        $this->assertEquals(2, $totalNotifications, 'At least some notifications should be sent');
    }

    #[Test]
    public function business_owners_do_not_receive_notifications_for_their_own_campaigns(): void
    {
        PostalCode::factory()->create(['postal_code' => '45066']);

        // Create business user who is also an influencer (hybrid account)
        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066',
        ])->create();

        // Also create influencer profile for same user
        $businessUser->update(['account_type' => \App\Enums\AccountType::BUSINESS]); // Ensure business type
        \App\Models\Influencer::factory()->create([
            'user_id' => $businessUser->id,
            'postal_code' => '45066',
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
        ]);

        // Create campaign from this business
        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '45066',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Premium fitness partnership',
            ])->toArray()
        );

        // Verify that business owner doesn't get notified about their own campaign
        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $businessUser->id,
            'notifiable_type' => 'App\\Models\\User',
        ]);
    }

    #[Test]
    public function draft_campaigns_do_not_trigger_notifications(): void
    {
        PostalCode::factory()->create(['postal_code' => '45066']);

        $businessUser = User::factory()->business()->withProfile([
            'industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066',
        ])->create();

        $influencerUser = User::factory()->influencer()->withProfile([
            'primary_industry' => BusinessIndustry::FITNESS_WELLNESS,
            'postal_code' => '45066',
        ])->create();

        // Create DRAFT campaign (should not trigger notifications)
        $draftCampaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => '45066',
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => CompensationType::MONETARY,
                'compensation_amount' => 1000,
                'campaign_goal' => 'Fitness partnership',
                'status' => \App\Enums\CampaignStatus::DRAFT, // Draft status
            ])->toArray()
        );

        // Verify no notifications sent for draft campaign
        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $influencerUser->id,
            'notifiable_type' => 'App\\Models\\User',
        ]);
    }
}
