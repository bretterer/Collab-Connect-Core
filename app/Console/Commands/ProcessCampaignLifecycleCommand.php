<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCampaignLifecycle;
use Illuminate\Console\Command;

class ProcessCampaignLifecycleCommand extends Command
{
    protected $signature = 'campaigns:process-lifecycle {--sync : Run synchronously instead of dispatching to queue}';

    protected $description = 'Process all campaign lifecycle transitions (publish scheduled, start due, complete due)';

    public function handle(): int
    {
        if ($this->option('sync')) {
            $this->info('Processing campaign lifecycle transitions synchronously...');
            (new ProcessCampaignLifecycle)->handle();
            $this->info('Campaign lifecycle transitions processed successfully.');
        } else {
            $this->info('Dispatching campaign lifecycle job to queue...');
            ProcessCampaignLifecycle::dispatch();
            $this->info('Campaign lifecycle job dispatched successfully.');
        }

        return self::SUCCESS;
    }
}
