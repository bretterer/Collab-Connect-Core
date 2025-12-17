<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleProfilePromotionUpdates implements ShouldQueue
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
        // Find all Influencer and Business entries with promoted_until today or in the past and unpromote them
        $today = now()->startOfDay();
        \App\Models\Influencer::whereNotNull('promoted_until')
            ->whereDate('promoted_until', '<=', $today)
            ->update([
                'promoted_until' => null,
                'is_promoted' => false,
            ]);
        \App\Models\Business::whereNotNull('promoted_until')
            ->whereDate('promoted_until', '<=', $today)
            ->update([
                'promoted_until' => null,
                'is_promoted' => false,
            ]);

    }
}
