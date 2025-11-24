<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sku = $this->faker->unique()->bothify('PROD-####');
        $name = $this->faker->unique()->words(3, true);

        // cost price (decimal 10,2)
        $costPrice = $this->faker->randomFloat(2, 1, 5000);

        // selling price is cost + markup (decimal 10,2)
        $markupPercent = $this->faker->numberBetween(0, 200); // 0% - 200%
        $sellingPrice = round($costPrice * (1 + $markupPercent / 100), 2);

        // commission is stored as decimal(4,0) — use integer percentage 0..100
        $commission = $this->faker->numberBetween(0, 100);

        $minimumStock = $this->faker->numberBetween(5, 20); // migration default is 5

        return [
            'sku' => $sku,
            'name' => $name,
            'description' => $this->faker->sentences(5, true),
            'slug' => Str::slug($name.'-'.$sku),
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'current_stock' => $this->faker->numberBetween(0, 100),
            'minimum_stock' => $minimumStock,
            'comission' => $commission,
            'is_active' => $this->faker->boolean(80),
            'product_category_id' => ProductCategory::inRandomOrder()->first()->id,
        ];
    }
}
