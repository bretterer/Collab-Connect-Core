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

        foreach ($payouts as $payout) {
            $this->processPayout($payout, $paypalService);
        }

        Log::info('ProcessReferralPayouts: Finished processing payouts');
    }

    /**
     * Process a single payout.
     */
    protected function processPayout(ReferralPayout $payout, PayPalPayoutsService $paypalService): void
    {
        $enrollment = $payout->enrollment;

        // Check if enrollment is disabled
        if ($enrollment->disabled_at !== null) {
            Log::warning('ProcessReferralPayouts: Enrollment is disabled', [
                'payout_id' => $payout->id,
                'enrollment_id' => $enrollment->id,
            ]);

            if ($this->attemptNumber === 3) {
                $payout->update([
                    'status' => PayoutStatus::CANCELLED,
                    'failure_reason' => 'Enrollment is disabled',
                    'failed_at' => now(),
                ]);
            }

            return;
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

            return;
        }

        // Attempt to process the payout via PayPal
        try {
            $result = $paypalService->createPayout(
                $enrollment,
                (float) $payout->amount,
                "Referral Commission - {$payout->month_year}"
            );

            if ($result === null) {
                // Payout failed
                $failureReason = 'PayPal payout failed';

                if ($this->attemptNumber === 3) {
                    // Final attempt - mark as cancelled
                    $payout->update([
                        'status' => PayoutStatus::CANCELLED,
                        'failure_reason' => $failureReason.' (Final attempt)',
                        'failed_at' => now(),
                    ]);

                    Log::error('ProcessReferralPayouts: Payout cancelled after final attempt', [
                        'payout_id' => $payout->id,
                        'enrollment_id' => $enrollment->id,
                    ]);
                } else {
                    // Mark as failed for retry
                    $payout->update([
                        'status' => PayoutStatus::FAILED,
                        'failure_reason' => $failureReason." (Attempt {$this->attemptNumber})",
                        'failed_at' => now(),
                    ]);

                    Log::warning('ProcessReferralPayouts: Payout failed, will retry', [
                        'payout_id' => $payout->id,
                        'attempt_number' => $this->attemptNumber,
                    ]);
                }

                return;
            }

            // Payout succeeded
            $batchHeader = $result['batch_header'] ?? [];
            $payoutBatchId = $batchHeader['payout_batch_id'] ?? null;

            // Get transaction ID from first item
            $items = $result['items'] ?? [];
            $transactionId = null;
            if (! empty($items[0])) {
                $transactionId = $items[0]['payout_item_id'] ?? $items[0]['transaction_id'] ?? null;
            }

            $payout->update([
                'status' => PayoutStatus::PAID,
                'paypal_batch_id' => $payoutBatchId,
                'paypal_transaction_id' => $transactionId,
                'paid_at' => now(),
            ]);

            Log::info('ProcessReferralPayouts: Payout successful', [
                'payout_id' => $payout->id,
                'transaction_id' => $transactionId,
                'batch_id' => $payoutBatchId,
            ]);
        } catch (\Exception $e) {
            $failureReason = 'Exception: '.$e->getMessage();

            if ($this->attemptNumber === 3) {
                // Final attempt - mark as cancelled
                $payout->update([
                    'status' => PayoutStatus::CANCELLED,
                    'failure_reason' => $failureReason,
                    'failed_at' => now(),
                ]);

                Log::error('ProcessReferralPayouts: Payout cancelled due to exception', [
                    'payout_id' => $payout->id,
                    'error' => $e->getMessage(),
                ]);
            } else {
                // Mark as failed for retry
                $payout->update([
                    'status' => PayoutStatus::FAILED,
                    'failure_reason' => $failureReason,
                    'failed_at' => now(),
                ]);

                Log::error('ProcessReferralPayouts: Exception during payout processing', [
                    'payout_id' => $payout->id,
                    'attempt_number' => $this->attemptNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
