<?php

namespace Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $admin = User::firstOrCreate(
                ['email' => config('app.admin_email')],
                [
                    'name' => config('app.admin_name'),
                    'email' => config('app.admin_email'),
                    'password' => config('app.admin_password'),
                ]
            )->assignRole('Super Admin');

            Log::info('Admin user seeded successfully!', ['email' => $admin->email]);
        } catch (Exception $e) {
            Log::error('Error seeding users!', ['error' => $e->getMessage()]);
        }
    }
}
