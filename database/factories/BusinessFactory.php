<?php

namespace Database\Factories;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompanySize;
use App\Enums\ContactRole;
use App\Enums\YearsInBusiness;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'primary_contact' => $this->faker->name(),
            'contact_role' => $this->faker->randomElement(ContactRole::cases())->value,
            'maturity' => $this->faker->randomElement(YearsInBusiness::cases())->value,
            'size' => $this->faker->randomElement(CompanySize::cases())->value,
            'type' => $this->faker->randomElement(BusinessType::cases()),
            'industry' => $this->faker->randomElement(BusinessIndustry::cases()),
            'description' => $this->faker->paragraph(),
            'selling_points' => $this->faker->sentence(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'target_age_range' => $this->faker->randomElements(['18-24', '25-34', '35-44', '45-54'], $this->faker->numberBetween(1, 3)),
            'target_gender' => $this->faker->randomElements(['male', 'female', 'non-binary'], $this->faker->numberBetween(1, 2)),
            'business_goals' => $this->faker->randomElements(['brand_awareness', 'product_promotion', 'growth_scaling'], $this->faker->numberBetween(1, 3)),
            'platforms' => $this->faker->randomElements(['instagram', 'facebook', 'tiktok', 'youtube'], $this->faker->numberBetween(1, 4)),
            'onboarding_complete' => false,
        ];
    }
}
