<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Models\Business;
use App\Models\Campaign;
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
            'business_id' => Business::factory(),
            'status' => CampaignStatus::DRAFT,
            'campaign_goal' => $this->faker->sentence(),
            'campaign_type' => $this->faker->randomElements(
                array_map(fn ($case) => $case->value, CampaignType::cases()),
                $this->faker->numberBetween(1, 3)
            ),
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
     * Indicate that the campaign is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CampaignStatus::IN_PROGRESS,
            'published_at' => now()->subDays(14),
            'started_at' => now(),
            'campaign_start_date' => now()->toDateString(),
        ]);
    }

    /**
     * Indicate that the campaign is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CampaignStatus::COMPLETED,
            'published_at' => now()->subDays(60),
            'started_at' => now()->subDays(30),
            'completed_at' => now(),
            'campaign_start_date' => now()->subDays(30)->toDateString(),
            'campaign_completion_date' => now()->toDateString(),
        ]);
    }

    /**
     * Indicate that the campaign is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CampaignStatus::ARCHIVED,
            'archived_at' => now(),
        ]);
    }

    /**
     * Create a complete campaign with all relationships
     */
    public function withFullDetails(): static
    {
        return $this->afterCreating(function (Campaign $campaign) {});
    }
}
