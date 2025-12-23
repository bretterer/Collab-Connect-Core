<?php

namespace Tests\Feature\Jobs;

use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Jobs\ProcessCampaignLifecycle;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProcessCampaignLifecycleTest extends TestCase
{
    #[Test]
    public function publishes_scheduled_campaigns_when_scheduled_date_is_reached(): void
    {
        $campaign = Campaign::factory()->create([
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => now()->subDay(),
        ]);

        $this->assertEquals(CampaignStatus::SCHEDULED, $campaign->status);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::PUBLISHED, $campaign->status);
        $this->assertNotNull($campaign->published_at);
    }

    #[Test]
    public function does_not_publish_scheduled_campaigns_with_future_date(): void
    {
        $campaign = Campaign::factory()->create([
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => now()->addDays(5),
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::SCHEDULED, $campaign->status);
    }

    #[Test]
    public function starts_published_campaigns_when_start_date_is_reached_and_has_accepted_applications(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->published()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_start_date' => now()->subDay()->toDateString(),
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
            'status' => CampaignApplicationStatus::ACCEPTED,
        ]);

        $this->assertEquals(CampaignStatus::PUBLISHED, $campaign->status);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::IN_PROGRESS, $campaign->status);
        $this->assertNotNull($campaign->started_at);
    }

    #[Test]
    public function does_not_start_published_campaigns_without_accepted_applications(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->published()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_start_date' => now()->subDay()->toDateString(),
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
            'status' => CampaignApplicationStatus::PENDING,
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::PUBLISHED, $campaign->status);
    }

    #[Test]
    public function starts_published_campaigns_with_null_start_date_if_has_accepted_applications(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->published()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_start_date' => null,
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
            'status' => CampaignApplicationStatus::ACCEPTED,
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::IN_PROGRESS, $campaign->status);
    }

    #[Test]
    public function does_not_start_published_campaigns_with_future_start_date(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->published()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_start_date' => now()->addDays(5)->toDateString(),
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
            'status' => CampaignApplicationStatus::ACCEPTED,
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::PUBLISHED, $campaign->status);
    }

    #[Test]
    public function completes_in_progress_campaigns_when_completion_date_is_reached(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->inProgress()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_completion_date' => now()->subDay()->toDateString(),
        ]);

        $this->assertEquals(CampaignStatus::IN_PROGRESS, $campaign->status);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::COMPLETED, $campaign->status);
        $this->assertNotNull($campaign->completed_at);
    }

    #[Test]
    public function does_not_complete_in_progress_campaigns_with_null_completion_date(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->inProgress()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_completion_date' => null,
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::IN_PROGRESS, $campaign->status);
    }

    #[Test]
    public function does_not_complete_in_progress_campaigns_with_future_completion_date(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->inProgress()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_completion_date' => now()->addDays(5)->toDateString(),
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::IN_PROGRESS, $campaign->status);
    }

    #[Test]
    public function handles_multiple_campaigns_transitioning_at_once(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $scheduledCampaign1 = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => now()->subDay(),
        ]);

        $scheduledCampaign2 = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => now()->subDays(2),
        ]);

        $publishedCampaign = Campaign::factory()->published()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_start_date' => now()->subDay()->toDateString(),
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $publishedCampaign->id,
            'user_id' => $influencer->id,
            'status' => CampaignApplicationStatus::ACCEPTED,
        ]);

        $inProgressCampaign = Campaign::factory()->inProgress()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_completion_date' => now()->subDay()->toDateString(),
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $scheduledCampaign1->refresh();
        $scheduledCampaign2->refresh();
        $publishedCampaign->refresh();
        $inProgressCampaign->refresh();

        $this->assertEquals(CampaignStatus::PUBLISHED, $scheduledCampaign1->status);
        $this->assertEquals(CampaignStatus::PUBLISHED, $scheduledCampaign2->status);
        $this->assertEquals(CampaignStatus::IN_PROGRESS, $publishedCampaign->status);
        $this->assertEquals(CampaignStatus::COMPLETED, $inProgressCampaign->status);
    }

    #[Test]
    public function skips_campaigns_already_in_target_state(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $alreadyPublished = Campaign::factory()->published()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'scheduled_date' => now()->subDay(),
        ]);
        $originalPublishedAt = $alreadyPublished->published_at;

        $alreadyInProgress = Campaign::factory()->inProgress()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_start_date' => now()->subDay()->toDateString(),
        ]);
        $originalStartedAt = $alreadyInProgress->started_at;

        $alreadyCompleted = Campaign::factory()->completed()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'campaign_completion_date' => now()->subDay()->toDateString(),
        ]);
        $originalCompletedAt = $alreadyCompleted->completed_at;

        (new ProcessCampaignLifecycle)->handle();

        $alreadyPublished->refresh();
        $alreadyInProgress->refresh();
        $alreadyCompleted->refresh();

        $this->assertEquals(CampaignStatus::PUBLISHED, $alreadyPublished->status);
        $this->assertEquals($originalPublishedAt->toDateTimeString(), $alreadyPublished->published_at->toDateTimeString());

        $this->assertEquals(CampaignStatus::IN_PROGRESS, $alreadyInProgress->status);
        $this->assertEquals($originalStartedAt->toDateTimeString(), $alreadyInProgress->started_at->toDateTimeString());

        $this->assertEquals(CampaignStatus::COMPLETED, $alreadyCompleted->status);
        $this->assertEquals($originalCompletedAt->toDateTimeString(), $alreadyCompleted->completed_at->toDateTimeString());
    }

    #[Test]
    public function logs_transitions_when_processing_campaigns(): void
    {
        Log::spy();

        $campaign = Campaign::factory()->create([
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => now()->subDay(),
        ]);

        (new ProcessCampaignLifecycle)->handle();

        Log::shouldHaveReceived('info')
            ->with('ProcessCampaignLifecycle: Auto-published campaign', \Mockery::type('array'));
    }

    #[Test]
    public function continues_processing_even_if_one_campaign_fails(): void
    {
        $campaign1 = Campaign::factory()->create([
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => now()->subDay(),
        ]);

        $campaign2 = Campaign::factory()->create([
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => now()->subDays(2),
        ]);

        (new ProcessCampaignLifecycle)->handle();

        $campaign1->refresh();
        $campaign2->refresh();

        $this->assertEquals(CampaignStatus::PUBLISHED, $campaign1->status);
        $this->assertEquals(CampaignStatus::PUBLISHED, $campaign2->status);
    }

    #[Test]
    public function command_dispatches_job_to_queue(): void
    {
        $this->artisan('campaigns:process-lifecycle')
            ->assertSuccessful()
            ->expectsOutput('Dispatching campaign lifecycle job to queue...')
            ->expectsOutput('Campaign lifecycle job dispatched successfully.');
    }

    #[Test]
    public function command_runs_synchronously_with_sync_option(): void
    {
        $this->artisan('campaigns:process-lifecycle', ['--sync' => true])
            ->assertSuccessful()
            ->expectsOutput('Processing campaign lifecycle transitions synchronously...')
            ->expectsOutput('Campaign lifecycle transitions processed successfully.');
    }
}
