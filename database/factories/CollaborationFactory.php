<?php

namespace Database\Factories;

use App\Enums\CollaborationStatus;
use App\Models\Business;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Collaboration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Collaboration>
 */
class CollaborationFactory extends Factory
{
    protected $model = Collaboration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'campaign_application_id' => CampaignApplication::factory(),
            'influencer_id' => User::factory(),
            'business_id' => Business::factory(),
            'status' => CollaborationStatus::ACTIVE,
            'started_at' => now(),
            'completed_at' => null,
            'cancelled_at' => null,
            'deliverables_submitted_at' => null,
            'notes' => null,
            'cancellation_reason' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationStatus::ACTIVE,
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationStatus::COMPLETED,
            'started_at' => now()->subDays(30),
            'completed_at' => now(),
            'deliverables_submitted_at' => now()->subDays(5),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationStatus::CANCELLED,
            'started_at' => now()->subDays(15),
            'cancelled_at' => now(),
            'cancellation_reason' => $this->faker->sentence(),
        ]);
    }

    public function withDeliverables(): static
    {
        return $this->state(fn (array $attributes) => [
            'deliverables_submitted_at' => now(),
        ]);
    }
}
