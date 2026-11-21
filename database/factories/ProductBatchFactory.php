<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductBatch> */
class ProductBatchFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
        ];
    }
}
