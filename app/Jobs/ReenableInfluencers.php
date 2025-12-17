<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReenableInfluencers implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Search for influencers who have their searchable_at set in the past and re-enable them
        $influencers = \App\Models\Influencer::where('is_searchable', false)
            ->whereNotNull('searchable_at')
            ->where('searchable_at', '<=', now())
            ->get();

        foreach ($influencers as $influencer) {
            $influencer->is_searchable = true;
            $influencer->searchable_at = null;
            $influencer->save();

            $influencer->user->notify(new \App\Notifications\InfluencerReenabledNotification($influencer));
        }
    }
}
