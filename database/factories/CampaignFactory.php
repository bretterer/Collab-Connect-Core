<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Models\Campaign;
use App\Models\User;
use App\Services\CampaignService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => CampaignStatus::DRAFT,
            'campaign_goal' => $this->faker->sentence(),
            'campaign_type' => $this->faker->randomElement(CampaignType::cases()),
            'target_zip_code' => $this->faker->numerify('#####'),
            'target_area' => $this->faker->city(),
            'campaign_description' => $this->faker->paragraph(),
            'influencer_count' => $this->faker->numberBetween(1, 10),
            'application_deadline' => $this->faker->dateTimeBetween('now', '+30 days'),
            'campaign_completion_date' => $this->faker->dateTimeBetween('+30 days', '+90 days'),
            'publish_action' => 'publish',
            'scheduled_date' => null,
            'current_step' => 4,
            'published_at' => null,
        ];
    }

    /**
     * Indicate that the campaign is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CampaignStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the campaign is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the campaign is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CampaignStatus::ARCHIVED,
        ]);
    }

    /**
     * Create a complete campaign with all relationships
     */
    public function withFullDetails(): static
    {
        return $this->afterCreating(function (Campaign $campaign) {
            // Create campaign brief
            $campaign->brief()->create([
                'project_name' => $this->faker->words(3, true),
                'main_contact' => $this->faker->name(),
                'campaign_objective' => $this->faker->sentence(),
                'key_insights' => $this->faker->paragraph(),
                'fan_motivator' => $this->faker->sentence(),
                'creative_connection' => $this->faker->paragraph(),
                'target_audience' => $this->faker->sentence(),
                'timing_details' => $this->faker->sentence(),
                'additional_requirements' => $this->faker->optional()->paragraph(),
            ]);

            // Create campaign brand
            $campaign->brand()->create([
                'brand_overview' => $this->faker->paragraph(),
                'brand_essence' => $this->faker->sentence(),
                'brand_pillars' => ['quality', 'innovation', 'community'],
                'current_advertising_campaign' => $this->faker->sentence(),
                'brand_story' => $this->faker->paragraph(),
                'brand_guidelines' => $this->faker->paragraph(),
            ]);

            // Create campaign requirements
            $campaign->requirements()->create([
                'social_requirements' => [
                    'platforms' => ['instagram', 'tiktok'],
                    'post_count' => $this->faker->numberBetween(1, 5),
                ],
                'placement_requirements' => [
                    'feed_posts' => true,
                    'stories' => false,
                    'reels' => true,
                ],
                'target_platforms' => ['instagram', 'tiktok'],
                'deliverables' => ['instagram_reel', 'instagram_story'],
                'success_metrics' => ['impressions', 'engagement_rate'],
                'content_guidelines' => $this->faker->paragraph(),
                'posting_restrictions' => $this->faker->optional()->sentence(),
                'specific_products' => $this->faker->optional()->sentence(),
                'additional_considerations' => $this->faker->optional()->paragraph(),
            ]);

            // Create campaign compensation
            $compensationType = $this->faker->randomElement([
                CompensationType::MONETARY,
                CompensationType::BARTER,
                CompensationType::FREE_PRODUCT,
                CompensationType::GIFT_CARD
            ]);
            
            $compensationAmount = $this->faker->numberBetween(100, 2000);
            
            $campaign->compensation()->create([
                'compensation_type' => $compensationType,
                'compensation_amount' => $compensationAmount,
                'compensation_description' => $this->getCompensationDescription($compensationType, $compensationAmount),
                'compensation_details' => [
                    'base_rate' => $compensationAmount,
                    'bonus_available' => $this->faker->boolean(),
                ],
            ]);
        });
    }

    private function getCompensationDescription(CompensationType $type, int $amount): string
    {
        return match($type) {
            CompensationType::MONETARY => '$' . number_format($amount) . ' payment',
            CompensationType::BARTER => 'Product exchange worth $' . number_format($amount),
            CompensationType::FREE_PRODUCT => 'Free products worth $' . number_format($amount),
            CompensationType::DISCOUNT => $amount . '% discount on all products',
            CompensationType::GIFT_CARD => '$' . number_format($amount) . ' gift card',
            CompensationType::EXPERIENCE => 'Experience package worth $' . number_format($amount),
            CompensationType::OTHER => 'Custom compensation arrangement',
        };
    }
}