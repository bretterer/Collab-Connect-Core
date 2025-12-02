<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Console\Command;

class StartDueCampaigns extends Command
{
    protected $signature = 'campaigns:start-due';

    protected $description = 'Start campaigns that have reached their start date and have accepted applications';

    public function handle(): int
    {
        $campaigns = Campaign::readyToStart()->get();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns ready to start.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($campaigns as $campaign) {
            CampaignService::startCampaign($campaign);
            $this->line("Started campaign: {$campaign->project_name} (ID: {$campaign->id})");
            $count++;
        }

        $this->info("Started {$count} campaign(s).");

        return self::SUCCESS;
    }
}
