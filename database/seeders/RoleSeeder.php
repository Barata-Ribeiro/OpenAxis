<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function in_array;

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
                        if (in_array($permissionName, $allPermissions)) {
                            $rolePermissions[] = $permissionName;
                        } else {
                            Log::warning("Permission '$permissionName' does not exist and cannot be assigned to role '$roleName'.");
                        }
                    }
                }

                $rolePermissions = array_values(array_intersect($rolePermissions, $allPermissions));

                $roles[$roleName] = $rolePermissions;
            }

            foreach ([RoleEnum::SUPER_ADMIN->value, RoleEnum::USER->value] as $roleName) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            }

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
            RoleEnum::USER->value => [
                'dashboard' => ['show'],
            ],
        ];
    }
}
