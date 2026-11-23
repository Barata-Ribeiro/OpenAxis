<?php

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;

test('a product keeps its SKU and slug and reserves the slug after archival', function () {
    $product = Product::factory()->create(['sku' => 'RI435', 'name' => 'Precision Scale']);
    $product->update(['name' => 'Updated Scale']);
    $product->delete();

    $replacement = Product::factory()->create(['name' => 'Precision Scale']);

    expect($product->slug)->toBe('precision-scale')
        ->and($replacement->slug)->toBe('precision-scale-1')
        ->and(Product::where('sku', 'RI435')->exists())->toBeFalse();

    expect(fn () => $replacement->update(['sku' => 'NEW-SKU']))->toThrow(DomainException::class);
});

test('catalog search finds exact SKUs and descriptive text', function () {
    $product = Product::factory()->create(['sku' => 'SCALE-100', 'description' => 'Lab calibration with a quartz sensor']);

    expect(Product::search(' SCALE-100 ')->sole()->is($product))->toBeTrue()
        ->and(Product::search('quartz sensor')->sole()->is($product))->toBeTrue();
});

test('a product belongs to multiple categories and resolves images in display order', function () {
    $product = Product::factory()->create();
    $parent = Category::factory()->create();
    $child = Category::factory()->for($parent, 'parent')->create();
    $product->categories()->attach([$parent->id, $child->id]);

    $second = Image::factory()->for($product, 'imageable')->create(['sort_order' => 30]);
    $first = Image::factory()->for($product, 'imageable')->create(['sort_order' => 10]);

    expect($child->parent->is($parent))->toBeTrue()
        ->and($product->categories()->pluck('categories.id')->all())->toEqualCanonicalizing([$parent->id, $child->id])
        ->and($product->images()->pluck('id')->all())->toBe([$first->id, $second->id])
        ->and($first->imageable->is($product))->toBeTrue();

    expect(fn () => $parent->update(['parent_id' => $child->id]))->toThrow(DomainException::class);
});
