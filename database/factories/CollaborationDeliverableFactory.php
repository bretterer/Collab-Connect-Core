<?php

namespace Database\Factories;

use App\Enums\CollaborationDeliverableStatus;
use App\Enums\DeliverableType;
use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CollaborationDeliverable>
 */
class CollaborationDeliverableFactory extends Factory
{
    protected $model = CollaborationDeliverable::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaboration_id' => Collaboration::factory(),
            'deliverable_type' => fake()->randomElement(DeliverableType::cases()),
            'status' => CollaborationDeliverableStatus::NOT_STARTED,
            'submitted_at' => null,
            'approved_at' => null,
            'post_url' => null,
            'notes' => null,
            'revision_feedback' => null,
        ];
    }

    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationDeliverableStatus::NOT_STARTED,
            'submitted_at' => null,
            'approved_at' => null,
            'post_url' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationDeliverableStatus::IN_PROGRESS,
            'submitted_at' => null,
            'approved_at' => null,
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationDeliverableStatus::SUBMITTED,
            'submitted_at' => now(),
            'approved_at' => null,
            'post_url' => 'https://instagram.com/p/'.fake()->uuid(),
            'notes' => fake()->optional()->sentence(),
        ]);
    }

    public function revisionRequested(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationDeliverableStatus::REVISION_REQUESTED,
            'submitted_at' => now()->subDays(2),
            'approved_at' => null,
            'post_url' => 'https://instagram.com/p/'.fake()->uuid(),
            'revision_feedback' => fake()->sentence(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CollaborationDeliverableStatus::APPROVED,
            'submitted_at' => now()->subDays(2),
            'approved_at' => now(),
            'post_url' => 'https://instagram.com/p/'.fake()->uuid(),
        ]);
    }

    public function instagramPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'deliverable_type' => DeliverableType::INSTAGRAM_POST,
        ]);
    }

    public function instagramReel(): static
    {
        return $this->state(fn (array $attributes) => [
            'deliverable_type' => DeliverableType::INSTAGRAM_REEL,
        ]);
    }

    public function tiktokVideo(): static
    {
        return $this->state(fn (array $attributes) => [
            'deliverable_type' => DeliverableType::TIKTOK_VIDEO,
        ]);
    }
}
