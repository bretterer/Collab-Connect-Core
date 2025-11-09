<?php

namespace App\Jobs;

use App\Enums\PercentageChangeType;
use App\Models\ReferralPercentageHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MonitorTemporaryReferralPayouts implements ShouldQueue
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
        // This job will run every hour and check for any temporary referral percentage changes that have expired.
        // If any are found, it will reset the referral percentage to the previous value.

        $expiredChanges = ReferralPercentageHistory::where('change_type', PercentageChangeType::TEMPORARY_DATE)
            ->where('expires_at', '<=', now())
            ->whereNull('processed_at') // Only process unprocessed expired changes
            ->get();

        foreach ($expiredChanges as $change) {

            $latestPermanentChange = ReferralPercentageHistory::where('referral_enrollment_id', $change->referral_enrollment_id)
                ->whereIn('change_type', [PercentageChangeType::PERMANENT, PercentageChangeType::ENROLLMENT])
                ->latest()
                ->first();

            ReferralPercentageHistory::create([
                'referral_enrollment_id' => $change->referral_enrollment_id,
                'old_percentage' => $change->new_percentage,
                'new_percentage' => $latestPermanentChange ? $latestPermanentChange->new_percentage : 0,
                'change_type' => PercentageChangeType::PERMANENT,
                'reason' => 'Temporary percentage expired',
                'changed_by_user_id' => null, // System change
            ]);

            // Mark the temporary change as processed so it won't be processed again
            $change->update([
                'processed_at' => now(),
            ]);
        }
    }
}
