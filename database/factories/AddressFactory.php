<?php

namespace Database\Factories;

use Carbon\Carbon;
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
            'type' => fake()->randomElement(['billing', 'shipping', 'billing_and_shipping']),
            'label' => fake()->optional()->word(),
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'complement' => fake()->word(),
            'neighborhood' => fake()->word(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'USA',
            'is_primary' => true,
            'created_at' => Carbon::parse(now()),
            'updated_at' => Carbon::parse(now()),
        ];
    }
}
