<?php

namespace App\Jobs;

use App\Enums\PayoutStatus;
use App\Models\ReferralPayout;
use App\Models\ReferralPayoutItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessReferralPayouts implements ShouldQueue
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
        Log::info('ProcessReferralPayouts: Starting to process payout items');

        DB::transaction(function () {
            // Step 1: Find all items in draft, pending, or approved status
            $items = ReferralPayoutItem::whereIn('status', [
                PayoutStatus::DRAFT,
                PayoutStatus::PENDING,
                PayoutStatus::APPROVED,
            ])->get();

            if ($items->isEmpty()) {
                Log::info('ProcessReferralPayouts: No items to process');

                return;
            }

            Log::info('ProcessReferralPayouts: Found '.$items->count().' items to process');

            // Step 2: Update all items to processing status
            ReferralPayoutItem::whereIn('id', $items->pluck('id'))
                ->update(['status' => PayoutStatus::PROCESSING]);

            // Step 3: Group by referral_enrollment_id and calculate totals
            $grouped = $items->groupBy('referral_enrollment_id');

            foreach ($grouped as $enrollmentId => $enrollmentItems) {
                $totalAmount = $enrollmentItems->sum('amount');
                $referralCount = $enrollmentItems->count();

                // Get the month/year from the first item's scheduled payout date
                $firstItem = $enrollmentItems->first();
                $scheduledDate = $firstItem->scheduled_payout_date;

                Log::info("ProcessReferralPayouts: Processing enrollment {$enrollmentId} - Amount: {$totalAmount}, Count: {$referralCount}");

                // Step 4: Create ReferralPayout entry with status pending
                $payout = ReferralPayout::create([
                    'referral_enrollment_id' => $enrollmentId,
                    'amount' => $totalAmount,
                    'currency' => $firstItem->currency ?? 'USD',
                    'status' => PayoutStatus::PENDING,
                    'month' => $scheduledDate->month,
                    'year' => $scheduledDate->year,
                    'referral_count' => $referralCount,
                ]);

                // Step 5: Update payout items to processed and link to payout
                ReferralPayoutItem::whereIn('id', $enrollmentItems->pluck('id'))
                    ->update([
                        'status' => PayoutStatus::PROCESSED,
                        'referral_payout_id' => $payout->id,
                    ]);

                Log::info("ProcessReferralPayouts: Created payout #{$payout->id} for enrollment {$enrollmentId}");
            }

            Log::info('ProcessReferralPayouts: Successfully processed all items');
        });
    }
}
