<?php

namespace App\Console\Commands;

use Database\Seeders\ReferralSeeder;
use Illuminate\Console\Command;

class SeedReferrals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:seed
                            {--user-id= : The ID of the user to create referrals for}
                            {--user-email= : The email of the user to create referrals for}
                            {--count=10 : Number of referrals to create}
                            {--status=active : Status of the referrals (active, pending, churned, cancelled)}
                            {--amount=1000 : Subscription amount in cents (e.g., 1000 = $10.00)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed referrals for a specific user for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user-id') ? (int) $this->option('user-id') : null;
        $userEmail = $this->option('user-email');
        $count = (int) $this->option('count');
        $status = $this->option('status');
        $amount = (int) $this->option('amount');

        $seeder = new ReferralSeeder;
        $seeder->setCommand($this);
        $seeder->run(
            userId: $userId,
            userEmail: $userEmail,
            count: $count,
            status: $status,
            subscriptionAmount: $amount
        );

        return Command::SUCCESS;
    }
}
