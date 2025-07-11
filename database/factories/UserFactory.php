<?php

namespace Database\Factories;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'account_type' => AccountType::UNDEFINED->value,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model is an influencer.
     */
    public function influencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::INFLUENCER->value,
        ]);
    }

    /**
     * Indicate that the model is a business.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::BUSINESS->value,
        ]);
    }

    /**
     * Indicate that the model is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::ADMIN->value,
        ]);
    }

    /**
     * Create a user with completed business onboarding.
     */
    public function withBusinessProfile(array $businessAttributes = []): static
    {
        return $this->business()->afterCreating(function ($user) use ($businessAttributes) {
            $user->businessProfile()->create(
                array_merge(
                    \Database\Factories\BusinessProfileFactory::new()->make()->toArray(),
                    $businessAttributes
                )
            );
        });
    }

    /**
     * Create a user with completed influencer onboarding.
     */
    public function withInfluencerProfile(array $influencerAttributes = []): static
    {
        return $this->influencer()->afterCreating(function ($user) use ($influencerAttributes) {
            $user->influencerProfile()->create(
                array_merge(
                    \Database\Factories\InfluencerProfileFactory::new()->make()->toArray(),
                    $influencerAttributes
                )
            );
        });
    }

    /**
     * Alias for withBusinessProfile() - creates a business user with completed onboarding.
     */
    public function businessComplete(array $businessAttributes = []): static
    {
        return $this->withBusinessProfile($businessAttributes);
    }

    /**
     * Alias for withInfluencerProfile() - creates an influencer user with completed onboarding.
     */
    public function influencerComplete(array $influencerAttributes = []): static
    {
        return $this->withInfluencerProfile($influencerAttributes);
    }
}
