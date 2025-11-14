<?php

namespace Database\Factories;

use App\Models\Market;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Market>
 */
class MarketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Market::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city().' Metro Area',
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the market is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the market is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create a market with specific zipcodes.
     */
    public function withZipcodes(array $postalCodes): static
    {
        return $this->afterCreating(function (Market $market) use ($postalCodes) {
            foreach ($postalCodes as $postalCode) {
                $market->zipcodes()->create([
                    'postal_code' => $postalCode,
                ]);
            }
        });
    }
}
