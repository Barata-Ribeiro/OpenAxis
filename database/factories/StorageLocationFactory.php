<?php

namespace Database\Factories;

use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StorageLocation> */
class StorageLocationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('LOC-#####'),
            'name' => fake()->words(2, true),
            'is_active' => true,
        ];
    }
}
