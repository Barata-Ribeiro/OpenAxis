<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(PermissionRegistrar $registrar): void
    {
        $guard = config('auth.defaults.guard', 'web');

        $registrar->forgetCachedPermissions();

        Permission::query()->upsert(
            array_map(fn (string $name): array => ['name' => $name, 'guard_name' => $guard], self::names()),
            ['name', 'guard_name'],
            ['name'],
        );

        $registrar->forgetCachedPermissions();
    }

    /** @return list<string> */
    public static function names(): array
    {
        $read = ['index', 'show'];
        $maintain = [...$read, 'create', 'edit'];
        $crud = [...$maintain, 'destroy'];
        $order = [...$maintain, 'confirm', 'complete', 'cancel'];

        $modules = [
            'dashboard' => ['show'],
            'user' => $crud,
            'role' => $crud,
            'permission' => $crud,
            'customer' => $crud,
            'supplier' => $crud,
            // Product images and order items follow the permissions of their parent.
            'product' => $crud,
            'category' => $crud,
            'unit-of-measure' => $crud,
            'storage-location' => $crud,
            'product-batch' => $maintain,
            'stock-movement' => [...$read, 'create'],
            'sale-order' => $order,
            'purchase-order' => $order,
            'financial-account' => [...$maintain, 'cancel'],
            'cash-entry' => [...$read, 'create'],
            'expense-category' => $crud,
            'payment-method' => $crud,
            'audit' => $read,
        ];

        $names = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $names[] = "$module.$action";
            }
        }

        return $names;
    }
}
