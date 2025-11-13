<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
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

            // TODO: Pluck permissions for syncing with roles

            foreach (RoleEnum::cases() as $role) {
                Role::firstOrCreate(['name' => $role->value, 'guard_name' => $guard]);
                // TODO: Sync permissions
            }
        } catch (Exception $e) {
            Log::error('Error seeding roles.', ['error' => $e->getMessage()]);
        }
    }
}
