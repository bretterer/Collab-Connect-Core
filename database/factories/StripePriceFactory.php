<?php

namespace Database\Factories;

use App\Models\StripePrice;
use App\Models\StripeProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StripePrice>
 */
class StripePriceFactory extends Factory
{
    protected $model = StripePrice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isRecurring = $this->faker->boolean(70); // 70% chance of recurring

        return [
            'stripe_id' => 'price_'.$this->faker->unique()->bothify('??????????????'),
            'active' => true,
            'billing_scheme' => 'per_unit',
            'livemode' => false,
            'metadata' => [],
            'stripe_product_id' => StripeProduct::factory(),
            'recurring' => $isRecurring ? [
                'interval' => $this->faker->randomElement(['month', 'year']),
                'interval_count' => 1,
            ] : null,
            'type' => $isRecurring ? 'recurring' : 'one_time',
            'unit_amount' => $this->faker->numberBetween(500, 10000), // $5.00 to $100.00
        ];
    }

    public function inactive(): self
    {
        return $this->state(['active' => false]);
    }

    public function recurring(): self
    {
        return $this->state([
            'type' => 'recurring',
            'recurring' => [
                'interval' => 'month',
                'interval_count' => 1,
            ],
        ]);
    }

    public function oneTime(): self
    {
        return $this->state([
            'type' => 'one_time',
            'recurring' => null,
        ]);
    }

    public function withFeatures(array $features): self
    {
        return $this->state([
            'metadata' => ['features' => json_encode($features)],
        ]);
    }

    public function free(): self
    {
        return $this->state(['unit_amount' => 0]);
    }

    public function livemode(): self
    {
        return $this->state(['livemode' => true]);
    }

    public function forProduct(StripeProduct $product): self
    {
        return $this->state(['stripe_product_id' => $product->id]);
    }
}
