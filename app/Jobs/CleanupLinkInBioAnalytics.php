<?php

namespace App\Jobs;

use App\Services\LinkInBioAnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CleanupLinkInBioAnalytics implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(LinkInBioAnalyticsService $service): void
    {
        $deletedCount = $service->cleanupOldData();

        Log::info('Link in Bio Analytics cleanup completed', [
            'deleted_records' => $deletedCount,
        ]);
    }
}
