<?php

namespace App\Console\Commands;

use App\Services\EmailSequenceService;
use Illuminate\Console\Command;

class ProcessEmailSequenceSends extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-sequences:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending email sequence sends';

    /**
     * Execute the console command.
     */
    public function handle(EmailSequenceService $service): void
    {
        $this->info('Processing pending email sequence sends...');

        $count = $service->processPendingSends();

        $this->info("Processed {$count} pending email sends");
    }
}
