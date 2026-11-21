<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Image> */
class ImageFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'imageable_type' => (new Product)->getMorphClass(),
            'imageable_id' => Product::factory(),
            'disk' => 'public',
            'path' => fake()->uuid().'.jpg',
            'sort_order' => 0,
        ];
    }
}
