<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Console\Command;

class PublishCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:publish-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled campaigns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Campaign::query()
            ->where('status', \App\Enums\CampaignStatus::SCHEDULED)
            ->where('scheduled_date', '<=', now())
            ->get()
            ->each(function (Campaign $campaign) {
                CampaignService::publishCampaign($campaign);
                $this->info("Published campaign ID: {$campaign->id}");
            });
    }
}
