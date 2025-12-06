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

        User::withoutAuditing(function () use ($roleNames) {
            User::factory()
                ->count(100)
                ->make()
                ->chunk(20)
                ->each(function ($chunk) {
                    $rows = $chunk
                        ->values()
                        ->reject(fn ($u) => $u->email === config('app.admin_email'))
                        ->makeHidden('avatar')
                        ->map(fn ($u) => $u->getAttributes())
                        ->toArray();

                    if (! empty($rows)) {
                        User::insert($rows);
                    }
                });

            User::whereNot('email', config('app.admin_email'))
                ->get()
                ->each(fn ($u) => $u->syncRoles([$roleNames[array_rand($roleNames)]]));
        });
    }
}
