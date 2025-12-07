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
        $type = $this->faker->randomElement(['individual', 'company']);

        $isIndividual = $type === 'individual';

        return [
            'name' => $isIndividual ? $this->faker->name() : $this->faker->company(),
            'email' => $isIndividual ? $this->faker->unique()->safeEmail() : $this->faker->unique()->companyEmail(),
            'phone_number' => $this->faker->optional()->e164PhoneNumber(),
            'identification' => $isIndividual ? $this->faker->unique()->numerify('###########') : $this->faker->unique()->numerify('##############'),
            'client_type' => $type,
            'created_at' => Carbon::parse(now()),
            'updated_at' => Carbon::parse(now()),
        ];
    }
}
