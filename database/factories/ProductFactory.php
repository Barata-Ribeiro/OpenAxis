<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'unit_of_measure_id' => UnitOfMeasure::factory(),
            'sku' => fake()->unique()->bothify('SKU-????-#####'),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'cost_price_cents' => 1_000,
            'sale_price_cents' => 1_500,
            'minimum_stock_milliunits' => 2_000,
            'is_active' => true,
        ];
    }
}
