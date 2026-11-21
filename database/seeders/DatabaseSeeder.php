<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::withoutEvents(fn () => $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
        ]));

        $this->call(ErpReferenceSeeder::class);

        if (app()->environment('local', 'testing')) {
            $this->call(ErpDemoSeeder::class);
        }
    }
}
