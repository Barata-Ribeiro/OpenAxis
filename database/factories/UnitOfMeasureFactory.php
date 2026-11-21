<?php

namespace Database\Factories;

use App\Models\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UnitOfMeasure> */
class UnitOfMeasureFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('U-#####'),
            'name' => fake()->word(),
            'quantity_decimals' => 0,
        ];
    }

    public function fractional(): static
    {
        return $this->state(fn (): array => ['quantity_decimals' => 3]);
    }
}
