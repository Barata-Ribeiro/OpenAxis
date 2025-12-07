<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = Partner::factory()
            ->count(50)
            ->make()->map(fn ($u) => $u->getAttributes())
            ->toArray();

        Partner::insert($partners);

        Partner::all()->each(fn (Partner $pt) => $pt->addresses()->create(Address::factory()->make()->toArray()));
    }
}
