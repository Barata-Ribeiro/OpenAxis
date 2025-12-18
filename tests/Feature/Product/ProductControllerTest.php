<?php

use App\Enums\RoleEnum;
use App\Models\Product;
use App\Models\ProductCategory;
use Faker\Generator as Faker;
use Inertia\Testing\AssertableInertia;

describe('tests for the "index" method of Product/ProductController', function () {
    $componentName = 'erp/products/index';

    test('authenticated users who are not authorized get a 403 response', function () {
        $this->actingAs(getUserWithRole(RoleEnum::FINANCE->value));

        $this->get(route('erp.products.index'))->assertForbidden();
    });

    test('authenticated authorized users can visit the product listing page', function () {
        $user = getUserWithRole(RoleEnum::VENDOR->value);

        $response = $this->actingAs($user)->get(route('erp.products.index'));

        $response->assertOk();
    });

    test('products page contains the products component', function () use ($componentName) {
        $this->actingAs(getUserWithRole(RoleEnum::VENDOR->value));

        $response = $this->get(route('erp.products.index'));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });

    test('products listing supports filtering by category_name', function () use ($componentName) {
        $category = ProductCategory::inRandomOrder()->whereIsActive(true)->first();
        Product::factory()->create(['product_category_id' => $category->id]);

        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.products.index', [
            'filters' => "category_name:{$category->name}",
        ]));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });
});

describe('tests for the "create" and "store" methods of Product/ProductController', function () {
    $componentName = 'erp/products/create';

    test('authenticated users who are not authorized get a 403 response when accessing the create page', function () {
        $this->actingAs(getUserWithRole(RoleEnum::FINANCE->value));

        $this->get(route('erp.products.create'))->assertForbidden();
    });

    test('authorized users can visit the product creation page', function () use ($componentName) {
        $this->actingAs(getUserWithRole(RoleEnum::BUYER->value));

        $response = $this->get(route('erp.products.create'));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });

    test('authorized users can create products', function () {
        $faker = app(Faker::class);

        $category = ProductCategory::inRandomOrder()->whereIsActive(true)->first();
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $payload = [
            'sku' => strtoupper($faker->bothify('SKU-###??')),
            'name' => $faker->word(),
            'description' => $faker->sentence(),
            'cost_price' => 10.50,
            'selling_price' => 15.00,
            'current_stock' => 5,
            'minimum_stock' => 1,
            'comission' => 5,
            'is_active' => true,
            'category' => $category->name,
        ];

        $this->actingAs($authorizedUser)
            ->post(route('erp.products.store'), $payload)
            ->assertRedirect(route('erp.products.index'));

        $this->assertDatabaseHas('products', [
            'sku' => $payload['sku'],
            'name' => $payload['name'],
            'product_category_id' => $category->id,
        ]);
    });
});

describe('tests for the "show", "edit" and "update" methods of Product/ProductController', function () {
    $showComponent = 'erp/products/show';
    $editComponent = 'erp/products/edit';

    test('authenticated users who are not authorized get a 403 response when viewing a product', function () {
        $product = Product::inRandomOrder()->first();

        $this->actingAs(getUserWithRole(RoleEnum::FINANCE->value));

        $this->get(route('erp.products.show', $product))->assertForbidden();
    });

    test('authorized users can view product details', function () use ($showComponent) {
        $product = Product::inRandomOrder()->first();

        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.products.show', $product));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($showComponent));
    });

    test('authenticated users who are not authorized get a 403 response when accessing the edit page', function () {
        $product = Product::inRandomOrder()->first();

        $this->actingAs(getUserWithRole(RoleEnum::FINANCE->value));

        $this->get(route('erp.products.edit', $product))->assertForbidden();
    });

    test('authorized users can visit the edit page', function () use ($editComponent) {
        $product = Product::inRandomOrder()->first();

        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.products.edit', $product));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($editComponent));
    });

    test('authorized users can update products', function () {
        $faker = app(Faker::class);

        $product = Product::inRandomOrder()->first();
        $category = ProductCategory::inRandomOrder()->whereIsActive(true)->first();

        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $payload = [
            'sku' => strtoupper($faker->bothify('SKU-###??')),
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'cost_price' => 20.00,
            'selling_price' => 25.00,
            'current_stock' => 10,
            'minimum_stock' => 2,
            'comission' => 3,
            'is_active' => false,
            'category' => $category->name,
        ];

        $this->actingAs($authorizedUser)
            ->patch(route('erp.products.update', $product), $payload)
            ->assertRedirect(route('erp.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sku' => $payload['sku'],
            'name' => $payload['name'],
            'product_category_id' => $category->id,
            'is_active' => 0,
        ]);
    });
});

describe('tests for the "destroy" and "forceDestroy" methods of Product/ProductController', function () {
    test('authenticated users who are not authorized cannot delete products', function () {
        $product = Product::inRandomOrder()->whereNull('deleted_at')->first();

        $this->actingAs(getUserWithRole(RoleEnum::FINANCE->value));

        $this->delete(route('erp.products.destroy', $product))->assertForbidden();
    });

    test('authorized users can soft delete products', function () {
        $product = Product::inRandomOrder()->whereNull('deleted_at')->first();

        $this->actingAs(getSuperAdmin())
            ->from(route('erp.products.index'))
            ->delete(route('erp.products.destroy', $product))
            ->assertRedirect(route('erp.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });

    test('authorized users can permanently delete soft deleted products', function () {
        $product = Product::inRandomOrder()->whereNull('deleted_at')->first();
        $product->delete();

        $this->actingAs(getSuperAdmin())
            ->delete(route('erp.products.force-destroy', $product))
            ->assertRedirect(route('erp.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    });
});
