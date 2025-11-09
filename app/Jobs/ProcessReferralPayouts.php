<?php

namespace App\Jobs;

use App\Enums\PayoutStatus;
use App\Models\ReferralPayout;
use App\Notifications\PayoutCancelledNotification;
use App\Notifications\PayPalEmailRequiredNotification;
use App\Services\PayPalPayoutsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessReferralPayouts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $attemptNumber = 1)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PayPalPayoutsService $paypalService): void
    {
        Log::info('ProcessReferralPayouts: Starting to process payouts', [
            'attempt_number' => $this->attemptNumber,
        ]);

        // Get all pending payouts
        $payouts = ReferralPayout::with(['enrollment.user'])
            ->where('status', PayoutStatus::PENDING)
            ->get();

        if ($payouts->isEmpty()) {
            Log::info('ProcessReferralPayouts: No pending payouts to process');

            return;
        }

        Log::info('ProcessReferralPayouts: Found '.$payouts->count().' pending payouts');

        // First, handle any that need to be cancelled or notified
        $validPayouts = collect();

        foreach ($payouts as $payout) {
            $enrollment = $payout->enrollment;

            // Check if enrollment is disabled - cancel immediately
            if ($enrollment->disabled_at !== null) {
                Log::warning('ProcessReferralPayouts: Enrollment is disabled', [
                    'payout_id' => $payout->id,
                    'enrollment_id' => $enrollment->id,
                ]);

                $payout->update([
                    'status' => PayoutStatus::CANCELLED,
                    'failure_reason' => 'Enrollment is disabled',
                    'failed_at' => now(),
                ]);

                continue;
            }

            // Check if PayPal email is set
            if (empty($enrollment->paypal_email)) {
                Log::warning('ProcessReferralPayouts: No PayPal email set', [
                    'payout_id' => $payout->id,
                    'enrollment_id' => $enrollment->id,
                    'attempt_number' => $this->attemptNumber,
                ]);

                if ($this->attemptNumber === 3) {
                    // Final attempt - cancel the payout
                    $payout->update([
                        'status' => PayoutStatus::CANCELLED,
                        'failure_reason' => 'No PayPal email provided after 3 attempts',
                        'failed_at' => now(),
                    ]);

                    // Send cancellation notification
                    $enrollment->user->notify(
                        new PayoutCancelledNotification($payout, 'No PayPal email provided after 3 attempts')
                    );

                    Log::info('ProcessReferralPayouts: Payout cancelled due to missing PayPal email', [
                        'payout_id' => $payout->id,
                    ]);
                } else {
                    // Send notification to user to add PayPal email
                    $enrollment->user->notify(new PayPalEmailRequiredNotification($payout, $this->attemptNumber));
                }

                continue;
            }

            // This payout is valid and can be processed
            $validPayouts->push($payout);
        }

        if ($validPayouts->isEmpty()) {
            Log::info('ProcessReferralPayouts: No valid payouts to process');

            return;
        }

        Log::info('ProcessReferralPayouts: Processing '.$validPayouts->count().' valid payouts');

        // Process payouts in batches of 5000
        $validPayouts->chunk(5000)->each(function ($batchPayouts) use ($paypalService) {
            $this->processBatch($batchPayouts->all(), $paypalService);
        });

        Log::info('ProcessReferralPayouts: Finished processing payouts');
    }

    /**
     * Process a batch of payouts.
     */
    protected function processBatch(array $payouts, PayPalPayoutsService $paypalService): void
    {
        if (empty($payouts)) {
            return;
        }

        try {
            Log::info('ProcessReferralPayouts: Processing batch', [
                'batch_size' => count($payouts),
                'attempt_number' => $this->attemptNumber,
            ]);

            // Attempt to process the batch via PayPal
            $result = $paypalService->createBatchPayout($payouts);

            if ($result === null) {
                // Batch failed - handle based on attempt number
                $failureReason = 'PayPal batch payout failed';

                if ($this->attemptNumber === 3) {
                    // Final attempt - cancel all payouts in the batch
                    foreach ($payouts as $payout) {
                        $payout->update([
                            'status' => PayoutStatus::CANCELLED,
                            'failure_reason' => $failureReason.' (Final attempt)',
                            'failed_at' => now(),
                        ]);
                    }

                    Log::error('ProcessReferralPayouts: Batch cancelled after final attempt', [
                        'batch_size' => count($payouts),
                    ]);
                } else {
                    // Keep status as PENDING for retry
                    Log::warning('ProcessReferralPayouts: Batch failed, will retry', [
                        'batch_size' => count($payouts),
                        'attempt_number' => $this->attemptNumber,
                    ]);
                }

                return;
            }

            // Batch succeeded - update all payouts with batch ID and mark as paid
            $batchHeader = $result['batch_header'] ?? [];
            $payoutBatchId = $batchHeader['payout_batch_id'] ?? null;

            foreach ($payouts as $payout) {
                $payout->update([
                    'status' => PayoutStatus::PAID,
                    'paypal_batch_id' => $payoutBatchId,
                    'paid_at' => now(),
                ]);
            }

            Log::info('ProcessReferralPayouts: Batch successful', [
                'batch_size' => count($payouts),
                'batch_id' => $payoutBatchId,
            ]);
        } catch (\Exception $e) {
            $failureReason = 'Exception: '.$e->getMessage();

            if ($this->attemptNumber === 3) {
                // Final attempt - cancel all payouts
                foreach ($payouts as $payout) {
                    $payout->update([
                        'status' => PayoutStatus::CANCELLED,
                        'failure_reason' => $failureReason,
                        'failed_at' => now(),
                    ]);
                }

                Log::error('ProcessReferralPayouts: Batch cancelled due to exception', [
                    'batch_size' => count($payouts),
                    'error' => $e->getMessage(),
                ]);
            } else {
                // Keep status as PENDING for retry
                Log::error('ProcessReferralPayouts: Exception during batch processing', [
                    'batch_size' => count($payouts),
                    'attempt_number' => $this->attemptNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
