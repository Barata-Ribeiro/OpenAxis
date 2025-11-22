<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleNames = array_values(array_filter(array_map(fn ($r) => $r->value, RoleEnum::cases()), fn ($r) => $r !== RoleEnum::SUPER_ADMIN->value));

        User::factory()->count(100)->create()->each(function ($user) use ($roleNames) {
            $user->syncRoles($roleNames[array_rand($roleNames)]);
        });
    }
}
