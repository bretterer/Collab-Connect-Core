<?php

namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class HandleCampaignBoostUpdates implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * Expires any boosted campaigns where the boost period has ended.
     */
    public function handle(): void
    {
        $expiredBoosts = Campaign::where('is_boosted', true)
            ->whereNotNull('boosted_until')
            ->where('boosted_until', '<=', now())
            ->get();

        if ($expiredBoosts->isEmpty()) {
            return;
        }

        foreach ($expiredBoosts as $campaign) {
            $campaign->update([
                'is_boosted' => false,
                'boosted_until' => null,
            ]);
        }

        Log::info('Campaign boosts expired', [
            'count' => $expiredBoosts->count(),
            'campaign_ids' => $expiredBoosts->pluck('id')->toArray(),
        ]);
    }
}
