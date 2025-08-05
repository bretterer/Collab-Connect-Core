<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['campaign_application', 'campaign_published', 'campaign_updated']),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'data' => null,
            'action_url' => $this->faker->optional()->url(),
            'is_read' => $this->faker->boolean(20), // 20% chance of being read
            'read_at' => null,
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create a campaign application notification.
     */
    public function campaignApplication(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'campaign_application',
            'title' => 'New Campaign Application',
            'message' => 'An influencer has applied to your campaign.',
            'data' => [
                'campaign_id' => $this->faker->numberBetween(1, 100),
                'applicant_id' => $this->faker->numberBetween(1, 50),
            ],
            'action_url' => '/campaigns/' . $this->faker->numberBetween(1, 100) . '/applications',
        ]);
    }
}