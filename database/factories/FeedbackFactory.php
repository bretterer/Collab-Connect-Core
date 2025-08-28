<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'type' => $this->faker->randomElement(\App\Enums\FeedbackType::cases()),
            'subject' => $this->faker->sentence(4),
            'message' => $this->faker->paragraphs(2, true),
            'url' => $this->faker->url(),
            'browser_info' => [
                'user_agent' => $this->faker->userAgent(),
                'ip_address' => $this->faker->ipv4(),
                'referrer' => $this->faker->url(),
            ],
            'session_data' => [
                'current_url' => $this->faker->url(),
                'previous_url' => $this->faker->url(),
                'timestamp' => now()->toISOString(),
            ],
            'resolved' => $this->faker->boolean(20), // 20% chance of being resolved
            'admin_notes' => $this->faker->optional(0.3)->sentence(),
            'resolved_at' => function (array $attributes) {
                return $attributes['resolved'] ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
            },
        ];
    }
}
