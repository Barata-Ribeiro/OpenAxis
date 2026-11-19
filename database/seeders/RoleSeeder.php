<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Log;
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

            Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $guard]);
        } catch (Exception $e) {
            Log::error('Error seeding roles.', ['error' => $e->getMessage()]);
        }
    }
}
