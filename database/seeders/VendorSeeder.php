<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::whereHas('roles', fn ($query) => $query->where('name', RoleEnum::VENDOR->value))->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            Vendor::factory()
                ->state(['user_id' => $userId])
                ->create();
        }
    }
}
