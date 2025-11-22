<?php

namespace Database\Seeders;

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

            $this->call([UserSeeder::class, ProductCategorySeeder::class]);
        }
    }
}
