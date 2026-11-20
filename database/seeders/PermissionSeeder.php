<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Log;
use Spatie\Permission\PermissionRegistrar;

use function in_array;
use function sprintf;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $resourceActions = [
            ['name' => 'index', 'label' => 'List %s'],
            ['name' => 'show', 'label' => 'View %s'],
            ['name' => 'create', 'label' => 'Create %s'],
            ['name' => 'edit', 'label' => 'Edit %s'],
            ['name' => 'destroy', 'label' => 'Delete %s'],
        ];

        $resourceModules = [
            ['name' => 'user', 'label' => ['singular' => 'user', 'plural' => 'users'], 'actions' => ['all']],
            ['name' => 'role', 'label' => ['singular' => 'role', 'plural' => 'roles'], 'actions' => ['all']],
            ['name' => 'permission', 'label' => ['singular' => 'permission', 'plural' => 'permissions'], 'actions' => ['all']],
        ];

        try {
            $permissions = [];

            foreach ($resourceModules as $module) {
                foreach ($resourceActions as $act) {
                    if (! in_array('all', $module['actions']) && ! in_array($act['name'], $module['actions'])) {
                        continue;
                    }

                    $label = sprintf($act['label'], $module['label']['singular']);

                    if ($act['name'] === 'index') {
                        $label = sprintf($act['label'], $module['label']['plural']);
                    }

                    $permissions[] = [
                        'name' => $module['name'].'.'.$act['name'],
                        'title' => $label,
                        'guard_name' => config('auth.defaults.guard', 'web'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DB::table('permissions')->insertOrIgnore($permissions);

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (Exception $e) {
            Log::error('Error seeding permissions!', ['error' => $e->getMessage()]);
        }
    }
}
