<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $sku = 'PROD-'.strtoupper((string) Str::ulid());
        $name = fake()->unique()->words(3, true);

        $baseSlug = Str::slug($name);
        $skuSlug = Str::slug($sku);
        $maxBaseLength = 100 - 1 - \strlen($skuSlug);
        $safeBaseSlug = $maxBaseLength > 0 ? substr($baseSlug, 0, $maxBaseLength) : substr($baseSlug, 0, 1);

        // cost price (decimal 10,2)
        $costPrice = fake()->randomFloat(2, 1, 5000);

        // selling price is cost + markup (decimal 10,2)
        $markupPercent = fake()->numberBetween(0, 200); // 0% - 200%
        $sellingPrice = round($costPrice * (1 + $markupPercent / 100), 2);

        // commission is stored as decimal(4,0) — use integer percentage 0..100
        $commission = fake()->numberBetween(0, 100);

        $minimumStock = fake()->numberBetween(5, 20); // migration default is 5

        return [
            'sku' => $sku,
            'name' => $name,
            'description' => fake()->sentences(5, true),
            'slug' => "{$safeBaseSlug}-{$skuSlug}",
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'current_stock' => fake()->numberBetween(0, 100),
            'minimum_stock' => $minimumStock,
            'comission' => $commission,
            'is_active' => fake()->boolean(80),
            'product_category_id' => ProductCategory::inRandomOrder()->first()->id,
            'created_at' => Carbon::parse(now()),
            'updated_at' => Carbon::parse(now()),
        ];
    }
}
