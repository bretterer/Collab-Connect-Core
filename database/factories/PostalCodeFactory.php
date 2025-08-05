<?php

namespace Database\Factories;

use App\Models\PostalCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostalCode>
 */
class PostalCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = PostalCode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_code' => 'US',
            'postal_code' => $this->faker->postcode(),
            'place_name' => $this->faker->city(),
            'admin_name1' => $this->faker->randomElement([
                'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut',
                'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa',
                'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan',
                'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire',
                'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio',
                'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota',
                'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia',
                'Wisconsin', 'Wyoming'
            ]),
            'admin_code1' => $this->faker->randomElement([
                'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN',
                'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV',
                'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN',
                'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'
            ]),
            'admin_name2' => $this->faker->city() . ' County',
            'admin_code2' => $this->faker->numerify('###'),
            'admin_name3' => null,
            'admin_code3' => null,
            'latitude' => (float) $this->faker->latitude(25, 49), // US latitude range
            'longitude' => (float) $this->faker->longitude(-125, -66), // US longitude range
            'accuracy' => $this->faker->randomElement([1, 4, 6]),
        ];
    }

    /**
     * Indicate that the postal code is in Canada.
     */
    public function canada(): static
    {
        return $this->state(fn (array $attributes) => [
            'country_code' => 'CA',
            'postal_code' => $this->faker->regexify('[A-Z][0-9][A-Z] [0-9][A-Z][0-9]'),
            'admin_name1' => $this->faker->randomElement([
                'Ontario', 'Quebec', 'British Columbia', 'Alberta', 'Manitoba',
                'Saskatchewan', 'Nova Scotia', 'New Brunswick', 'Newfoundland and Labrador',
                'Prince Edward Island', 'Northwest Territories', 'Nunavut', 'Yukon'
            ]),
            'admin_code1' => $this->faker->randomElement([
                'ON', 'QC', 'BC', 'AB', 'MB', 'SK', 'NS', 'NB', 'NL', 'PE', 'NT', 'NU', 'YT'
            ]),
            'latitude' => (float) $this->faker->latitude(42, 70), // Canada latitude range
            'longitude' => (float) $this->faker->longitude(-141, -52), // Canada longitude range
        ]);
    }

    /**
     * Indicate that the postal code is in New York.
     */
    public function newYork(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_name1' => 'New York',
            'admin_code1' => 'NY',
            'latitude' => (float) $this->faker->latitude(40.4, 45.0), // NY latitude range
            'longitude' => (float) $this->faker->longitude(-79.8, -71.8), // NY longitude range
        ]);
    }

    /**
     * Indicate that the postal code is in California.
     */
    public function california(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_name1' => 'California',
            'admin_code1' => 'CA',
            'latitude' => (float) $this->faker->latitude(32.5, 42.0), // CA latitude range
            'longitude' => (float) $this->faker->longitude(-124.4, -114.1), // CA longitude range
        ]);
    }

    /**
     * Indicate that the postal code has no coordinates.
     */
    public function withoutCoordinates(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    /**
     * Indicate that the postal code has specific coordinates.
     */
    public function withCoordinates(float $latitude, float $longitude): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    /**
     * Indicate that the postal code is in a specific place.
     */
    public function inPlace(string $placeName, string $stateName = null, string $stateCode = null): static
    {
        return $this->state(fn (array $attributes) => [
            'place_name' => $placeName,
            'admin_name1' => $stateName ?? $attributes['admin_name1'],
            'admin_code1' => $stateCode ?? $attributes['admin_code1'],
        ]);
    }

    /**
     * Indicate that the postal code has a specific postal code.
     */
    public function withPostalCode(string $postalCode): static
    {
        return $this->state(fn (array $attributes) => [
            'postal_code' => $postalCode,
        ]);
    }
}