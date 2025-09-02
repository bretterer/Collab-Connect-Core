<?php

namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendCampaignNotifications implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Campaign $campaign)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $influencers = $this->campaign->findMatchingInfluencers();

        $influencers->each(function ($influencer) {
            $influencer->user->notify(new \App\Notifications\NewCampaignNotification($this->campaign));
        });
    }
}
