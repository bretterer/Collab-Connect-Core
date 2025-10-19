<?php

namespace Database\Seeders;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Enums\DeliverableType;
use App\Enums\SuccessMetric;
use App\Enums\TargetPlatform;
use App\Models\User;
use App\Services\CampaignService;
use Illuminate\Database\Seeder;

class CampaignTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create a business user
        $businessUser = User::where('email', config('collabconnect.init_business_email'))->first();

        if (! $businessUser) {
            $businessUser = User::factory()->business()->create([
                'email' => config('collabconnect.init_business_email'),
                'name' => 'Collab Coffee',
            ]);
        }

        // Create the Collab Coffee campaign using CampaignService
        $campaignData = [
            // Core campaign data
            'campaign_goal' => 'increase brand awareness and drive community connection by highlighting moments that Collab Coffee makes better',
            'campaign_type' => CampaignType::USER_GENERATED,
            'target_zip_code' => '48823',
            'target_area' => 'East Lansing, Michigan',
            'campaign_description' => 'Show how Collab Coffee makes any moment better through authentic storytelling.',
            'influencer_count' => 5,
            'application_deadline' => '2025-02-15',
            'campaign_completion_date' => '2025-03-31',
            'publish_action' => 'publish',
            'current_step' => 4,

            // Brief information
            'project_name' => 'Collab Coffee Creator Brief',
            'main_contact' => 'Rita - Marketing Manager',
            'campaign_objective' => 'Show how Collab Coffee makes any moment/day/routine better',
            'key_insights' => 'The atmosphere in America has become pretty toxic and divisive from a political and cultural standpoint. And, people are feeling more alone and isolated than ever before, despite having a ton of "friends" and "followers." People are looking for a break from "breaking news" and to experience a little joy and personal connection.',
            'fan_motivator' => 'Every time I go to Collab Coffee I know I\'m going to leave feeling better/my day will be better than when I walked in (or drove up)',
            'creative_connection' => 'Everyone has a Collab Coffee story. Highlight those "moments" Collab Coffee can make (an occasion, a feeling, an action or a story) better. Examples: A reassuring ritual: you and a friend hitting a Collab Coffee after the gym. An occasion: Reuniting with an old friend or going shopping with your mom. A feeling: Needing a little mood uplift after working remote from your basement office all day long. A story: recovering after scoring a goal on your own net.',
            'target_audience' => 'Local food and lifestyle influencers with engaged followings in the Midwest region',
            'timing_details' => 'Campaign should run from x date to y date. Deliverable schedule: Round 1, Round 2, Approval, 1st post, reporting due, etc.',
            'additional_requirements' => 'Creators must be located in Michigan, Ohio, Illinois, Indiana, or Wisconsin. Preference for food and lifestyle content creators with authentic engagement.',

            // Brand information
            'brand_overview' => 'Collab Coffee, headquartered in East Lansing, Michigan, is a national coffee franchise dedicated to making life better with every cup. Since opening its first store in 1995, Collab Coffee has grown to over 430 locations across 13 states, driven by a simple mission: to support people in building a life they love. Known for its fun, flavorful beverages and unpretentious, people-first approach, the brand offers a welcoming space where customers feel seen, supported, and uplifted. 100% of our locations are locally owned and operated.',
            'brand_essence' => 'Collab Coffee lifts people up by creating good energy',
            'brand_pillars' => [
                'high_quality_products',
                'community_focused',
                'authentic_relationships',
                'local_ownership',
                'excellent_service',
                'fun_and_friendly',
            ],
            'current_advertising_campaign' => 'Collab Coffee\'s current campaign that we just launched is called "Moments," and it\'s about driving community and connection by highlighting moments that Collab Coffee makes better.',
            'brand_story' => 'Collab Coffee was founded with a handshake, a few thousand dollars, and a firm belief that we could energize the world. When Bob Fish and Mike McFall partnered all those years ago in a small East Lansing coffee shop, they did so with the notion that a store could serve more than just a cup of coffee, and that a business could provide a sense of belonging and connection. We strive to make everyone\'s lives a little bit better – our customers, our employees, and our partners – and we pour our heart and soul into everything we do.',
            'brand_guidelines' => 'Use playful, approachable, positive and thoughtful tone. Tag @collabcoffee and use #CollabCoffeeMoments',

            // Requirements
            'target_platforms' => [
                TargetPlatform::INSTAGRAM->value,
                TargetPlatform::TIKTOK->value,
            ],
            'deliverables' => [
                DeliverableType::INSTAGRAM_REEL->value,
                DeliverableType::INSTAGRAM_STORY->value,
                DeliverableType::TIKTOK_VIDEO->value,
            ],
            'success_metrics' => [
                SuccessMetric::IMPRESSIONS->value,
                SuccessMetric::ENGAGEMENT_RATE->value,
            ],
            'content_guidelines' => 'Content should be authentic, positive, and showcase real moments with Collab Coffee. Focus on community, connection, and the feeling of being uplifted.',
            'posting_restrictions' => 'Nothing political, polarizing or divisive, no offensive language',
            'specific_products' => 'Feel free to include products that are authentic to you and your story.',
            'additional_considerations' => 'While the approach is positive, that doesn\'t mean it has to be vanilla and boring. We definitely want to lean into the "playful" tone and appreciate creativity',

            // Compensation
            'compensation_type' => CompensationType::MONETARY,
            'compensation_amount' => 500,
            'compensation_description' => 'TBD - Competitive compensation based on reach and engagement',
            'compensation_details' => [
                'base_rate' => 500,
                'bonus_for_high_engagement' => true,
                'product_compensation' => 'Free drinks and merchandise',
            ],
        ];

        $campaign = CampaignService::saveDraft($businessUser, $campaignData);

        // Publish the campaign
        $campaign->update([
            'status' => CampaignStatus::PUBLISHED,
            'published_at' => now(),
        ]);

    }
}
