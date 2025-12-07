<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::whereHas('roles', fn ($query) => $query->where('name', RoleEnum::VENDOR->value))->pluck('id')->toArray();

        $vendors = Vendor::factory()
            ->count(\count($userIds))
            ->state(fn () => ['user_id' => $userIds[array_rand($userIds)]])
            ->make()
            ->makeHidden('full_name')
            ->map(fn ($u) => $u->getAttributes())
            ->toArray();

        $vendors = array_map(function ($vendor) {
            $vendor['date_of_birth'] = Carbon::parse($vendor['date_of_birth'])->format('Y-m-d');

            return $vendor;
        }, $vendors);

        $users = User::factory()
            ->count(5)
            ->make()
            ->makeHidden('avatar')
            ->map(fn ($u) => $u->getAttributes())
            ->toArray();

        DB::transaction(function () use ($vendors, $users) {
            Vendor::insert($vendors);
            User::insert($users);
        });

        User::whereIn('email', array_column($users, 'email'))
            ->get()
            ->each(fn ($u) => $u->assignRole(RoleEnum::VENDOR->value));
    }
}
