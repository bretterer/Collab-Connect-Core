<?php

namespace Database\Factories;

use App\Models\Collaboration;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaboration_id' => Collaboration::factory(),
            'reviewer_id' => User::factory(),
            'reviewee_id' => User::factory(),
            'reviewer_type' => $this->faker->randomElement(['business', 'influencer']),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional(0.8)->paragraph(),
            'submitted_at' => now(),
        ];
    }

    /**
     * Review from a business.
     */
    public function fromBusiness(): static
    {
        return $this->state(fn (array $attributes) => [
            'reviewer_type' => 'business',
        ]);
    }

    /**
     * Review from an influencer.
     */
    public function fromInfluencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'reviewer_type' => 'influencer',
        ]);
    }

    /**
     * Review with a specific rating.
     */
    public function withRating(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $rating,
        ]);
    }

    /**
     * Review without a comment.
     */
    public function withoutComment(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment' => null,
        ]);
    }

    /**
     * Unsubmitted review (still in draft).
     */
    public function unsubmitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'submitted_at' => null,
        ]);
    }
}
