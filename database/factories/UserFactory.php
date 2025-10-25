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
     * Create a user with a profile based on the current account type.
     * This method should be chained after business() or influencer().
     */
    public function withProfile(array $profileAttributes = []): static
    {
        return $this->afterCreating(function ($user) use ($profileAttributes) {
            if ($user->account_type === AccountType::BUSINESS) {
                // Create a Business and link it to the user
                $business = \App\Models\Business::factory()->create(
                    array_merge(['onboarding_complete' => true], $profileAttributes)
                );

                // Create the business-user relationship
                \App\Models\BusinessUser::create([
                    'business_id' => $business->id,
                    'user_id' => $user->id,
                    'role' => 'owner',
                ]);

                // Set as current business
                $user->setCurrentBusiness($business);

            } elseif ($user->account_type === AccountType::INFLUENCER) {
                // Create an Influencer and link it to the user
                $influencer = \App\Models\Influencer::factory()->create(
                    array_merge(['user_id' => $user->id, 'onboarding_complete' => true], $profileAttributes)
                );

            }
        });
    }

    /**
     * Create a user with an active subscription.
     * This method should be chained after business() or influencer() and withProfile().
     */
    public function subscribed(): static
    {
        return $this->afterCreating(function ($user) {
            // Determine the billable model
            $billable = null;
            if ($user->account_type === AccountType::BUSINESS) {
                $billable = $user->currentBusiness;
            } elseif ($user->account_type === AccountType::INFLUENCER) {
                $billable = $user->influencer;
            }

            if ($billable) {
                // Create a subscription record directly in the database
                // This bypasses Stripe for testing purposes
                $billable->subscriptions()->create([
                    'type' => 'default',
                    'stripe_id' => 'sub_test_' . Str::random(14),
                    'stripe_status' => 'active',
                    'stripe_price' => 'price_test_' . Str::random(14),
                    'quantity' => 1,
                    'trial_ends_at' => null,
                    'ends_at' => null,
                ]);
            }
        });
    }
}
