<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([PermissionSeeder::class, RoleSeeder::class, AdminSeeder::class]);

        if (App::environment('local', 'testing', 'staging')) {

            // TODO: Extract this logic to a dedicated seeder class
            $roleNames = array_values(array_filter(array_map(fn ($r) => $r->value, RoleEnum::cases()), fn ($r) => $r !== RoleEnum::SUPER_ADMIN->value));

            User::factory()->count(100)->create()->each(function ($user) use ($roleNames) {
                $user->syncRoles($roleNames[array_rand($roleNames)]);
            });

            $this->call([]); // TODO: Add seeder classes for local, testing, and staging environments
        }
    }
}
