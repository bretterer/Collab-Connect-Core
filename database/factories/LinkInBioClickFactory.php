<?php

namespace Database\Factories;

use App\Models\LinkInBioClick;
use App\Models\LinkInBioSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkInBioClick>
 */
class LinkInBioClickFactory extends Factory
{
    protected $model = LinkInBioClick::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $deviceTypes = ['mobile', 'desktop', 'tablet'];
        $linkTitles = ['Instagram', 'TikTok', 'YouTube', 'Twitter', 'Website', 'Shop'];

        return [
            'link_in_bio_settings_id' => LinkInBioSettings::factory(),
            'link_index' => fake()->numberBetween(0, 5),
            'link_title' => fake()->randomElement($linkTitles),
            'link_url' => fake()->url(),
            'ip_hash' => hash('sha256', fake()->ipv4().config('app.key')),
            'user_agent' => fake()->userAgent(),
            'device_type' => fake()->randomElement($deviceTypes),
            'clicked_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Set the click to a specific link index.
     */
    public function forLink(int $index, string $title, string $url): static
    {
        return $this->state(fn (array $attributes) => [
            'link_index' => $index,
            'link_title' => $title,
            'link_url' => $url,
        ]);
    }

    /**
     * Set the click to be from a specific date.
     */
    public function clickedAt(\DateTimeInterface $date): static
    {
        return $this->state(fn (array $attributes) => [
            'clicked_at' => $date,
        ]);
    }

    /**
     * Indicate that the click is from a mobile device.
     */
    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'mobile',
        ]);
    }
}
