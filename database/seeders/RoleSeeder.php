<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $guard = config('auth.defaults.guard', 'web');

            $allPermissions = Permission::where('guard_name', $guard)->pluck('name')->toArray();

            $modulesForRoles = $this->getModulesForRoles();

            $roles = [];
            foreach ($modulesForRoles as $roleName => $mods) {
                $rolePermissions = [];
                foreach ($mods as $module => $actions) {
                    foreach ($actions as $action) {
                        $permissionName = "$module.$action";
                        if (\in_array($permissionName, $allPermissions)) {
                            $rolePermissions[] = $permissionName;
                        } else {
                            Log::warning("Permission '$permissionName' does not exist and cannot be assigned to role '$roleName'.");
                        }
                    }
                }

                $rolePermissions = array_values(array_intersect($rolePermissions, $allPermissions));

                $roles[$roleName] = $rolePermissions;
            }

            Role::firstOrCreate(['name' => RoleEnum::SUPER_ADMIN->value, 'guard_name' => $guard]);

            foreach ($roles as $roleName => $permissions) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard])
                    ->syncPermissions(array_values(array_intersect($permissions, $allPermissions)));
            }
        } catch (Exception $e) {
            Log::error('Error seeding roles.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves an array of modules associated with different roles.
     * This method is used internally by the seeder to define the permissions
     * or access levels for various roles in the system.
     *
     * @return array An associative array where keys are role names and values are arrays of module names.
     */
    private function getModulesForRoles(): array
    {
        return [
            RoleEnum::VENDOR->value => [
                'dashboard' => ['show'],
                'product' => ['index', 'show'],
                'client' => ['index', 'show', 'create', 'edit'],
                'sale' => ['index', 'show', 'create', 'edit'],
                'vendor' => ['index', 'show'],
                'supply' => ['index', 'show'],
            ],
            RoleEnum::BUYER->value => [
                'dashboard' => ['show'],
                'product' => ['index', 'show', 'create', 'edit'],
                'order' => ['index', 'show', 'create', 'edit'],
                'supplier' => ['index', 'show', 'create', 'edit'],
                'supply' => ['index', 'show', 'create', 'edit'],
            ],
            RoleEnum::FINANCE->value => [
                'dashboard' => ['show'],
                'finance' => ['index', 'show', 'create', 'edit'],
                'sale' => ['index', 'show'],
                'order' => ['index', 'show'],
            ],
        ];
    }
}
