<?php

use App\Enums\RoleEnum;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;

const PRODUCT_CATEGORY_PREFIX = 'Category ';

describe('tests for the "index" method of Product/ProductCategoryController', function () {
    $componentName = 'erp/product-categories/index';

    $rolesWithProductIndex = [
        RoleEnum::VENDOR->value,
        RoleEnum::BUYER->value,
    ];

    test('authenticated users who are not authorized get a 403 response', function () {
        $this->actingAs(User::factory()->create());

        $this->get(route('erp.categories.index'))->assertForbidden();
    });

    test('authenticated authorized users can visit the product categories page', function () use ($rolesWithProductIndex) {
        $user = User::whereHas('roles', fn ($q) => $q->whereIn('name', $rolesWithProductIndex))->inRandomOrder()->first();

        $this->actingAs($user)
            ->get(route('erp.categories.index'))
            ->assertOk();
    });

    test('categories page contains the product categories component', function () use ($rolesWithProductIndex, $componentName) {
        $this->actingAs(User::whereHas('roles', fn ($q) => $q->whereIn('name', $rolesWithProductIndex))->inRandomOrder()->first());

        $response = $this->get(route('erp.categories.index'));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
                ->has('categories.data', 6)
                ->where('categories.data.0.id', ProductCategory::where('id', 1)->first()->id)
            );
    });
});

describe('tests for the "create" and "store" methods of Product/ProductCategoryController', function () {
    $componentName = 'erp/product-categories/create';

    test('authenticated users who are not authorized get a 403 response when accessing the create page', function () {
        $this->actingAs(User::factory()->create());

        $this->get(route('erp.categories.create'))->assertForbidden();
    });

    test('authorized users can visit the product category creation page', function () use ($componentName) {
        $this->actingAs(User::whereHas('roles', fn ($q) => $q->where('name', RoleEnum::BUYER->value))->inRandomOrder()->first());

        $response = $this->get(route('erp.categories.create'));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });

    test('authorized users can create product categories', function () {
        $faker = app(Faker::class);
        $authorizedUser = User::whereHas('roles', fn ($q) => $q->where('name', RoleEnum::BUYER->value))->inRandomOrder()->first();

        $payload = [
            'name' => PRODUCT_CATEGORY_PREFIX.Str::uuid(),
            'description' => $faker->sentence(),
            'is_active' => true,
        ];

        $this->actingAs($authorizedUser)
            ->post(route('erp.categories.store'), $payload)
            ->assertRedirect(route('erp.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('product_categories', [
            'name' => $payload['name'],
            'is_active' => true,
        ]);
    });
});

describe('tests for the "edit" and "update" methods of Product/ProductCategoryController', function () {
    $componentName = 'erp/product-categories/edit';

    test('authenticated users who are not authorized get a 403 response when accessing the edit page', function () {
        $category = ProductCategory::factory()->create([
            'name' => PRODUCT_CATEGORY_PREFIX.Str::uuid(),
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create());

        $this->get(route('erp.categories.edit', $category))->assertForbidden();
    });

    test('authorized users can visit the product category edit page', function () use ($componentName) {
        $category = ProductCategory::factory()->create([
            'name' => PRODUCT_CATEGORY_PREFIX.Str::uuid(),
            'is_active' => true,
        ]);

        $this->actingAs(User::whereHas('roles', fn ($q) => $q->where('name', RoleEnum::BUYER->value))->inRandomOrder()->first());

        $response = $this->get(route('erp.categories.edit', $category));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component($componentName)
                ->has('category')
                ->where('category.id', $category->id)
            );
    });

    test('authorized users can update product categories', function () {
        $faker = app(Faker::class);
        $category = ProductCategory::factory()->create([
            'name' => PRODUCT_CATEGORY_PREFIX.Str::uuid(),
            'description' => $faker->sentence(),
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'Updated '.Str::uuid(),
            'description' => $faker->sentence(),
            'is_active' => false,
        ];

        $this->actingAs(User::whereHas('roles', fn ($q) => $q->where('name', RoleEnum::BUYER->value))->inRandomOrder()->first());

        $this->patch(route('erp.categories.update', $category), $payload)
            ->assertRedirect(route('erp.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => $payload['name'],
            'description' => $payload['description'],
            'is_active' => false,
        ]);
    });
});

describe('tests for the "destroy" method of Product/ProductCategoryController', function () {
    test('authenticated users who are not authorized cannot delete product categories', function () {
        $category = ProductCategory::factory()->create([
            'name' => PRODUCT_CATEGORY_PREFIX.Str::uuid(),
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create());

        $this->delete(route('erp.categories.destroy', $category))->assertForbidden();
    });

    test('authorized users cannot delete categories with associated products', function () {
        $category = ProductCategory::factory()->create([
            'name' => PRODUCT_CATEGORY_PREFIX.Str::uuid(),
            'is_active' => true,
        ]);

        Product::factory()->create(['product_category_id' => $category->id]);

        $this->actingAs(User::factory()->create()->assignRole(RoleEnum::SUPER_ADMIN->value));

        $this->from(route('erp.categories.index'))
            ->delete(route('erp.categories.destroy', $category))
            ->assertRedirect(route('erp.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('product_categories', ['id' => $category->id]);
    });

    test('authorized users can delete categories without associated products', function () {
        $category = ProductCategory::factory()->create([
            'name' => PRODUCT_CATEGORY_PREFIX.Str::uuid(),
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create()->assignRole(RoleEnum::SUPER_ADMIN->value));

        $this->delete(route('erp.categories.destroy', $category))
            ->assertRedirect(route('erp.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);
    });
});
