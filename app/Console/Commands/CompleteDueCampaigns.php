<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Console\Command;

class CompleteDueCampaigns extends Command
{
    protected $signature = 'campaigns:complete-due';

    protected $description = 'Complete campaigns that have reached their completion date';

    public function handle(): int
    {
        $campaigns = Campaign::readyToComplete()->get();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns ready to complete.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($campaigns as $campaign) {
            CampaignService::completeCampaign($campaign);
            $this->line("Completed campaign: {$campaign->project_name} (ID: {$campaign->id})");
            $count++;
        }

        $this->info("Completed {$count} campaign(s).");

        return self::SUCCESS;
    }
}
