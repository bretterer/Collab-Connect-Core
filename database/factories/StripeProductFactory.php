<?php

namespace Database\Factories;

use App\Models\StripeProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StripeProduct>
 */
class StripeProductFactory extends Factory
{
    protected $model = StripeProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stripe_id' => 'prod_' . $this->faker->unique()->bothify('??????????????'),
            'name' => $this->faker->words(3, true),
            'active' => true,
            'description' => $this->faker->sentence(),
            'metadata' => [
                'account_type' => $this->faker->randomElement(['BUSINESS', 'INFLUENCER'])
            ],
            'livemode' => false,
        ];
    }

    public function inactive(): self
    {
        return $this->state(['active' => false]);
    }

    public function business(): self
    {
        return $this->state([
            'metadata' => ['account_type' => 'BUSINESS']
        ]);
    }

    public function influencer(): self
    {
        return $this->state([
            'metadata' => ['account_type' => 'INFLUENCER']
        ]);
    }

    public function livemode(): self
    {
        return $this->state(['livemode' => true]);
    }
}
