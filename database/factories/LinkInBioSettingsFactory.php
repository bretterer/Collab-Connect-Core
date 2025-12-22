<?php

namespace Database\Factories;

use App\Models\LinkInBioSettings;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkInBioSettings>
 */
class LinkInBioSettingsFactory extends Factory
{
    protected $model = LinkInBioSettings::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'influencer_id' => function () {
                $user = User::factory()->influencer()->withProfile()->create();
                $user->influencer->update(['username' => 'testuser_'.uniqid()]);

                return $user->influencer->id;
            },
            'settings' => LinkInBioSettings::getDefaultSettings(),
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the settings are published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the settings are not published.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
