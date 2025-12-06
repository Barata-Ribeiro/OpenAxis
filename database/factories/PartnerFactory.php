<?php

namespace Database\Factories;

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
        $type = $this->faker->randomElement($partnerTypes);
        $isSupplierOrBoth = \in_array($type, ['supplier', 'both']);

        return [
            'name' => $isSupplierOrBoth ? $this->faker->company() : $this->faker->name(),
            'type' => $type,
            'email' => $isSupplierOrBoth ? $this->faker->unique()->companyEmail() : $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'identification' => $isSupplierOrBoth ? $this->faker->unique()->numerify('##.###.###/####-##') : $this->faker->unique()->numerify('###.###.###-##'),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
