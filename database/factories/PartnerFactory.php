<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partnerTypes = ['client', 'supplier', 'both'];
        $type = fake()->randomElement($partnerTypes);
        $isSupplierOrBoth = \in_array($type, ['supplier', 'both']);

        return [
            'name' => $isSupplierOrBoth ? fake()->unique()->company() : fake()->unique()->name(),
            'type' => $type,
            'email' => $isSupplierOrBoth ? fake()->unique()->companyEmail() : fake()->unique()->safeEmail(),
            'phone_number' => fake()->unique()->e164PhoneNumber(),
            'identification' => $isSupplierOrBoth ? fake()->unique()->numerify('##.###.###/####-##') : fake()->unique()->numerify('###.###.###-##'),
            'is_active' => fake()->boolean(90),
            'created_at' => Carbon::parse(now()),
            'updated_at' => Carbon::parse(now()),
        ];
    }
}
