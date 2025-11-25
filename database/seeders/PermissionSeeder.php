<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $action = [
            ['name' => 'index', 'label' => 'List %s'],
            ['name' => 'show', 'label' => 'View %s'],
            ['name' => 'create', 'label' => 'Create %s'],
            ['name' => 'edit', 'label' => 'Edit %s'],
            ['name' => 'destroy', 'label' => 'Delete %s'],
        ];

        $modules = [
            ['name' => 'user', 'label' => ['singular' => 'user', 'plural' => 'users'], 'actions' => ['all']],
            ['name' => 'role', 'label' => ['singular' => 'role', 'plural' => 'roles'], 'actions' => ['all']],
            ['name' => 'permission', 'label' => ['singular' => 'permission', 'plural' => 'permissions'], 'actions' => ['all']],
            ['name' => 'dashboard', 'label' => ['singular' => 'dashboard', 'plural' => 'dashboards'], 'actions' => ['show']],
            ['name' => 'product', 'label' => ['singular' => 'product', 'plural' => 'products'], 'actions' => ['all']],
            ['name' => 'client', 'label' => ['singular' => 'client', 'plural' => 'clients'], 'actions' => ['all']],
            ['name' => 'order', 'label' => ['singular' => 'order', 'plural' => 'orders'], 'actions' => ['all']],
            ['name' => 'sale', 'label' => ['singular' => 'sale', 'plural' => 'sales'], 'actions' => ['all']],
            ['name' => 'vendor', 'label' => ['singular' => 'vendor', 'plural' => 'vendors'], 'actions' => ['all']],
            ['name' => 'supplier', 'label' => ['singular' => 'supplier', 'plural' => 'suppliers'], 'actions' => ['all']],
            ['name' => 'supply', 'label' => ['singular' => 'supply', 'plural' => 'supplies'], 'actions' => ['all']],
            ['name' => 'finance', 'label' => ['singular' => 'finance', 'plural' => 'finances'], 'actions' => ['all']],
        ];

        try {
            $permissions = [];

            foreach ($modules as $module) {
                foreach ($action as $act) {
                    if (! \in_array('all', $module['actions']) && ! \in_array($act['name'], $module['actions'])) {
                        continue;
                    }

                    $label = \sprintf($act['label'], $module['label']['singular']);

                    if ($act['name'] === 'index') {
                        $label = \sprintf($act['label'], $module['label']['plural']);
                    }

                    $permissions[] = [
                        'name' => $module['name'].'.'.$act['name'],
                        'label' => $label,
                    ];
                }
            }

            foreach ($permissions as $perm) {
                Permission::where(['name' => $perm['name'], 'title' => $perm['label']])
                    ->existsOr(fn () => Permission::create(['name' => $perm['name'], 'title' => $perm['label']]));
            }
        } catch (Exception $e) {
            Log::error('Error seeding permissions!', ['error' => $e->getMessage()]);
        }
    }
}
