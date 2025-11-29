<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'type' => $this->faker->randomElement(['billing', 'shipping', 'billing_and_shipping']),
            'label' => $this->faker->optional()->word(),
            'street' => $this->faker->streetName(),
            'number' => $this->faker->buildingNumber(),
            'complement' => $this->faker->word(),
            'neighborhood' => $this->faker->word(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => 'USA',
            'is_primary' => true,
        ];
    }
}
