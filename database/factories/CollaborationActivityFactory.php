<?php

namespace Database\Factories;

use App\Enums\CollaborationActivityType;
use App\Models\Collaboration;
use App\Models\CollaborationActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CollaborationActivity>
 */
class CollaborationActivityFactory extends Factory
{
    protected $model = CollaborationActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaboration_id' => Collaboration::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(CollaborationActivityType::cases()),
            'metadata' => null,
            'created_at' => now(),
        ];
    }

    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'type' => CollaborationActivityType::STARTED,
            'metadata' => null,
        ]);
    }

    public function deliverableSubmitted(?string $deliverableType = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CollaborationActivityType::DELIVERABLE_SUBMITTED,
            'metadata' => $deliverableType ? ['deliverable_type' => $deliverableType] : null,
        ]);
    }

    public function deliverableApproved(?string $deliverableType = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CollaborationActivityType::DELIVERABLE_APPROVED,
            'metadata' => $deliverableType ? ['deliverable_type' => $deliverableType] : null,
        ]);
    }

    public function revisionRequested(?string $deliverableType = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CollaborationActivityType::REVISION_REQUESTED,
            'metadata' => $deliverableType ? ['deliverable_type' => $deliverableType] : null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'type' => CollaborationActivityType::COMPLETED,
            'metadata' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CollaborationActivityType::CANCELLED,
            'metadata' => null,
        ]);
    }

    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }
}
