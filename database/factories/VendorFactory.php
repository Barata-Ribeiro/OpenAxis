<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
            'phone_number' => fake()->phoneNumber(),
            'commission_rate' => fake()->randomFloat(2, 0, 60),
            'is_active' => fake()->boolean(),
            'created_at' => Carbon::parse(now()),
            'updated_at' => Carbon::parse(now()),
        ];
    }
}
