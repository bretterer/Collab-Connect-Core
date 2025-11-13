<?php

namespace App\Console\Commands;

use App\Enums\BusinessIndustry;
use App\Enums\CampaignType;
use App\Facades\MatchScore;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Console\Command;

class CreateMatchingCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collabconnect:create-matching-campaign {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a matching campaign for influencer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::query()->find($userId);
        $businessUser = User::query()->where('email', config('collabconnect.init_business_email'))->first();

        if (! $user->isInfluencerAccount()) {
            $this->error("User ID: {$userId} is not an influencer.");

            return Command::FAILURE;
        }

        $businessProfile = $businessUser->currentBusiness;
        $businessProfile->industry = BusinessIndustry::FITNESS_WELLNESS;
        $businessProfile->save();

        $user->influencer->primary_industry = BusinessIndustry::FITNESS_WELLNESS;
        $user->influencer->save();

        $campaign = $businessUser->currentBusiness->campaigns()->create(
            Campaign::factory()->make([
                'target_zip_code' => $user->influencer->postal_code,
                'campaign_type' => CampaignType::BRAND_PARTNERSHIPS,
                'compensation_type' => fake()->randomElement($user->influencer->compensation_types),
                'compensation_amount' => 1000,
                'campaign_goal' => fake()->sentence(),
                'status' => \App\Enums\CampaignStatus::PUBLISHED, // Published status
            ])->toArray()
        );

        $this->info('Match percentage: '.MatchScore::calculateMatchScore($campaign, $user->influencer));

        return Command::SUCCESS;
    }
}
