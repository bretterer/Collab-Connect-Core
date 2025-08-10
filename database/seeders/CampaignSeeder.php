<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Enums\DeliverableType;
use App\Enums\SuccessMetric;
use App\Enums\TargetPlatform;
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
        // Get the default business user first
        $defaultBusinessUser = User::where('email', env('INIT_BUSINESS_EMAIL'))
            ->where('account_type', AccountType::BUSINESS)
            ->whereHas('businessProfile')
            ->with('businessProfile')
            ->first();

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
            'food_service' => [
                [
                    'goal' => 'Farm-to-Table Experience',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Showcase our locally-sourced ingredients and artisanal cooking in your content. Perfect for food bloggers and lifestyle influencers.',
                    'compensation_range' => [150, 300],
                    'influencers_range' => [3, 6],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
                [
                    'goal' => 'Seasonal Menu Launch',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Help us introduce our new seasonal menu items through authentic food photography and reviews.',
                    'compensation_range' => [200, 400],
                    'influencers_range' => [2, 4],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
                [
                    'goal' => 'Coffee Culture Campaign',
                    'type' => CampaignType::USER_GENERATED,
                    'description' => 'Share your coffee ritual and workspace moments featuring our signature blends and pastries.',
                    'compensation_range' => [100, 250],
                    'influencers_range' => [4, 8],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
            ],
            'boutique' => [
                [
                    'goal' => 'Curated Style Collection',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Style and review pieces from our carefully curated collection. Perfect for fashion and lifestyle influencers.',
                    'compensation_range' => [200, 500],
                    'influencers_range' => [2, 5],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
                [
                    'goal' => 'Local Designer Spotlight',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Showcase our local designer pieces and share the stories behind each unique item.',
                    'compensation_range' => [300, 600],
                    'influencers_range' => [3, 6],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
                [
                    'goal' => 'Sustainable Fashion Movement',
                    'type' => CampaignType::BRAND_PARTNERSHIPS,
                    'description' => 'Partner with us to promote sustainable fashion choices and eco-friendly wardrobe building.',
                    'compensation_range' => [250, 450],
                    'influencers_range' => [2, 4],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
            ],
            'salon' => [
                [
                    'goal' => 'Hair Transformation Journey',
                    'type' => CampaignType::PRODUCT_REVIEWS,
                    'description' => 'Document your hair transformation experience and review our professional treatments and products.',
                    'compensation_range' => [150, 350],
                    'influencers_range' => [2, 4],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
                [
                    'goal' => 'Self-Care Sunday Series',
                    'type' => CampaignType::SPONSORED_POSTS,
                    'description' => 'Create content around self-care routines featuring our spa services and take-home products.',
                    'compensation_range' => [200, 400],
                    'influencers_range' => [3, 6],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
                [
                    'goal' => 'Wedding Beauty Prep',
                    'type' => CampaignType::EVENT_COVERAGE,
                    'description' => 'Share your wedding beauty preparation journey featuring our bridal packages and products.',
                    'compensation_range' => [300, 600],
                    'influencers_range' => [1, 3],
                    'compensation_type' => CompensationType::FREE_PRODUCT,
                ],
            ],
        ];

        $compensationTypes = CompensationType::cases();
        $campaignTypes = CampaignType::cases();
        $campaignStatuses = [
            CampaignStatus::PUBLISHED,
            CampaignStatus::DRAFT,
            CampaignStatus::SCHEDULED,
            CampaignStatus::ARCHIVED
        ];

        foreach ($businessUsers as $businessUser) {
            $industry = $businessUser->businessProfile->industry?->value ?? 'fashion';

            // Get templates for this industry, or use fashion as fallback
            $templates = $campaignTemplates[$industry] ?? $campaignTemplates['fashion'];

            // Create 1-3 campaigns per business
            $numCampaigns = rand(1, 3);

            for ($i = 0; $i < $numCampaigns; $i++) {
                $template = $templates[array_rand($templates)];

                // Use template compensation type if specified, otherwise random
                $compensationType = $template['compensation_type'] ?? $compensationTypes[array_rand($compensationTypes)];

                // Random compensation amount based on type
                $compensationAmount = $this->getCompensationAmount($compensationType, $template['compensation_range']);

                // Random campaign status
                $campaignStatus = $campaignStatuses[array_rand($campaignStatuses)];
                
                // Set dates and status-specific fields based on campaign status
                [$applicationDeadline, $campaignCompletion, $publishedAt, $scheduledDate, $currentStep] = $this->getDatesForStatus($campaignStatus);

                // Random target zip code (using business zip or random)
                $targetZip = $businessUser->businessProfile->primary_zip_code ?? '12345';

                // Generate comprehensive campaign data
                $influencerCount = rand($template['influencers_range'][0], $template['influencers_range'][1]);
                $targetPlatforms = $this->getRandomTargetPlatforms();
                $deliverables = $this->getRandomDeliverables($targetPlatforms);
                $successMetrics = $this->getRandomSuccessMetrics();
                
                Campaign::factory()->withFullDetails()->create([
                    'user_id' => $businessUser->id,
                    'status' => $campaignStatus,
                    'campaign_goal' => $template['goal'],
                    'campaign_type' => $template['type'],
                    'target_zip_code' => $targetZip,
                    'target_area' => $this->getTargetArea(),
                    'campaign_description' => $template['description'],
                    'influencer_count' => $influencerCount,
                    'application_deadline' => $applicationDeadline,
                    'campaign_completion_date' => $campaignCompletion,
                    'publish_action' => $campaignStatus === CampaignStatus::SCHEDULED ? 'publish_later' : 'publish_now',
                    'scheduled_date' => $scheduledDate,
                    'current_step' => $currentStep,
                    'published_at' => $publishedAt,
                    'compensation_type' => $compensationType,
                    'compensation_amount' => $compensationAmount,
                    'compensation_description' => $this->getCompensationDescription($compensationType, $compensationAmount),
                    'compensation_details' => $this->getCompensationDetails($compensationType, $compensationAmount),
                    'brand_overview' => $this->getBrandOverview($businessUser->businessProfile),
                    'current_advertising_campaign' => $this->getCurrentAdvertisingCampaign(),
                    'brand_story' => $this->getBrandStory($businessUser->businessProfile),
                    'campaign_objective' => $this->getCampaignObjective($template['type']),
                    'key_insights' => $this->getKeyInsights(),
                    'fan_motivator' => $this->getFanMotivator(),
                    'creative_connection' => $this->getCreativeConnection(),
                    'specific_products' => $this->getSpecificProducts(),
                    'posting_restrictions' => $this->getPostingRestrictions(),
                    'additional_considerations' => $this->getAdditionalConsiderations(),
                    'target_platforms' => $targetPlatforms,
                    'deliverables' => $deliverables,
                    'success_metrics' => $successMetrics,
                    'timing_details' => $this->getTimingDetails($applicationDeadline, $campaignCompletion),
                    'target_audience' => $this->getTargetAudience(),
                    'content_guidelines' => $this->getContentGuidelines(),
                    'brand_guidelines' => $this->getBrandGuidelines(),
                    'main_contact' => $this->getMainContact($businessUser),
                    'project_name' => $template['goal'],
                    'social_requirements' => $this->getSocialRequirements($influencerCount),
                    'placement_requirements' => $this->getPlacementRequirements(),
                    'additional_requirements' => $this->getAdditionalRequirements(),
                ]);
            }
        }

        // Create specific campaigns for the default business user to showcase gifted product campaigns
        if ($defaultBusinessUser) {
            $giftingIndustries = ['food_service', 'boutique', 'salon'];
            
            foreach ($giftingIndustries as $industry) {
                $templates = $campaignTemplates[$industry];
                
                // Create one campaign from each industry template
                foreach ($templates as $template) {
                    $compensationType = $template['compensation_type'] ?? CompensationType::FREE_PRODUCT;
                    $compensationAmount = $this->getCompensationAmount($compensationType, $template['compensation_range']);
                    
                    // Random campaign status (bias towards published for gifting campaigns)
                    $giftingStatuses = [
                        CampaignStatus::PUBLISHED,
                        CampaignStatus::PUBLISHED, // More published campaigns
                        CampaignStatus::DRAFT,
                        CampaignStatus::SCHEDULED,
                    ];
                    $campaignStatus = $giftingStatuses[array_rand($giftingStatuses)];
                    
                    // Set dates and status-specific fields based on campaign status
                    [$applicationDeadline, $campaignCompletion, $publishedAt, $scheduledDate, $currentStep] = $this->getDatesForStatus($campaignStatus);
                    
                    // Random target zip code (using business zip or random)
                    $targetZip = $defaultBusinessUser->businessProfile->primary_zip_code ?? '12345';
                    
                    // Generate comprehensive campaign data
                    $influencerCount = rand($template['influencers_range'][0], $template['influencers_range'][1]);
                    $targetPlatforms = $this->getRandomTargetPlatforms();
                    $deliverables = $this->getRandomDeliverables($targetPlatforms);
                    $successMetrics = $this->getRandomSuccessMetrics();
                    
                    Campaign::factory()->withFullDetails()->create([
                        'user_id' => $defaultBusinessUser->id,
                        'status' => $campaignStatus,
                        'campaign_goal' => $template['goal'],
                        'campaign_type' => $template['type'],
                        'target_zip_code' => $targetZip,
                        'target_area' => $this->getTargetArea(),
                        'campaign_description' => $template['description'],
                        'influencer_count' => $influencerCount,
                        'application_deadline' => $applicationDeadline,
                        'campaign_completion_date' => $campaignCompletion,
                        'publish_action' => $campaignStatus === CampaignStatus::SCHEDULED ? 'publish_later' : 'publish_now',
                        'scheduled_date' => $scheduledDate,
                        'current_step' => $currentStep,
                        'published_at' => $publishedAt,
                        'compensation_type' => $compensationType,
                        'compensation_amount' => $compensationAmount,
                        'compensation_description' => $this->getGiftingCompensationDescription($industry, $compensationAmount),
                        'compensation_details' => $this->getGiftingCompensationDetails($industry, $compensationAmount),
                        'brand_overview' => $this->getGiftingBrandOverview($industry),
                        'current_advertising_campaign' => $this->getCurrentAdvertisingCampaign(),
                        'brand_story' => $this->getGiftingBrandStory($industry),
                        'campaign_objective' => $this->getCampaignObjective($template['type']),
                        'key_insights' => $this->getGiftingKeyInsights($industry),
                        'fan_motivator' => $this->getGiftingFanMotivator($industry),
                        'creative_connection' => $this->getGiftingCreativeConnection($industry),
                        'specific_products' => $this->getGiftingSpecificProducts($industry),
                        'posting_restrictions' => $this->getPostingRestrictions(),
                        'additional_considerations' => $this->getGiftingAdditionalConsiderations($industry),
                        'target_platforms' => $targetPlatforms,
                        'deliverables' => $deliverables,
                        'success_metrics' => $successMetrics,
                        'timing_details' => $this->getTimingDetails($applicationDeadline, $campaignCompletion),
                        'target_audience' => $this->getGiftingTargetAudience($industry),
                        'content_guidelines' => $this->getContentGuidelines(),
                        'brand_guidelines' => $this->getBrandGuidelines(),
                        'main_contact' => $this->getMainContact($defaultBusinessUser),
                        'project_name' => $template['goal'],
                        'social_requirements' => $this->getSocialRequirements($influencerCount),
                        'placement_requirements' => $this->getPlacementRequirements(),
                        'additional_requirements' => $this->getAdditionalRequirements(),
                    ]);
                }
            }
            
            $this->command->info('Created additional gifting campaigns for default business user.');
        }

        $publishedCount = Campaign::where('status', CampaignStatus::PUBLISHED)->count();
        $draftCount = Campaign::where('status', CampaignStatus::DRAFT)->count(); 
        $scheduledCount = Campaign::where('status', CampaignStatus::SCHEDULED)->count();
        $archivedCount = Campaign::where('status', CampaignStatus::ARCHIVED)->count();
        
        $this->command->info("Created {$publishedCount} published, {$draftCount} draft, {$scheduledCount} scheduled, and {$archivedCount} archived campaigns.");
    }

    private function getDatesForStatus(CampaignStatus $status): array
    {
        return match ($status) {
            CampaignStatus::PUBLISHED => [
                now()->addDays(rand(7, 60)), // application_deadline
                now()->addDays(rand(75, 120)), // campaign_completion_date
                now()->subDays(rand(1, 14)), // published_at
                null, // scheduled_date
                4 // current_step (completed)
            ],
            CampaignStatus::DRAFT => [
                now()->addDays(rand(7, 60)), // application_deadline
                now()->addDays(rand(75, 120)), // campaign_completion_date
                null, // published_at
                null, // scheduled_date
                rand(1, 3) // current_step (incomplete)
            ],
            CampaignStatus::SCHEDULED => [
                now()->addDays(rand(7, 60)), // application_deadline
                now()->addDays(rand(75, 120)), // campaign_completion_date
                null, // published_at
                now()->addDays(rand(1, 30)), // scheduled_date
                4 // current_step (completed, ready to publish)
            ],
            CampaignStatus::ARCHIVED => [
                now()->subDays(rand(60, 180)), // application_deadline (past)
                now()->subDays(rand(30, 120)), // campaign_completion_date (past)
                now()->subDays(rand(180, 365)), // published_at (long ago)
                null, // scheduled_date
                4 // current_step (was completed)
            ],
        };
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

    private function getRandomTargetPlatforms(): array
    {
        $platforms = TargetPlatform::cases();
        $selectedPlatforms = [];
        
        // Select 1-3 random platforms
        $count = rand(1, 3);
        $randomPlatforms = array_rand($platforms, $count);
        
        if (!is_array($randomPlatforms)) {
            $randomPlatforms = [$randomPlatforms];
        }
        
        foreach ($randomPlatforms as $index) {
            $selectedPlatforms[] = $platforms[$index]->value;
        }
        
        return $selectedPlatforms;
    }

    private function getRandomDeliverables(array $targetPlatforms): array
    {
        $deliverables = DeliverableType::cases();
        $selectedDeliverables = [];
        
        // Select 1-4 random deliverables
        $count = rand(1, 4);
        $randomDeliverables = array_rand($deliverables, min($count, count($deliverables)));
        
        if (!is_array($randomDeliverables)) {
            $randomDeliverables = [$randomDeliverables];
        }
        
        foreach ($randomDeliverables as $index) {
            $selectedDeliverables[] = $deliverables[$index]->value;
        }
        
        return $selectedDeliverables;
    }

    private function getRandomSuccessMetrics(): array
    {
        $metrics = SuccessMetric::cases();
        $selectedMetrics = [];
        
        // Select 2-5 random metrics
        $count = rand(2, 5);
        $randomMetrics = array_rand($metrics, min($count, count($metrics)));
        
        if (!is_array($randomMetrics)) {
            $randomMetrics = [$randomMetrics];
        }
        
        foreach ($randomMetrics as $index) {
            $selectedMetrics[] = $metrics[$index]->value;
        }
        
        return $selectedMetrics;
    }

    private function getTargetArea(): string
    {
        $areas = [
            'Local Community',
            'Greater Metro Area',
            'State-wide',
            'Regional',
            'National',
            'Within 25 miles',
            'Within 50 miles',
            'Downtown District',
            'Suburban Areas'
        ];
        
        return $areas[array_rand($areas)];
    }

    private function getCompensationDetails($type, $amount): array
    {
        return match ($type) {
            CompensationType::MONETARY => [
                'payment_method' => 'bank_transfer',
                'payment_timeline' => 'within_30_days',
                'tax_form_required' => $amount >= 600
            ],
            CompensationType::FREE_PRODUCT => [
                'product_selection' => 'from_current_collection',
                'retail_value' => $amount,
                'shipping_included' => true
            ],
            CompensationType::GIFT_CARD => [
                'card_type' => 'digital',
                'expiration' => '12_months',
                'stackable' => true
            ],
            default => [
                'details' => 'To be discussed upon acceptance',
                'estimated_value' => $amount
            ]
        };
    }

    private function getBrandOverview($businessProfile): string
    {
        $businessName = $businessProfile->business_name ?? 'Our Company';
        $industry = $businessProfile->industry?->label() ?? 'Business';
        
        return "At {$businessName}, we're a leading {$industry} company dedicated to delivering exceptional products and experiences to our customers. We've built a strong reputation for quality, innovation, and customer satisfaction in our industry.";
    }

    private function getCurrentAdvertisingCampaign(): string
    {
        $campaigns = [
            "We're currently running our Spring Launch campaign focusing on new product awareness and customer acquisition.",
            "Our ongoing brand awareness initiative emphasizes community engagement and authentic storytelling.",
            "Currently promoting our seasonal collection through multi-channel marketing efforts.",
            "Running a customer loyalty campaign to increase repeat purchases and brand advocacy.",
            "Focused on digital-first marketing strategy to reach younger demographics.",
        ];
        
        return $campaigns[array_rand($campaigns)];
    }

    private function getBrandStory($businessProfile): string
    {
        $businessName = $businessProfile->business_name ?? 'Our Company';
        
        return "Founded with a passion for excellence, {$businessName} started as a vision to create products that make a difference in people's lives. Our journey has been driven by innovation, quality, and a commitment to our community. Today, we continue to grow while staying true to our core values and mission.";
    }

    private function getCampaignObjective($campaignType): string
    {
        return match ($campaignType) {
            CampaignType::SPONSORED_POSTS => 'Increase brand awareness and drive engagement through authentic influencer content that showcases our products in real-life scenarios.',
            CampaignType::PRODUCT_REVIEWS => 'Generate authentic product reviews and testimonials to build trust and credibility with potential customers.',
            CampaignType::EVENT_COVERAGE => 'Create buzz and excitement around our event while reaching new audiences through influencer coverage.',
            CampaignType::USER_GENERATED => 'Encourage user-generated content that showcases our products being used and loved by real customers.',
            CampaignType::BRAND_PARTNERSHIPS => 'Establish long-term partnerships with influencers who align with our brand values and can become brand ambassadors.',
            default => 'Increase brand awareness and engagement through authentic influencer partnerships.'
        };
    }

    private function getKeyInsights(): string
    {
        $insights = [
            "Our target audience responds well to authentic, unfiltered content that shows real product usage.",
            "Visual storytelling performs better than traditional advertising approaches with our demographic.",
            "Content that includes tutorials or how-to elements drives higher engagement rates.",
            "Our community values transparency and behind-the-scenes content.",
            "User-generated content featuring our products has 3x higher engagement than brand-created content."
        ];
        
        return $insights[array_rand($insights)];
    }

    private function getFanMotivator(): string
    {
        $motivators = [
            "Quality products that enhance their lifestyle and provide real value",
            "Being part of a community that shares similar values and interests",
            "Access to exclusive products and experiences not available elsewhere", 
            "Supporting a brand that aligns with their personal values and beliefs",
            "The confidence that comes from using trusted, high-quality products"
        ];
        
        return $motivators[array_rand($motivators)];
    }

    private function getCreativeConnection(): string
    {
        $connections = [
            "Show how our products seamlessly fit into your daily routine and enhance your lifestyle",
            "Share your authentic experience and genuine reactions to our products",
            "Create content that tells a story about how our products solve real problems",
            "Demonstrate the quality and craftsmanship that sets our products apart",
            "Connect with your audience by sharing why you personally love our brand"
        ];
        
        return $connections[array_rand($connections)];
    }

    private function getSpecificProducts(): string
    {
        $products = [
            "Our latest seasonal collection featuring new colors and designs",
            "Flagship products from our bestselling line that define our brand",
            "New product launch items that we're excited to introduce to the market",
            "Customer favorite products with proven track record of satisfaction",
            "Limited edition items that create exclusivity and urgency"
        ];
        
        return $products[array_rand($products)];
    }

    private function getPostingRestrictions(): string
    {
        $restrictions = [
            "Please avoid posting near competing brands or products in the same category",
            "Maintain family-friendly content that aligns with our brand values",
            "No controversial topics or divisive political content in posts featuring our products",
            "Ensure all claims about product benefits are accurate and not exaggerated",
            "Include proper disclaimers and hashtags as required by FTC guidelines"
        ];
        
        return $restrictions[array_rand($restrictions)];
    }

    private function getAdditionalConsiderations(): string
    {
        $considerations = [
            "We value long-term partnerships and may extend collaboration opportunities for top performers",
            "High-performing content may be featured on our official brand channels with proper credit",
            "We encourage creative freedom while maintaining brand consistency in messaging",
            "Please allow 3-5 business days for content approval before posting",
            "We provide detailed product information and talking points to support content creation"
        ];
        
        return $considerations[array_rand($considerations)];
    }

    private function getTimingDetails($applicationDeadline, $campaignCompletion): string
    {
        return "Applications close on {$applicationDeadline->format('F j, Y')}. Selected influencers will be notified within 5 business days. Content creation and posting should be completed by {$campaignCompletion->format('F j, Y')}.";
    }

    private function getTargetAudience(): string
    {
        $audiences = [
            "Ages 18-34, primarily female, interested in fashion and lifestyle content",
            "Health-conscious individuals aged 25-45 who value wellness and fitness",
            "Parents aged 28-40 looking for family-friendly products and experiences", 
            "Young professionals aged 22-35 who appreciate quality and convenience",
            "Creative individuals aged 20-40 who value unique and innovative products"
        ];
        
        return $audiences[array_rand($audiences)];
    }

    private function getContentGuidelines(): string
    {
        return "Please maintain authentic tone and voice while naturally incorporating our products. Use high-quality images with good lighting. Include lifestyle shots showing products in use. Mention specific product features and benefits that resonate with you personally.";
    }

    private function getBrandGuidelines(): string
    {
        return "Use our official brand colors and fonts when possible. Tag our official social media accounts. Include required hashtags and disclaimers. Maintain consistent messaging about our brand values. Ensure content reflects our premium positioning.";
    }

    private function getMainContact($businessUser): string
    {
        return $businessUser->name . " (" . $businessUser->email . ")";
    }

    private function getSocialRequirements($influencerCount): array
    {
        $baseFollowers = [
            'instagram' => rand(10000, 100000),
            'tiktok' => rand(25000, 500000),
            'youtube' => rand(5000, 50000)
        ];
        
        return [
            'minimum_followers' => $baseFollowers,
            'engagement_rate' => 'minimum_2_percent',
            'audience_demographics' => 'primarily_us_based',
            'content_quality' => 'professional_standard'
        ];
    }

    private function getPlacementRequirements(): array
    {
        return [
            'post_timing' => 'peak_engagement_hours',
            'hashtag_requirements' => ['#sponsored', '#ad', '#partnership'],
            'mention_requirements' => 'tag_brand_account',
            'content_approval' => 'required_before_posting'
        ];
    }

    private function getAdditionalRequirements(): array
    {
        return [
            'content_rights' => 'usage_rights_granted',
            'exclusivity_period' => '30_days_same_category',
            'reporting_required' => 'performance_metrics_within_48_hours',
            'compliance' => 'ftc_guidelines_mandatory'
        ];
    }

    private function getGiftingCompensationDescription(string $industry, int $amount): string
    {
        return match ($industry) {
            'food_service' => "Complimentary dining experience and take-home products worth $".number_format($amount),
            'boutique' => "Curated fashion pieces and accessories worth $".number_format($amount),
            'salon' => "Premium beauty treatments and product package worth $".number_format($amount),
            default => "Gifted products and services worth $".number_format($amount),
        };
    }

    private function getGiftingCompensationDetails(string $industry, int $amount): array
    {
        return match ($industry) {
            'food_service' => [
                'includes' => 'Full meal experience for 2 people plus take-home specialty items',
                'estimated_value' => $amount,
                'additional_perks' => 'Recipe cards and chef recommendations',
                'pickup_required' => true
            ],
            'boutique' => [
                'includes' => 'Choice of 2-3 pieces from current collection',
                'estimated_value' => $amount,
                'size_consultation' => 'Personal styling session included',
                'exchange_policy' => '30 days for size exchanges'
            ],
            'salon' => [
                'includes' => 'Full service treatment plus take-home product kit',
                'estimated_value' => $amount,
                'appointment_required' => 'Must schedule 48 hours in advance',
                'aftercare_included' => 'Professional maintenance tips'
            ],
            default => [
                'estimated_value' => $amount,
                'details' => 'Product selection to be discussed'
            ]
        };
    }

    private function getGiftingBrandOverview(string $industry): string
    {
        return match ($industry) {
            'food_service' => "We're a locally-owned restaurant specializing in farm-to-table cuisine that celebrates seasonal ingredients and artisanal craftsmanship. Our commitment to quality and community has made us a beloved gathering place for food enthusiasts.",
            'boutique' => "Our boutique curates unique, sustainable fashion pieces from emerging and established designers who share our values of quality, creativity, and ethical production. We believe in fashion that tells a story and makes a statement.",
            'salon' => "As a full-service salon and spa, we specialize in personalized beauty experiences using premium, cruelty-free products. Our team of expert stylists and aestheticians are passionate about helping clients look and feel their absolute best.",
            default => "We're committed to delivering exceptional products and experiences to our valued customers."
        };
    }

    private function getGiftingBrandStory(string $industry): string
    {
        return match ($industry) {
            'food_service' => "What started as a passion for bringing people together over exceptional food has grown into a cornerstone of our local culinary scene. We source from local farms, work with talented chefs, and create experiences that nourish both body and soul.",
            'boutique' => "Born from a love of unique fashion and sustainable practices, our boutique represents more than just clothing - it's a celebration of individual style and conscious consumerism. Every piece in our collection is chosen for its quality, story, and positive impact.",
            'salon' => "Founded by master stylists with a vision for personalized beauty, we've built our reputation on exceptional service and genuine care for each client's unique needs. We believe beauty is about confidence, self-expression, and feeling amazing in your own skin.",
            default => "Our story is one of passion, quality, and dedication to our community."
        };
    }

    private function getGiftingKeyInsights(string $industry): string
    {
        return match ($industry) {
            'food_service' => "Food content that shows the full experience - from the ambiance and presentation to the actual taste and quality - resonates most with our audience. Behind-the-scenes content and chef interactions perform exceptionally well.",
            'boutique' => "Style content that shows versatility and real-world wearability performs better than traditional fashion photography. Our customers love seeing how pieces can be styled for different occasions and personal aesthetics.",
            'salon' => "Before and after transformations generate incredible engagement, but the process and education around techniques and products create lasting connections with potential clients seeking similar results.",
            default => "Authentic experiences and genuine reactions drive the highest engagement with our brand."
        };
    }

    private function getGiftingFanMotivator(string $industry): string
    {
        return match ($industry) {
            'food_service' => "The joy of discovering exceptional flavors and supporting local, sustainable dining experiences that bring people together over memorable meals.",
            'boutique' => "Finding unique pieces that express their personal style while supporting ethical fashion practices and emerging designers.",
            'salon' => "The confidence that comes from professional beauty treatments and the self-care ritual that enhances their natural beauty and personal wellness.",
            default => "Quality experiences that enhance their lifestyle and align with their values."
        };
    }

    private function getGiftingCreativeConnection(string $industry): string
    {
        return match ($industry) {
            'food_service' => "Share the complete dining journey - the atmosphere, the artful presentation, your genuine reactions to flavors, and how the experience made you feel. Food is about more than taste; it's about connection and community.",
            'boutique' => "Show how our pieces integrate with your personal style and lifestyle. Mix and match items, demonstrate versatility, and share why certain pieces spoke to you. Fashion should feel personal and authentic.",
            'salon' => "Document your transformation journey and the boost in confidence that follows. Share the techniques you learned, the products you loved, and how the experience enhanced your self-care routine.",
            default => "Create authentic content that shows how our products genuinely enhance your lifestyle."
        };
    }

    private function getGiftingSpecificProducts(string $industry): string
    {
        return match ($industry) {
            'food_service' => "Featured seasonal menu items, signature craft beverages, artisanal pastries, and take-home specialty products like house-made sauces or coffee blends.",
            'boutique' => "Current seasonal collection pieces, statement accessories, sustainable fashion basics, and limited-edition designer collaborations that reflect our brand aesthetic.",
            'salon' => "Signature treatments like custom color services, rejuvenating facials, professional styling sessions, and our curated selection of premium take-home beauty products.",
            default => "Our featured products and services that best represent our brand quality and values."
        };
    }

    private function getGiftingAdditionalConsiderations(string $industry): string
    {
        return match ($industry) {
            'food_service' => "Please coordinate visit timing with our team to ensure optimal experience and photo opportunities. We're happy to provide ingredient lists for dietary considerations and recipe inspirations for content creation.",
            'boutique' => "We offer personal styling consultations to help select pieces that photograph beautifully and suit your aesthetic. Size exchanges are available, and we can provide garment care instructions for longevity.",
            'salon' => "Consultation appointments are required to customize services to your needs and desired outcomes. We provide professional maintenance tips and product recommendations to extend your results.",
            default => "We're committed to ensuring you have everything needed for successful content creation and a positive brand experience."
        };
    }

    private function getGiftingTargetAudience(string $industry): string
    {
        return match ($industry) {
            'food_service' => "Food enthusiasts and lifestyle influencers aged 25-45 who appreciate culinary artistry, local dining experiences, and sustainable food practices.",
            'boutique' => "Fashion-forward individuals aged 22-40 who value unique style, sustainable fashion, and supporting independent designers and small businesses.",
            'salon' => "Beauty and lifestyle enthusiasts aged 25-50 who prioritize self-care, professional beauty treatments, and high-quality beauty products and services.",
            default => "Individuals who appreciate quality products and authentic brand experiences."
        };
    }
}
