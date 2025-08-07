<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing business users with profiles
        $businessUsers = User::where('account_type', AccountType::BUSINESS)
            ->whereHas('businessProfile')
            ->with('businessProfile')
            ->get();

        if ($businessUsers->isEmpty()) {
            $this->command->info('No business users found. Creating sample campaigns with new businesses.');

            return;
        }

        // Campaign templates by industry
        $campaignTemplates = [
            'fashion' => [
                [
                    'goal' => 'Spring Collection Launch',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Showcase our latest spring fashion collection. Perfect for fashion influencers with 10K+ followers.',
                    'compensation_range' => [500, 1500],
                    'influencers_range' => [3, 8],
                ],
                [
                    'goal' => 'Summer Style Guide',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Review and promote our summer clothing line. Great for lifestyle and fashion content creators.',
                    'compensation_range' => [300, 800],
                    'influencers_range' => [2, 5],
                ],
                [
                    'goal' => 'Fashion Week Coverage',
                    'type' => CampaignType::EVENT_COVERAGE,
                    'description' => 'Cover our fashion week events and showcase our runway looks.',
                    'compensation_range' => [800, 2000],
                    'influencers_range' => [5, 10],
                ],
            ],
            'beauty' => [
                [
                    'goal' => 'Skincare Routine Campaign',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Review our new skincare line and share your beauty routine.',
                    'compensation_range' => [400, 1000],
                    'influencers_range' => [3, 6],
                ],
                [
                    'goal' => 'Makeup Tutorial Series',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Create makeup tutorials featuring our products.',
                    'compensation_range' => [600, 1200],
                    'influencers_range' => [4, 8],
                ],
            ],
            'fitness' => [
                [
                    'goal' => 'Fitness Challenge Campaign',
                    'type' => CampaignType::BRAND_PARTNERSHIPS,
                    'description' => 'Promote our 30-day fitness challenge. Perfect for health and wellness influencers.',
                    'compensation_range' => [800, 2000],
                    'influencers_range' => [5, 12],
                ],
                [
                    'goal' => 'Workout Series',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Create workout videos featuring our fitness equipment and programs.',
                    'compensation_range' => [500, 1200],
                    'influencers_range' => [3, 8],
                ],
            ],
            'food' => [
                [
                    'goal' => 'Restaurant Review Series',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Review our menu items and share your dining experience.',
                    'compensation_range' => [300, 800],
                    'influencers_range' => [4, 8],
                ],
                [
                    'goal' => 'Cooking Class Promotion',
                    'type' => CampaignType::EVENT_COVERAGE,
                    'description' => 'Cover our cooking classes and culinary events.',
                    'compensation_range' => [400, 1000],
                    'influencers_range' => [3, 6],
                ],
            ],
            'home' => [
                [
                    'goal' => 'Home Styling Series',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Showcase our home decor products in your space.',
                    'compensation_range' => [600, 1200],
                    'influencers_range' => [3, 7],
                ],
                [
                    'goal' => 'DIY Project Campaign',
                    'type' => CampaignType::USER_GENERATED,
                    'description' => 'Create DIY projects using our home improvement products.',
                    'compensation_range' => [400, 900],
                    'influencers_range' => [2, 5],
                ],
            ],
            'technology' => [
                [
                    'goal' => 'Product Review Campaign',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Review our latest tech products and share your experience.',
                    'compensation_range' => [700, 1500],
                    'influencers_range' => [3, 8],
                ],
                [
                    'goal' => 'Tech Tutorial Series',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Create tutorials featuring our technology products.',
                    'compensation_range' => [500, 1200],
                    'influencers_range' => [4, 10],
                ],
            ],
            'automotive' => [
                [
                    'goal' => 'Car Review Series',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Review our vehicles and share your driving experience.',
                    'compensation_range' => [1000, 2500],
                    'influencers_range' => [2, 6],
                ],
            ],
            'health' => [
                [
                    'goal' => 'Wellness Campaign',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Promote our health and wellness products.',
                    'compensation_range' => [600, 1200],
                    'influencers_range' => [3, 7],
                ],
            ],
            'education' => [
                [
                    'goal' => 'Learning Series',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Create educational content featuring our learning resources.',
                    'compensation_range' => [400, 900],
                    'influencers_range' => [2, 6],
                ],
            ],
            'real_estate' => [
                [
                    'goal' => 'Property Showcase',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Showcase our properties and share real estate insights.',
                    'compensation_range' => [800, 1800],
                    'influencers_range' => [2, 5],
                ],
            ],
            'retail' => [
                [
                    'goal' => 'Store Experience Campaign',
                    'type' => CampaignType::EVENT_COVERAGE,
                    'description' => 'Share your shopping experience at our stores.',
                    'compensation_range' => [300, 700],
                    'influencers_range' => [4, 8],
                ],
            ],
            'hospitality' => [
                [
                    'goal' => 'Hotel Experience',
                    'type' => CampaignType::EVENT_COVERAGE,
                    'description' => 'Share your stay experience at our properties.',
                    'compensation_range' => [500, 1200],
                    'influencers_range' => [3, 6],
                ],
            ],
            'finance' => [
                [
                    'goal' => 'Financial Education',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Create content about financial literacy and our services.',
                    'compensation_range' => [600, 1200],
                    'influencers_range' => [2, 5],
                ],
            ],
        ];

        $compensationTypes = CompensationType::cases();
        $campaignTypes = CampaignType::cases();

        foreach ($businessUsers as $businessUser) {
            $industry = $businessUser->businessProfile->industry?->value ?? 'fashion';

            // Get templates for this industry, or use fashion as fallback
            $templates = $campaignTemplates[$industry] ?? $campaignTemplates['fashion'];

            // Create 1-3 campaigns per business
            $numCampaigns = rand(1, 3);

            for ($i = 0; $i < $numCampaigns; $i++) {
                $template = $templates[array_rand($templates)];

                // Random compensation type
                $compensationType = $compensationTypes[array_rand($compensationTypes)];

                // Random compensation amount based on type
                $compensationAmount = $this->getCompensationAmount($compensationType, $template['compensation_range']);

                // Random dates within next 2 months
                $applicationDeadline = now()->addDays(rand(7, 60));
                $campaignCompletion = $applicationDeadline->copy()->addDays(rand(14, 45));
                $publishedAt = now()->subDays(rand(1, 14));

                // Random target zip code (using business zip or random)
                $targetZip = $businessUser->businessProfile->primary_zip_code ?? '12345';

                Campaign::factory()->withFullDetails()->create([
                    'user_id' => $businessUser->id,
                    'status' => CampaignStatus::PUBLISHED,
                    'campaign_goal' => $template['goal'],
                    'campaign_type' => $template['type'],
                    'campaign_description' => $template['description'],
                    'influencer_count' => rand($template['influencers_range'][0], $template['influencers_range'][1]),
                    'target_zip_code' => $targetZip,
                    'application_deadline' => $applicationDeadline,
                    'campaign_completion_date' => $campaignCompletion,
                    'published_at' => $publishedAt,
                ]);
            }
        }

        $this->command->info('Created '.Campaign::where('status', CampaignStatus::PUBLISHED)->count().' published campaigns.');
    }

    private function getCompensationAmount(CompensationType $type, array $range): int
    {
        return match ($type) {
            CompensationType::MONETARY => rand($range[0], $range[1]),
            CompensationType::BARTER => rand(200, 800),
            CompensationType::FREE_PRODUCT => rand(100, 500),
            CompensationType::DISCOUNT => rand(50, 200),
            CompensationType::GIFT_CARD => rand(100, 500),
            CompensationType::EXPERIENCE => rand(300, 1000),
            CompensationType::OTHER => rand(200, 600),
        };
    }

    private function getCompensationDescription(CompensationType $type, int $amount): string
    {
        return match ($type) {
            CompensationType::MONETARY => '$'.number_format($amount).' payment',
            CompensationType::BARTER => 'Product exchange worth $'.number_format($amount),
            CompensationType::FREE_PRODUCT => 'Free products worth $'.number_format($amount),
            CompensationType::DISCOUNT => $amount.'% discount on all products',
            CompensationType::GIFT_CARD => '$'.number_format($amount).' gift card',
            CompensationType::EXPERIENCE => 'Experience package worth $'.number_format($amount),
            CompensationType::OTHER => 'Custom compensation arrangement',
        };
    }
}
