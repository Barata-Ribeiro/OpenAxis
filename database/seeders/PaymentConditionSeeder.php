<?php

namespace Database\Seeders;

use App\Models\PaymentCondition;
use Illuminate\Database\Seeder;

class PaymentConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [
            ['name' => 'Cash',                 'days_until_due' => 0,  'installments' => 1],
            ['name' => '30 days',              'days_until_due' => 30, 'installments' => 1],
            ['name' => '2x 30 days',           'days_until_due' => 30, 'installments' => 2],
            ['name' => '3x 30 days',           'days_until_due' => 30, 'installments' => 3],
            ['name' => '45 days',              'days_until_due' => 45, 'installments' => 1],
            ['name' => '2x 45 days',           'days_until_due' => 45, 'installments' => 2],
            ['name' => '60 days',              'days_until_due' => 60, 'installments' => 1],
            ['name' => '90 days',              'days_until_due' => 90, 'installments' => 1],
        ];

        foreach ($conditions as $index => $attrs) {
            $code = sprintf('PC%03d', $index + 1); // PC001, PC002, ...
            PaymentCondition::firstOrCreate(array_merge(['code' => $code], $attrs));
        }
    }
}
