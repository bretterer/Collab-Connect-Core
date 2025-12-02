<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Console\Command;

class ProcessScheduledCampaigns extends Command
{
    protected $signature = 'campaigns:process-scheduled';

    protected $description = 'Publish campaigns that are scheduled for today or earlier';

    public function handle(): int
    {
        $campaigns = Campaign::readyToPublish()->get();

        if ($campaigns->isEmpty()) {
            $this->info('No scheduled campaigns ready to publish.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($campaigns as $campaign) {
            CampaignService::publishCampaign($campaign);
            $this->line("Published campaign: {$campaign->project_name} (ID: {$campaign->id})");
            $count++;
        }

        $this->info("Published {$count} scheduled campaign(s).");

        return self::SUCCESS;
    }
}
