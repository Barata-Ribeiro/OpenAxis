<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(PermissionRegistrar $registrar): void
    {
        $guard = config('auth.defaults.guard', 'web');

        $registrar->forgetCachedPermissions();

        DB::transaction(function () use ($guard): void {
            $permissions = Permission::query()->where('guard_name', $guard)->get()->keyBy('name');

            foreach (RoleEnum::cases() as $role) {
                $grants = array_map(
                    fn (string $name): Permission => $permissions->get($name)
                        ?? throw PermissionDoesNotExist::create($name, $guard),
                    self::permissionsFor($role),
                );

                $record = Role::query()->firstOrCreate(['name' => $role->value, 'guard_name' => $guard]);
                $record = Role::query()->whereKey($record->getKey())->lockForUpdate()->firstOrFail();

                if ($role === RoleEnum::SuperAdmin) {
                    $record->givePermissionTo($grants);
                } else {
                    $record->syncPermissions($grants);
                }
            }
        }, attempts: 3);

        $registrar->forgetCachedPermissions();
    }

    /** @return list<string> */
    public static function permissionsFor(RoleEnum $role): array
    {
        if ($role === RoleEnum::SuperAdmin) {
            return PermissionSeeder::names();
        }

        $read = ['index', 'show'];
        $maintain = [...$read, 'create', 'edit'];
        $order = [...$maintain, 'confirm', 'complete', 'cancel'];
        $catalog = [...$maintain, 'destroy'];

        $modules = match ($role) {
            RoleEnum::User => [],
            RoleEnum::Sales => [
                'customer' => $maintain,
                'product' => $read,
                'sale-order' => $order,
            ],
            RoleEnum::Purchasing => [
                'supplier' => $maintain,
                'product' => $read,
                'purchase-order' => $order,
            ],
            RoleEnum::Inventory => [
                'product' => $read,
                'unit-of-measure' => $read,
                'storage-location' => $maintain,
                'product-batch' => $maintain,
                'stock-movement' => [...$read, 'create'],
                'sale-order' => $read,
                'purchase-order' => $read,
            ],
            RoleEnum::CatalogManager => [
                'product' => $catalog,
                'category' => $catalog,
                'unit-of-measure' => $catalog,
                'storage-location' => $read,
            ],
            RoleEnum::Finance => [
                'customer' => $read,
                'supplier' => $read,
                'sale-order' => $read,
                'purchase-order' => $read,
                'financial-account' => [...$maintain, 'cancel'],
                'cash-entry' => [...$read, 'create'],
                'expense-category' => $maintain,
                'payment-method' => $maintain,
            ],
            RoleEnum::Auditor => [
                'customer' => $read,
                'supplier' => $read,
                'product' => $read,
                'category' => $read,
                'unit-of-measure' => $read,
                'storage-location' => $read,
                'product-batch' => $read,
                'stock-movement' => $read,
                'sale-order' => $read,
                'purchase-order' => $read,
                'financial-account' => $read,
                'cash-entry' => $read,
                'expense-category' => $read,
                'payment-method' => $read,
                'audit' => $read,
            ],
        };

        $permissions = ['dashboard.show'];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[] = "$module.$action";
            }
        }

        return $permissions;
    }
}
