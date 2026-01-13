<?php

use App\Enums\RoleEnum;
use App\Enums\StockMovementTypeEnum;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use Inertia\Testing\AssertableInertia;

describe('tests for the "index" method of Product/InventoryController', function () {
    $componentName = 'erp/inventory/index';

    test('users without inventory.index permission get a 403 response', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::FINANCE->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.inventory.index'))
            ->assertForbidden();
    });

    test('authorized users can access the inventory listing', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('inventory.data')
            ->has('inventory.links')
            ->has('categories')
        );
    });

    test('inventory listing supports sorting by id', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $expectedFirstId = Product::query()->orderByDesc('id')->value('id');

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.index', [
            'per_page' => 1,
            'sort_by' => 'id',
            'sort_dir' => 'desc',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('inventory.data', 1)
            ->where('inventory.data.0.id', $expectedFirstId)
        );
    });

    test('inventory listing gracefully falls back when sort field is invalid', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $expectedFirstId = Product::query()->orderBy('id')->value('id');

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.index', [
            'per_page' => 1,
            'sort_by' => 'not_a_real_field',
            'sort_dir' => 'asc',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('inventory.data', 1)
            ->where('inventory.data.0.id', $expectedFirstId)
        );
    });

    test('inventory listing supports per_page pagination controls', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.index', [
            'per_page' => 2,
            'sort_by' => 'id',
            'sort_dir' => 'asc',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('inventory.data')
            ->where('inventory.per_page', 2)
        );
    });

    test('inventory listing supports category_name filtering', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $categoryName = ProductCategory::query()->orderBy('id')->value('name');

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.index', [
            'per_page' => 10,
            'sort_by' => 'id',
            'sort_dir' => 'asc',
            'filters' => "category_name:$categoryName",
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('inventory.data')
            ->where('inventory.data.0.category.name', $categoryName)
        );
    });
});

describe('tests for the "show" method of Product/InventoryController', function () {
    $componentName = 'erp/inventory/show';

    test('users without inventory.show permission cannot view product inventory movements', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::FINANCE->value);
        $product = Product::query()->orderBy('id')->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.inventory.show', $product))
            ->assertForbidden();
    });

    test('authorized users can view product inventory movements', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $product = Product::query()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.show', $product));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('product.id', $product->id)
            ->has('product.name')
            ->has('product.slug')
            ->has('product.current_stock')
            ->has('product.minimum_stock')
            ->has('movements.data')
            ->has('movements.links')
        );
    });
});

describe('tests for the "create" method of Product/InventoryController', function () {
    $componentName = 'erp/inventory/create';

    test('users without inventory.create permission cannot access the adjustment creation page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::FINANCE->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.inventory.create'))
            ->assertForbidden();
    });

    test('authorized users can access the adjustment creation page', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.create'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('products')
        );
    });
});

describe('tests for the "edit" method of Product/InventoryController', function () {
    $componentName = 'erp/inventory/edit';

    test('users without inventory.edit permission cannot access the edit page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::FINANCE->value);
        $product = Product::query()->orderBy('id')->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.inventory.edit', $product))
            ->assertForbidden();
    });

    test('authorized users can access the edit page', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $product = Product::query()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($authorizedUser)->get(route('erp.inventory.edit', $product));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('product.id', $product->id)
            ->has('product.name')
            ->has('product.slug')
            ->has('product.current_stock')
            ->has('product.minimum_stock')
        );
    });
});

describe('tests for the "update" method of Product/InventoryController', function () {
    test('users without inventory.edit permission cannot adjust inventory', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::FINANCE->value);
        $product = Product::query()->orderBy('id')->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->patch(route('erp.inventory.update', $product), [
                'product_id' => $product->id,
                'movement_type' => StockMovementTypeEnum::ADJUSTMENT->value,
                'quantity' => 1,
                'reason' => 'Test adjustment',
                'reference' => 'TEST-REF-001',
            ])
            ->assertForbidden();
    });

    test('authorized users get validation errors when required fields are missing', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $product = Product::query()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->from(route('erp.inventory.edit', $product))
            ->patch(route('erp.inventory.update', $product), []);

        $response->assertRedirect(route('erp.inventory.edit', $product));
        $response->assertSessionHasErrors();
    });

    test('authorized users can adjust inventory and a stock movement is created', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $product = Product::query()->orderBy('id')->firstOrFail();

        $payload = [
            'product_id' => $product->id,
            'movement_type' => StockMovementTypeEnum::ADJUSTMENT->value,
            'quantity' => 1,
            'reason' => 'Test adjustment',
            'reference' => 'TEST-REF-INV-001',
        ];

        $response = $this->actingAs($authorizedUser)->patch(route('erp.inventory.update', $product), $payload);

        $response->assertRedirect(route('erp.inventory.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'user_id' => $authorizedUser->id,
            'movement_type' => StockMovementTypeEnum::ADJUSTMENT->value,
        ]);

        expect(StockMovement::query()
            ->where('product_id', $product->id)
            ->where('user_id', $authorizedUser->id)
            ->where('movement_type', StockMovementTypeEnum::ADJUSTMENT->value)
            ->exists())->toBeTrue();
    });
});
