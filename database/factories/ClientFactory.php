<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['individual', 'company']);

        $isIndividual = $type === 'individual';

        return [
            'name' => $isIndividual ? fake()->unique()->name() : fake()->unique()->company(),
            'email' => $isIndividual ? fake()->unique()->safeEmail() : fake()->unique()->companyEmail(),
            'phone_number' => fake()->optional()->e164PhoneNumber(),
            'identification' => $isIndividual ? fake()->unique()->numerify('###########') : fake()->unique()->numerify('##############'),
            'client_type' => $type,
            'created_at' => Carbon::parse(now()),
            'updated_at' => Carbon::parse(now()),
        ];
    }
}
