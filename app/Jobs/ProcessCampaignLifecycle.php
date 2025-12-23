<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCampaignLifecycle implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $this->publishScheduledCampaigns();
        $this->startDueCampaigns();
        $this->completeDueCampaigns();
    }

    protected function publishScheduledCampaigns(): void
    {
        $campaigns = Campaign::readyToPublish()->get();

        if ($campaigns->isEmpty()) {
            Log::info('ProcessCampaignLifecycle: No scheduled campaigns ready to publish.');

            return;
        }

        foreach ($campaigns as $campaign) {
            try {
                CampaignService::publishCampaign($campaign);

                Log::info('ProcessCampaignLifecycle: Auto-published campaign', [
                    'campaign_id' => $campaign->id,
                    'project_name' => $campaign->project_name,
                    'scheduled_date' => $campaign->scheduled_date,
                    'published_at' => now()->toDateTimeString(),
                ]);
            } catch (\Throwable $e) {
                Log::error('ProcessCampaignLifecycle: Failed to publish campaign', [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("ProcessCampaignLifecycle: Published {$campaigns->count()} scheduled campaign(s).");
    }

    protected function startDueCampaigns(): void
    {
        $campaigns = Campaign::readyToStart()->get();

        if ($campaigns->isEmpty()) {
            Log::info('ProcessCampaignLifecycle: No campaigns ready to start.');

            return;
        }

        foreach ($campaigns as $campaign) {
            try {
                CampaignService::startCampaign($campaign);

                Log::info('ProcessCampaignLifecycle: Auto-started campaign', [
                    'campaign_id' => $campaign->id,
                    'project_name' => $campaign->project_name,
                    'campaign_start_date' => $campaign->campaign_start_date,
                    'started_at' => now()->toDateTimeString(),
                ]);
            } catch (\Throwable $e) {
                Log::error('ProcessCampaignLifecycle: Failed to start campaign', [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("ProcessCampaignLifecycle: Started {$campaigns->count()} campaign(s).");
    }

    protected function completeDueCampaigns(): void
    {
        $campaigns = Campaign::readyToComplete()->get();

        if ($campaigns->isEmpty()) {
            Log::info('ProcessCampaignLifecycle: No campaigns ready to complete.');

            return;
        }

        foreach ($campaigns as $campaign) {
            try {
                CampaignService::completeCampaign($campaign);

                Log::info('ProcessCampaignLifecycle: Auto-completed campaign', [
                    'campaign_id' => $campaign->id,
                    'project_name' => $campaign->project_name,
                    'campaign_completion_date' => $campaign->campaign_completion_date,
                    'completed_at' => now()->toDateTimeString(),
                ]);
            } catch (\Throwable $e) {
                Log::error('ProcessCampaignLifecycle: Failed to complete campaign', [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("ProcessCampaignLifecycle: Completed {$campaigns->count()} campaign(s).");
    }
}
