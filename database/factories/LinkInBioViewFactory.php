<?php

namespace Database\Factories;

use App\Models\LinkInBioSettings;
use App\Models\LinkInBioView;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkInBioView>
 */
class LinkInBioViewFactory extends Factory
{
    protected $model = LinkInBioView::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $deviceTypes = ['mobile', 'desktop', 'tablet'];
        $referrers = [
            'https://instagram.com/',
            'https://twitter.com/',
            'https://facebook.com/',
            'https://tiktok.com/',
            'https://google.com/',
            null,
        ];

        $referrer = fake()->randomElement($referrers);

        return [
            'link_in_bio_settings_id' => LinkInBioSettings::factory(),
            'ip_hash' => hash('sha256', fake()->ipv4().config('app.key')),
            'user_agent' => fake()->userAgent(),
            'device_type' => fake()->randomElement($deviceTypes),
            'referrer' => $referrer,
            'referrer_domain' => $referrer ? parse_url($referrer, PHP_URL_HOST) : null,
            'is_unique' => fake()->boolean(70),
            'viewed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the view is unique.
     */
    public function unique(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_unique' => true,
        ]);
    }

    /**
     * Indicate that the view is from a mobile device.
     */
    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'mobile',
        ]);
    }

    /**
     * Indicate that the view is from a desktop device.
     */
    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'desktop',
        ]);
    }

    /**
     * Set the view to be from a specific date.
     */
    public function viewedAt(\DateTimeInterface $date): static
    {
        return $this->state(fn (array $attributes) => [
            'viewed_at' => $date,
        ]);
    }
}
