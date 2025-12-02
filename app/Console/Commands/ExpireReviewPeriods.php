<?php

namespace App\Console\Commands;

use App\Services\ReviewService;
use Illuminate\Console\Command;

class ExpireReviewPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:expire-periods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire review periods that have passed their 15-day window';

    /**
     * Execute the console command.
     */
    public function handle(ReviewService $reviewService): int
    {
        $this->info('Checking for expired review periods...');

        $count = $reviewService->expireOverdueReviewPeriods();

        if ($count > 0) {
            $this->info("Expired {$count} review period(s).");
        } else {
            $this->info('No review periods to expire.');
        }

        return Command::SUCCESS;
    }
}
