<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryNames = ['Electronics', 'Computers', 'Furniture', 'Stationery', 'Home Appliances', 'Toys'];

        foreach ($categoryNames as $name) {
            ProductCategory::factory()->create(['name' => $name, 'is_active' => true]);
        }
    }
}
