<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignApplication>
 */
class CampaignApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CampaignApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
            'message' => $this->faker->paragraphs(2, true),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
            'submitted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'reviewed_at' => null,
            'review_notes' => null,
        ];
    }

    /**
     * Indicate that the application is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'reviewed_at' => null,
            'review_notes' => null,
        ]);
    }

    /**
     * Indicate that the application is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'reviewed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'review_notes' => $this->faker->optional()->sentence(),
        ]);
    }

    /**
     * Indicate that the application is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'review_notes' => $this->faker->sentence(),
        ]);
    }
}