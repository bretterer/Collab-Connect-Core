<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\CampaignApplicationStatus;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all published campaigns
        $campaigns = Campaign::where('status', 'published')->get();

        if ($campaigns->isEmpty()) {
            $this->command->info('No published campaigns found. Skipping application seeding.');

            return;
        }

        // Get all influencer users with profiles
        $influencers = User::where('account_type', AccountType::INFLUENCER)
            ->whereHas('influencerProfile')
            ->with('influencerProfile')
            ->get();

        if ($influencers->isEmpty()) {
            $this->command->info('No influencer users found. Skipping application seeding.');

            return;
        }

        $totalApplications = 0;

        foreach ($campaigns as $campaign) {
            // Random number of applications per campaign (20-80% of the influencer count goal)
            $maxApplications = min(
                $influencers->count(),
                max(1, (int) ($campaign->influencer_count * fake()->randomFloat(2, 0.2, 0.8)))
            );

            $applicationCount = fake()->numberBetween(1, $maxApplications);

            // Select random influencers for this campaign (no duplicates)
            $selectedInfluencers = $influencers->random($applicationCount);

            foreach ($selectedInfluencers as $influencer) {
                // Check if application already exists for this campaign-user combination
                $existingApplication = CampaignApplication::where('campaign_id', $campaign->id)
                    ->where('user_id', $influencer->id)
                    ->first();

                if ($existingApplication) {
                    continue; // Skip if application already exists
                }

                // Generate realistic application messages based on campaign type
                $message = $this->generateApplicationMessage($campaign, $influencer);

                // Determine application status with realistic distribution
                $status = $this->determineApplicationStatus();

                // Set submission date (between campaign published and now, or up to application deadline)
                $startDate = $campaign->published_at ?? now()->subDays(30);
                $endDate = $campaign->application_deadline ?? now();

                // Ensure end date is after start date
                if ($endDate <= $startDate) {
                    $endDate = $startDate->copy()->addDays(7);
                }

                $submittedAt = fake()->dateTimeBetween($startDate, $endDate);

                $applicationData = [
                    'campaign_id' => $campaign->id,
                    'user_id' => $influencer->id,
                    'message' => $message,
                    'status' => $status,
                    'submitted_at' => $submittedAt,
                ];

                // Add review data for accepted/rejected applications
                if ($status !== CampaignApplicationStatus::PENDING) {
                    $reviewDate = now();
                    if ($submittedAt < now()) {
                        $reviewDate = fake()->dateTimeBetween($submittedAt, 'now');
                    }
                    $applicationData['reviewed_at'] = $reviewDate;

                    if ($status === CampaignApplicationStatus::REJECTED) {
                        $applicationData['review_notes'] = fake()->randomElement([
                            'Thank you for your application. We decided to go with influencers who have a different audience demographic.',
                            'Your content style doesn\'t align with our brand aesthetic for this particular campaign.',
                            'We received many qualified applications and had to make some difficult decisions.',
                            'Looking for influencers with higher engagement rates for this campaign.',
                            'We need influencers in a different geographic location for this campaign.',
                        ]);
                        $applicationData['rejected_at'] = $applicationData['reviewed_at'];
                    } else {
                        $applicationData['review_notes'] = fake()->optional(0.3)->randomElement([
                            'Great portfolio! Looking forward to working together.',
                            'Your content style is perfect for our brand.',
                            'Excited to collaborate on this campaign!',
                        ]);
                        $applicationData['accepted_at'] = $applicationData['reviewed_at'];
                    }
                }

                CampaignApplication::create($applicationData);
                $totalApplications++;
            }
        }

        $this->command->info("Created {$totalApplications} campaign applications across {$campaigns->count()} campaigns.");

        // Show breakdown by status
        $pendingCount = CampaignApplication::where('status', CampaignApplicationStatus::PENDING)->count();
        $acceptedCount = CampaignApplication::where('status', CampaignApplicationStatus::ACCEPTED)->count();
        $rejectedCount = CampaignApplication::where('status', CampaignApplicationStatus::REJECTED)->count();

        $this->command->info("Status breakdown: {$pendingCount} pending, {$acceptedCount} accepted, {$rejectedCount} rejected");
    }

    /**
     * Generate a realistic application message based on campaign details
     */
    private function generateApplicationMessage(Campaign $campaign, User $influencer): string
    {
        $templates = [
            "Hi! I'm really excited about your {campaign_goal} campaign. As a {niche} content creator with {follower_count}+ followers, I believe my audience would love your brand. I've been creating content in this space for {experience} years and have worked with similar brands before. Would love to discuss how we can collaborate!",

            "Hello! Your {campaign_goal} campaign caught my attention immediately. My content focuses on {niche} and I have {follower_count} engaged followers who trust my recommendations. I'd love to create authentic content that showcases your brand in a genuine way. Looking forward to potentially working together!",

            "Hi there! I'm interested in your {campaign_goal} campaign. I specialize in {niche} content and have built a community of {follower_count} followers who actively engage with my posts. My content style aligns well with what you're looking for, and I'd be thrilled to collaborate with your brand!",

            "Greetings! As a {niche} influencer with {follower_count} followers, I'm very interested in your {campaign_goal} campaign. I create high-quality content that resonates with my audience, and I believe we could create something amazing together. I'd love to hear more about your vision for this collaboration.",

            "Hello! Your {campaign_goal} campaign sounds fantastic! I'm a {niche} content creator with {follower_count} dedicated followers. My audience demographic seems to align perfectly with your target market. I'd be excited to create engaging content that drives real results for your brand.",
        ];

        $template = fake()->randomElement($templates);

        // Get influencer's primary social media platform for follower count
        $primarySocialAccount = $influencer->socialMediaAccounts()->where('is_primary', true)->first();
        $followerCount = $primarySocialAccount ? number_format($primarySocialAccount->follower_count) : '10K';

        // Get a niche based on influencer profile or generate one
        $niche = $influencer->influencerProfile->content_niches[0] ?? fake()->randomElement([
            'lifestyle', 'fashion', 'beauty', 'fitness', 'food', 'travel', 'tech', 'home decor',
        ]);

        $experience = fake()->numberBetween(1, 8);

        return str_replace([
            '{campaign_goal}',
            '{niche}',
            '{follower_count}',
            '{experience}',
        ], [
            strtolower($campaign->campaign_goal),
            $niche,
            $followerCount,
            $experience,
        ], $template);
    }

    /**
     * Determine application status with realistic distribution
     */
    private function determineApplicationStatus(): CampaignApplicationStatus
    {
        $random = fake()->numberBetween(1, 100);

        return match (true) {
            $random <= 60 => CampaignApplicationStatus::PENDING,   // 60% pending
            $random <= 80 => CampaignApplicationStatus::ACCEPTED,  // 20% accepted
            default => CampaignApplicationStatus::REJECTED,        // 20% rejected
        };
    }
}
