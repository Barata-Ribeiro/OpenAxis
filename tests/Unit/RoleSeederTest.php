<?php

use App\Enums\RoleEnum;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

test('the base user can only view the dashboard', function () {
    expect(RoleSeeder::permissionsFor(RoleEnum::User))->toBe(['dashboard.show']);
});

test('each ERP role can perform its duties without access to other operations', function (RoleEnum $role, array $allowed, array $denied) {
    $permissions = RoleSeeder::permissionsFor($role);

    expect(array_diff($allowed, $permissions))->toBeEmpty();
    expect(array_intersect($denied, $permissions))->toBeEmpty();
})->with([
    'sales' => [RoleEnum::Sales, ['customer.edit', 'sale-order.confirm'], ['supplier.edit', 'purchase-order.show', 'cash-entry.create']],
    'purchasing' => [RoleEnum::Purchasing, ['supplier.edit', 'purchase-order.confirm'], ['customer.edit', 'sale-order.show', 'cash-entry.create']],
    'inventory' => [RoleEnum::Inventory, ['purchase-order.show', 'sale-order.show', 'product-batch.create', 'stock-movement.create'], ['purchase-order.edit', 'sale-order.edit', 'product.edit', 'cash-entry.create']],
    'catalog manager' => [RoleEnum::CatalogManager, ['product.edit', 'category.create', 'unit-of-measure.edit'], ['stock-movement.create', 'purchase-order.create', 'sale-order.create']],
    'finance' => [RoleEnum::Finance, ['financial-account.cancel', 'cash-entry.create', 'payment-method.edit'], ['purchase-order.edit', 'sale-order.edit', 'stock-movement.create']],
]);

test('auditors can read the ERP records and audit trail', function () {
    $permissions = RoleSeeder::permissionsFor(RoleEnum::Auditor);

    expect($permissions)->toContain('audit.index', 'audit.show', 'sale-order.show', 'purchase-order.show', 'stock-movement.show', 'cash-entry.show');
    expect($permissions)->each->toMatch('/\.(index|show)$/');
});

test('administrative permissions are reserved for the super admin', function () {
    foreach (RoleEnum::cases() as $role) {
        if ($role === RoleEnum::SuperAdmin) {
            continue;
        }

        expect(RoleSeeder::permissionsFor($role))->each->not->toMatch('/^(user|role|permission)\./');
    }
});

test('the catalog contains every seeded grant and protects retained records', function () {
    $catalog = PermissionSeeder::names();

    expect($catalog)->toContain('user.destroy', 'role.edit', 'permission.index');
    expect($catalog)->toBe(array_values(array_unique($catalog)));
    expect(array_intersect($catalog, [
        'sale-order.destroy', 'purchase-order.destroy', 'product-batch.destroy', 'financial-account.destroy',
        'stock-movement.edit', 'stock-movement.destroy', 'cash-entry.edit', 'cash-entry.destroy',
    ]))->toBeEmpty();

    foreach (RoleEnum::cases() as $role) {
        expect(array_diff(RoleSeeder::permissionsFor($role), $catalog))->toBeEmpty();
    }

    expect(RoleSeeder::permissionsFor(RoleEnum::SuperAdmin))->toBe($catalog);
});
