<?php

namespace Database\Seeders;

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
            ['name' => 'user', 'label' => ['singular' => 'user', 'plural' => 'users']],
            ['name' => 'role', 'label' => ['singular' => 'role', 'plural' => 'roles']],
            ['name' => 'permission', 'label' => ['singular' => 'permission', 'plural' => 'permissions']],
            ['name' => 'dashboard', 'label' => ['singular' => 'dashboard', 'plural' => 'dashboards']],
            ['name' => 'product', 'label' => ['singular' => 'product', 'plural' => 'products']],
            ['name' => 'client', 'label' => ['singular' => 'client', 'plural' => 'clients']],
            ['name' => 'order', 'label' => ['singular' => 'order', 'plural' => 'orders']],
            ['name' => 'sale', 'label' => ['singular' => 'sale', 'plural' => 'sales']],
            ['name' => 'vendor', 'label' => ['singular' => 'vendor', 'plural' => 'vendors']],
            ['name' => 'supplier', 'label' => ['singular' => 'supplier', 'plural' => 'suppliers']],
            ['name' => 'supply', 'label' => ['singular' => 'supply', 'plural' => 'supplies']],
            ['name' => 'finance', 'label' => ['singular' => 'finance', 'plural' => 'finances']],
        ];

        try {
            $permissions = [];

            foreach ($modules as $module) {
                foreach ($action as $act) {
                    $label = sprintf($act['label'], $module['label']['singular']);

                    if ($act['name'] === 'index') {
                        $label = sprintf($act['label'], $module['label']['plural']);
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
        } catch (\Exception $e) {
            Log::error('Error seeding permissions!', ['error' => $e->getMessage()]);
        }
    }
}
